<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.26
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Model\Index\Magento\Catalog\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Mirasvit\Search\Model\Index\AbstractIndex;
use Mirasvit\Search\Model\Index\IndexerFactory;
use Mirasvit\Search\Model\Index\SearcherFactory;

class Index extends AbstractIndex
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param CategoryCollectionFactory $collectionFactory
     * @param IndexerFactory            $indexer
     * @param SearcherFactory           $searcher
     */
    public function __construct(
        CategoryCollectionFactory $collectionFactory,
        IndexerFactory $indexer,
        SearcherFactory $searcher
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($indexer, $searcher);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Category')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return __('Magento')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'magento_catalog_category';
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [
            'name'             => __('Name'),
            'description'      => __('Description'),
            'meta_title'       => __('Page Title'),
            'meta_keywords'    => __('Meta Keywords'),
            'meta_description' => __('Meta Description'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return 'entity_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildSearchCollection()
    {
        $collection = $this->collectionFactory->create()
            ->addNameToResult()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('level', ['gt' => 1]);

        if (strpos($collection->getSelect(), '`e`') > 0) {
            $this->searcher->joinMatches($collection, 'e.entity_id');
        } else {
            $this->searcher->joinMatches($collection, 'main_table.entity_id');
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableEntities($storeId, $entityIds = null, $lastEntityId = null, $limit = 100)
    {
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect(array_keys($this->getAttributes()))
            ->setStoreId($storeId)
            ->addFieldToFilter('is_active', 1);

        if ($entityIds) {
            $collection->addFieldToFilter('entity_id', ['in' => $entityIds]);
        }

        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId])
            ->setPageSize($limit)
            ->setOrder('entity_id');

        return $collection;
    }
}
