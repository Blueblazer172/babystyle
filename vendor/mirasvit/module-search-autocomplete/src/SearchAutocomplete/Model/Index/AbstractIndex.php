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


namespace Mirasvit\SearchAutocomplete\Model\Index;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\Search\Model\Index;

abstract class AbstractIndex
{
    /**
     * @var AbstractCollection
     */
    protected $collection;

    /**
     * @var Index
     */
    protected $index;

    /**
     * Set collection
     *
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Set index
     *
     * @param Index $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Collection size
     *
     * @return int
     */
    abstract public function getSize();

    /**
     * List of items
     *
     * @return array
     */
    abstract public function getItems();
}
