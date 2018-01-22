<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Megamenu\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\CacheInterface;

class StoreCategories extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    const CATEGORY_TREE_ID = 'CATALOG_PRODUCT_CATEGORY_TREE';
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var array
     */
    protected $categoriesTrees = [];

    protected $categoriesCollection = [];

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var DbHelper
     */
    protected $dbHelper;

    protected $list = [];

    /**
     * @var CacheInterface
     */
    private $cacheManager;

    /**
     * @param CategoryCollectionFactory               $categoryCollectionFactory
     * @param DbHelper                                $dbHelper
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        DbHelper $dbHelper,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->dbHelper = $dbHelper;
        $this->_layout  = $layout;

    }//end __construct()

    /**
     * Retrieve cache interface
     *
     * @return CacheInterface
     * @deprecated
     */
    private function getCacheManager()
    {
        if (!$this->cacheManager) {
            $this->cacheManager = ObjectManager::getInstance()
                ->get(CacheInterface::class);
        }
        return $this->cacheManager;
    }

    /**
     * Retrieve categories tree
     *
     * @param  string|null $filter
     * @return array
     */
    public function getCategoriesTree($filter = null)
    {
        if (isset($this->categoriesTrees[$filter])) {
            return $this->categoriesTrees[$filter];
        }

        // @var $matchingNamesCollection \Magento\Catalog\Model\ResourceModel\Category\Collection
        $matchingNamesCollection = $this->categoryCollectionFactory->create();

        if ($filter !== null) {
            $matchingNamesCollection->addAttributeToFilter(
                'name',
                ['like' => $this->dbHelper->addLikeEscape($filter, ['position' => 'any'])]
            );
        }

        $matchingNamesCollection->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID]);

        $shownCategoriesIds = [];

        /*
            * @var \Magento\Catalog\Model\Category $category
        */
        foreach ($matchingNamesCollection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        // @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownCategoriesIds)])
            ->addAttributeToSelect(['name', 'is_active', 'parent_id']);

        $categoryById = [
                         CategoryModel::TREE_ROOT_ID => [
                                                         'value'    => CategoryModel::TREE_ROOT_ID,
                                                         'children' => null,
                                                        ],
                        ];

        foreach ($collection as $category) {
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId];
                }
            }
            $categoryById[$category->getId()]['is_active']        = $category->getIsActive();
            $categoryById[$category->getId()]['label']            = str_replace("'", " ", $category->getName());
            $categoryById[$category->getId()]['name']             = str_replace("'", " ", $category->getName());
            $categoryById[$category->getParentId()]['children'][] = &$categoryById[$category->getId()];
            $categoryById[$category->getId()]['level']            = $category->getLevel();
        }

        $this->categoriesTrees[$filter] = $categoryById[CategoryModel::TREE_ROOT_ID]['children'];
        return $this->categoriesTrees[$filter];

    }//end getCategoriesTree()


    public function getCategoriesCollection($menuCatgories = [], $filter = null)
    {

        $categoryTree = $this->getCacheManager()->load(self::CATEGORY_TREE_ID . '_' . $filter);
        if ($categoryTree) {
            return unserialize($categoryTree);
        }

        if (isset($this->categoriesCollection[$filter])) {
            return $this->categoriesCollection[$filter];
        }

        // @var $matchingNamesCollection \Magento\Catalog\Model\ResourceModel\Category\Collection
        $matchingNamesCollection = $this->categoryCollectionFactory->create();

        if ($filter !== null) {
            $matchingNamesCollection->addAttributeToFilter(
                'name',
                ['like' => $this->dbHelper->addLikeEscape($filter, ['position' => 'any'])]
            );
        }

        $matchingNamesCollection->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID]);


        if (!empty($menuCatgories)) {
            $matchingNamesCollection->addAttributeToFilter('entity_id', ['in' => $menuCatgories]);
        } 

        $shownCategoriesIds = [];

        /*
            * @var \Magento\Catalog\Model\Category $category
        */
        foreach ($matchingNamesCollection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        // @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownCategoriesIds)])
            ->addAttributeToSelect(['name', 'is_active', 'parent_id']);

        $categoryById = [
                         CategoryModel::TREE_ROOT_ID => [
                                                         'value'    => CategoryModel::TREE_ROOT_ID,
                                                         'children' => null,
                                                        ],
                        ];

        foreach ($collection as $category) {
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId];
                }
            }
            $categoryById[$category->getId()]['category']['url']  = $category->getUrl();
            $categoryById[$category->getId()]['category']['name'] = $category->getName();
            $categoryById[$category->getParentId()]['children'][] = &$categoryById[$category->getId()];
        }

        $this->categoriesCollection[$filter] = $categoryById[CategoryModel::TREE_ROOT_ID]['children'];


        $this->getCacheManager()->save(
            serialize($categoryById[CategoryModel::TREE_ROOT_ID]['children']),
            self::CATEGORY_TREE_ID . '_' . $filter,
            [
                \Magento\Catalog\Model\Category::CACHE_TAG,
                \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
            ]
        );

        return $this->categoriesCollection[$filter];

    }//end getCategoriesTree()


    public function generatCategory($category) {
        $this->list[] = $category;
        if (isset($category['children']) && $category['children']) {
            foreach ($category['children'] as $cat) {
                $this->generatCategory($cat);
            }
        }
    }

    public function getCategoryList() {
        $categoriesTrees = $this->getCategoriesTree();
        foreach ($categoriesTrees as $category) {
            $this->generatCategory($category);
        }
        $list = $this->list; 
        foreach ($list as $k => &$category) {
            if ($category['level'] == 0) {
                unset($list[$k]);
            }
            $category['level'] -= 1;

            $categoryLabel = '';

            if ($category['level']!=0) {
                $categoryLabel = '| ';
            }

            $categoryLabel .= $this->_getSpaces($category['level']) . '(ID:' . $category['value'] . ') ' . $category['label'];
            $category['label'] = $categoryLabel;
        } 
        return $list;
    }

    protected function _getSpaces($n)
    {
        $s = '';
        for($i = 0; $i < $n; $i++) {
            $s .= '_ _ ';
        }
        return $s;
    }


}//end class
