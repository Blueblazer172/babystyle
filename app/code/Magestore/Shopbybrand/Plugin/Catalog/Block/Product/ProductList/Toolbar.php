<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: zero
 * Date: 30/03/2016
 * Time: 09:23
 */

namespace Magestore\Shopbybrand\Plugin\Catalog\Block\Product\ProductList;


class Toolbar
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(\Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     * @param \Magento\Framework\Data\Collection $collection
     * @return array
     */
    public function beforeSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        $collection
    ){
		$brand = $this->_coreRegistry->registry('current_brand');
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
		//echo $request->getFullActionName();//die('testing');
		//echo '*****'.empty($_GET['cat']).'*****';die;
		if($request->getFullActionName()=='brand_index_viewbrand'){
			if(empty($_GET['cat'])){
				$collection->addAttributeToFilter('entity_id', ['in' => $brand->getArrayProductIds()]);	
			}
				
				
		}
		
		
		if (count($collection->getItems()) > 0) {
		   return [$collection];
		}
        /** @var \Magestore\Shopbybrand\Model\Brand $brand */
        $brand = $this->_coreRegistry->registry('current_brand');
        if ($brand) {
            $dir = strtoupper($toolbar->getCurrentDirection());
            $order = $toolbar->getCurrentOrder();
            $collection->addAttributeToFilter('entity_id', ['in' => $brand->getArrayProductIds()]);
            if ($order == null || $order == 'position') {
                $collection
                    ->getSelect()
                    ->joinLeft(
                        ['ms_brand_products' => $collection->getTable('ms_brand_products')],
                        "e.entity_id = ms_brand_products.product_id",
                        [
                            'position' => 'ms_brand_products.position',
                        ]
                    )
                    ->order('ms_brand_products.position ' . $dir);
                $collection->getSize();
                $this->_coreRegistry->register('is_join_position', '1');
            }
        }
        return [$collection];
    }
}