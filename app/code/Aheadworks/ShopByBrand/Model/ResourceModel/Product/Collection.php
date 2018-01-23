<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\ShopByBrand\Model\ResourceModel\Product;

use Aheadworks\ShopByBrand\Api\Data\BrandInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\App\ObjectManager;

/**
 * Class Collection
 * @package Aheadworks\ShopByBrand\Model\ResourceModel\Product
 */
class Collection extends ProductCollection
{
    /**
     * @var bool|string
     */
    private $linkField;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Add brand filter
     *
     * @param BrandInterface $brand
     * @return $this
     */
    public function addBrandFilter($brand)
    {
        $select = $this->getProductOptionSelect($brand->getAttributeCode(), $this->getStoreId());

        $this->getSelect()
            ->join(
                ['eav_index_table_brand' => $select],
                implode(
                    ' AND ',
                    [
                        'eav_index_table_brand.entity_id = e.entity_id',
                        'eav_index_table_brand.option_id = ' . $brand->getOptionId()
                    ]
                ),
                []
            );
        return $this;
    }

    /**
     * Add sorting by bestsellers
     *
     * @return $this
     */
    public function addSortingByBestsellers()
    {
        $qtyOrderedSelect = $this->getConnection()->select();
        $qtyOrderedSelect
            ->from(
                ['order_item_table' => $this->getTable('sales_order_item')],
                ['qty_ordered' => new \Zend_Db_Expr('SUM(order_item_table.qty_ordered)'), 'product_id']
            )
            ->group('order_item_table.product_id');
        $this->getSelect()
            ->joinLeft(
                ['qty_ordered_select' => $qtyOrderedSelect],
                'e.entity_id = qty_ordered_select.product_id',
                []
            )
            ->group('e.entity_id')
            ->order('SUM(qty_ordered_select.qty_ordered) DESC');
        return $this;
    }

    /**
     * Add sorting by newest
     *
     * @return $this
     */
    public function addSortingByNewest()
    {
        $this->getSelect()->order('e.updated_at DESC');
        return $this;
    }

    /**
     * Add sorting by price ascending
     *
     * @return $this
     */
    public function addSortingByPriceAsc()
    {
        $this->addOrder('price', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Add sorting by price descending
     *
     * @return $this
     */
    public function addSortingByPriceDesc()
    {
        $this->addOrder('price');
        return $this;
    }

    /**
     * Add random sorting
     *
     * @return $this
     */
    public function addRandomSorting()
    {
        $this->getSelect()->orderRand('e.entity_id');
        return $this;
    }

    /**
     * Product option sub query
     *
     * @param string $attributeCode
     * @param int $storeId
     * @return Select
     */
    private function getProductOptionSelect($attributeCode, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['attribute_table' => $this->getTable('eav_attribute')], [])
            ->columns(
                ['entity_id' => 'e.entity_id']
            )->join(
                ['eav_index_table' => $this->getTable('catalog_product_index_eav')],
                implode(
                    ' AND ',
                    [
                        'attribute_table.attribute_id = eav_index_table.attribute_id',
                        'eav_index_table.store_id = ' . $storeId
                    ]
                ),
                []
            )->join(
                ['e' => $this->getTable('catalog_product_entity')],
                'e.entity_id = eav_index_table.entity_id',
                []
            );

        if ($this->hasSourceIdInCatalogIndex()) {
            $entityLinkField = 'entity_id';
            $select
                ->joinLeft(
                    ['eav_parent_index_table' => $this->getTable('catalog_product_index_eav')],
                    implode(
                        ' AND ',
                        [
                            'attribute_table.attribute_id = eav_parent_index_table.attribute_id',
                            'eav_parent_index_table.store_id = ' . $storeId,
                            'eav_parent_index_table.entity_id = e.entity_id',
                            'eav_parent_index_table.source_id = e.entity_id'
                        ]
                    ),
                    []
                );
        } else {
            /* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            $attribute = $this->getAttribute($attributeCode);
            $attValueTableName = $attribute->getBackendTable();
            $entityLinkField = $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField();

            $select
                ->joinLeft(
                    ['eav_parent_index_table' => $attValueTableName],
                    implode(
                        ' AND ',
                        [
                            'attribute_table.attribute_id = eav_parent_index_table.attribute_id',
                            'eav_parent_index_table.' . $entityLinkField . ' = e.' . $entityLinkField,
                        ]
                    ),
                    []
                );
        }

        $select
            ->columns(
                new \Zend_Db_Expr(
                    'CASE 
                        WHEN (e.type_id = "configurable" AND !ISNULL(eav_parent_index_table.' . $entityLinkField . ')) 
                            OR e.type_id = "grouped" OR e.type_id = "bundle" THEN eav_parent_index_table.value
                        WHEN (e.type_id = "configurable" AND ISNULL(eav_parent_index_table.' . $entityLinkField . ')) 
                            OR e.type_id = "simple" THEN eav_index_table.value
                        ELSE null
                    END AS "option_id"'
                )
            )
            ->where('attribute_table.attribute_code = ?', $attributeCode)
            ->group('eav_index_table.entity_id');
        return $select;
    }

    /**
     * Check if the product index has the source_id field
     *
     * @return bool
     */
    private function hasSourceIdInCatalogIndex()
    {
        if (version_compare($this->getProductMetadata()->getVersion(), '2.1.9', '>=')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve ProductMetadataInterface instance
     *
     * @return ProductMetadataInterface
     */
    private function getProductMetadata()
    {
        if (!$this->productMetadata) {
            $this->productMetadata = ObjectManager::getInstance()->get(ProductMetadataInterface::class);
        }
        return $this->productMetadata;
    }

    /**
     * Retrieve link field and cache it
     *
     * @return bool|string
     * @throws \Exception
     */
    private function getLinkField()
    {
        if ($this->linkField === null) {
            $this->linkField = $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField();
        }
        return $this->linkField;
    }

    /**
     * Retrieve MetadataPool instance
     *
     * @return MetadataPool
     */
    private function getMetadataPool()
    {
        if (!$this->metadataPool) {
            $this->metadataPool = ObjectManager::getInstance()->get(MetadataPool::class);
        }
        return $this->metadataPool;
    }
}
