<?php
namespace Magenest\Moneris\Model\Ui\Direct;

use Magenest\Moneris\Block\Payment;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magenest\Moneris\Model\Adminhtml\Source\Environment;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param UrlInterface $urlBuilder
     */
    public function __construct(ConfigInterface $config, UrlInterface $urlBuilder)
    {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                Payment::MONERIS_CODE => [
                    'connectionType' => $this->config->getValue('connection_type'),
                    'cvd' => $this->config->getValue('cvd_enable'),
                    'orderCancelUrl' => $this->urlBuilder->getUrl(
                        'moneris/order/cancel',
                        ['_secure' => true]
                    ),
                    'paymentUrl' => $this->getPaymentUrl(),
                    'hppid' => $this->config->getValue('hpp_id'),
                    'hppkey' => $this->config->getValue('hpp_key'),
                    'isUSCountry' => $this->isUsCountry(),
                    'getOrderData' => $this->urlBuilder->getUrl(
                        'moneris/order/getorderdata',
                        ['_secure'=> true]
                    ),
                ]
            ]
        ];
    }

    public function getPaymentUrl()
    {
        $prefix = (bool)$this->config->getValue('sandbox_flag') ? 'sandbox_' : '';
        $after = $this->isUsCountry()  ? '_us' : '';
        $path = $prefix . 'moneris_gateway' . $after;
        $gateway = $this->config->getValue($path);
        $preload_request = 'moneris_path_preload_request'.$after;
        $additionalPath = $this->config->getValue($preload_request);
        return trim($gateway) . $additionalPath;
    }

    public function isUsCountry()
    {
        if ($this->config->getValue('environment') == Environment::ENVIRONMENT_US) {
            return true;
        }

        return false;
    }
}
