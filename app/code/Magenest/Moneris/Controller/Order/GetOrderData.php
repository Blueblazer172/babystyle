<?php

namespace Magenest\Moneris\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\ResultInterface;

class GetOrderData extends Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        Session $checkoutSession,
        SessionManager $sessionManager
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->checkoutSession = $checkoutSession;
        $this->sessionManager = $sessionManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $orderId = $this->checkoutSession->getData('last_order_id');

        try {
            $order = $this->orderRepository->get((int) $orderId);
            $orderIncrementId = $order->getIncrementId();
            $amount = number_format($order->getTotalDue(), 2, '.', '');
            $controllerResult->setData([
                'amount' => $amount,
                'orderId' => $orderIncrementId
            ]);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            return $this->getErrorResponse($controllerResult);
        }

        return $controllerResult;
    }

    /**
     * @param ResultInterface $controllerResult
     * @return ResultInterface
     */
    private function getErrorResponse(ResultInterface $controllerResult)
    {
        $controllerResult->setHttpResponseCode(Exception::HTTP_BAD_REQUEST);
        $controllerResult->setData(['message' => __('Sorry, but something went wrong')]);

        return $controllerResult;
    }
}
