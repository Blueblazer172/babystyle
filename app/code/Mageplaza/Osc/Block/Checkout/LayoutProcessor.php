<?php
namespace Mageplaza\Osc\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Block\Checkout\AttributeMerger;

class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper            $attributeMapper
     * @param AttributeMerger                                       $merger
     */
    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        AttributeMerger $merger
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper               = $attributeMapper;
        $this->merger                        = $merger;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        /*Load payment layout*/
        if (isset($jsLayout['components']['payment']['children'])) {
            if (!isset($jsLayout['components']['payment']['children']['payments-list']['children'])) {
                $jsLayout['components']['payment']['children']['payments-list']['children'] = [];
            }

            $jsLayout['components']['payment']['children']['payments-list']['children'] =
                array_merge_recursive(
                    $jsLayout['components']['payment']['children']['payments-list']['children'],
                    $this->processPaymentConfiguration(
                        $jsLayout['components']['payment']['children']['renders']['children']
                    )
                );
        }

        return $jsLayout;
    }

    /**
     * Inject billing address component into every payment component
     *
     * @param array $configuration list of payment components
     * @param array $elements attributes that must be displayed in address form
     * @return array
     */
    private function processPaymentConfiguration(array &$configuration, array $elements = array())
    {
        $output = [];
        foreach ($configuration as $paymentGroup => $groupConfig) {
            foreach ($groupConfig['methods'] as $paymentCode => $paymentComponent) {
                if (empty($paymentComponent['isBillingAddressRequired'])) {
                    continue;
                }
                $output[$paymentCode . '-form'] = [
                    'component'       => 'Magento_Checkout/js/view/billing-address',
                    'displayArea'     => 'billing-address-form-' . $paymentCode,
                    'provider'        => 'checkoutProvider',
                    'deps'            => 'checkoutProvider',
                    'dataScopePrefix' => 'billingAddress' . $paymentCode,
                    'sortOrder'       => 1,
                ];
            }
            unset($configuration[$paymentGroup]['methods']);
        }

        return $output;
    }
}
