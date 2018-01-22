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


namespace Mirasvit\Search\Block\Index;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Base extends Template
{
    /**
     * Base constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Collection
     *
     * @return array
     */
    public function getCollection()
    {
        return $this->getIndex()->getIndexInstance()->getSearchCollection();
    }

    /**
     * Truncate text
     *
     * @param string $string
     * @return string
     */
    public function truncate($string)
    {
        if (strlen($string) > 512) {
            $string = substr($string, 0, 512) . '...';
        }

        return $string;
    }
}
