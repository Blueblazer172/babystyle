<?php
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Plugin\Aggregation\Category\DataProvider;

/**
 * Interceptor class for @see \Magento\CatalogSearch\Model\Adapter\Mysql\Plugin\Aggregation\Category\DataProvider
 */
class Interceptor extends \Magento\CatalogSearch\Model\Adapter\Mysql\Plugin\Aggregation\Category\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\App\ScopeResolverInterface $scopeResolver, \Magento\Catalog\Model\Layer\Resolver $layerResolver)
    {
        $this->___init();
        parent::__construct($resource, $scopeResolver, $layerResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function aroundGetDataSet(\Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject, \Closure $proceed, \Magento\Framework\Search\Request\BucketInterface $bucket, array $dimensions, \Magento\Framework\DB\Ddl\Table $entityIdsTable)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'aroundGetDataSet');
        if (!$pluginInfo) {
            return parent::aroundGetDataSet($subject, $proceed, $bucket, $dimensions, $entityIdsTable);
        } else {
            return $this->___callPlugins('aroundGetDataSet', func_get_args(), $pluginInfo);
        }
    }
}
