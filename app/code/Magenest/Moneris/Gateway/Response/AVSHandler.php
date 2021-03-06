<?php
namespace Magenest\Moneris\Gateway\Response;

use Magenest\Moneris\Model\Adminhtml\Source\OrderHandlerAction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Model\Config;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class AVSHandler implements HandlerInterface
{
    const XML_PATH_AVS_ENABLE = 'avs_enable';
    const XML_PATH_AVS_STREET_FAIL_ZIP_FAIL = 'avs_street_fail_zip_fail';
    const XML_PATH_AVS_STREET_FAIL_ZIP_PASS = 'avs_street_fail_zip_pass';
    const XML_PATH_AVS_STREET_PASS_ZIP_FAIL = 'avs_street_pass_zip_fail';
    const XML_PATH_AVS_STREET_ZIP_NULL = 'avs_street_zip_null';

    const STREET_FAIL_ZIP_FAIL_CODE = ['N'];
    const STREET_FAIL_ZIP_PASS_CODE = ['P','Z','T','W'];
    const STREET_PASS_ZIP_FAIL_CODE = ['A','B'];
    const STREET_ZIP_NULL = ['S','A','B','R','S','U'];
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * AVSHandler constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        if ($this->config->getValue(self::XML_PATH_AVS_ENABLE)) {
            if (isset($response['AvsResultCode']) && $response['AvsResultCode'] != 'null') {
                //AVS was Verified
                $payment->setAdditionalInformation(
                    'avs_response_code',
                    $response['AvsResultCode']
                );

                //STREET AND ZIP FAIL
                if (in_array($response['AvsResultCode'], self::STREET_FAIL_ZIP_FAIL_CODE)) {
                    $this->doOrderAction($this->config->getValue(self::XML_PATH_AVS_STREET_FAIL_ZIP_FAIL), $payment);
                    return;
                }

                //STREET FAIL AND ZIP PASS
                if (in_array($response['AvsResultCode'], self::STREET_FAIL_ZIP_PASS_CODE)) {
                    $this->doOrderAction($this->config->getValue(self::XML_PATH_AVS_STREET_FAIL_ZIP_PASS), $payment);
                    return;
                }

                //STREET PASS AND ZIP FAIL
                if (in_array($response['AvsResultCode'], self::STREET_PASS_ZIP_FAIL_CODE)) {
                    $this->doOrderAction($this->config->getValue(self::XML_PATH_AVS_STREET_PASS_ZIP_FAIL), $payment);
                    return;
                }

                //STREET AND ZIP NULL
                if (in_array($response['AvsResultCode'], self::STREET_ZIP_NULL)) {
                    $this->doOrderAction($this->config->getValue(self::XML_PATH_AVS_STREET_ZIP_NULL), $payment);
                    return;
                }
            } else {
                //AVS was NOT Verified
                $payment->setAdditionalInformation(
                    'avs_response_code',
                    'null'
                );
                $this->doOrderAction($this->config->getValue(self::XML_PATH_AVS_STREET_ZIP_NULL), $payment);
                return;
            }
        }
    }

    /**
     * @param string $action
     * @param Payment $payment
     */
    private function doOrderAction($action, $payment)
    {
        switch ($action) {
            case OrderHandlerAction::ORDER_ACTION_CANCEL:
                $payment->setIsTransactionClosed(true);
                $payment->setAdditionalInformation('order_action', OrderHandlerAction::ORDER_ACTION_CANCEL);
                $payment->setAdditionalInformation('order_action_handler_code', OrderHandlerAction::ORDER_ACTION_AVS_HANDLER);
                return;
            case OrderHandlerAction::ORDER_ACTION_HOLD:
                $payment->setIsTransactionClosed(false);
                $payment->setAdditionalInformation('order_action', OrderHandlerAction::ORDER_ACTION_HOLD);
                $payment->setAdditionalInformation('order_action_handler_code', OrderHandlerAction::ORDER_ACTION_AVS_HANDLER);
                return;
            default:
                return;
        }
    }
}
