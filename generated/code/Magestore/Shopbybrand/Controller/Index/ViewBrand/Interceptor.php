<?php
namespace Magestore\Shopbybrand\Controller\Index\ViewBrand;

/**
 * Interceptor class for @see \Magestore\Shopbybrand\Controller\Index\ViewBrand
 */
class Interceptor extends \Magestore\Shopbybrand\Controller\Index\ViewBrand implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Magento\Framework\Registry $registry, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magestore\Shopbybrand\Helper\Data $helper, \Magestore\Shopbybrand\Model\SystemConfig $systemConfig, \Magestore\Shopbybrand\Model\Brand $brandFactory, \Magento\Catalog\Model\Layer\Resolver $layerResolver)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $registry, $storeManager, $helper, $systemConfig, $brandFactory, $layerResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function _initBrand()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '_initBrand');
        if (!$pluginInfo) {
            return parent::_initBrand();
        } else {
            return $this->___callPlugins('_initBrand', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        if (!$pluginInfo) {
            return parent::getActionFlag();
        } else {
            return $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        if (!$pluginInfo) {
            return parent::getRequest();
        } else {
            return $this->___callPlugins('getRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        if (!$pluginInfo) {
            return parent::getResponse();
        } else {
            return $this->___callPlugins('getResponse', func_get_args(), $pluginInfo);
        }
    }
}
