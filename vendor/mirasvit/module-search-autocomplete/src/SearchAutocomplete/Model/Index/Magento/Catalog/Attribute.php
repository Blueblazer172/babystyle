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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.0.49
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchAutocomplete\Model\Index\Magento\Catalog;

use Mirasvit\SearchAutocomplete\Model\Index\AbstractIndex;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory as CatalogCategoryFactory;
use Magento\Framework\UrlFactory;

class Attribute extends AbstractIndex
{
    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * Query constructor.
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        UrlFactory $urlFactory
    ) {
        $this->urlFactory = $urlFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->collection->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];

        $attr = $this->index->getProperty('attribute');

        foreach ($this->collection as $value) {
            $url = $this->urlFactory->create()
                ->getUrl('catalogsearch/advanced/result', ['_query' => [$attr => $value->getValue()]]);

            $items[] = [
                'title' => $value->getLabel(),
                'url'   => $url,
            ];
        }

        return $items;
    }
}
