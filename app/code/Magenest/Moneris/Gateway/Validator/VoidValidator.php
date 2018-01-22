<?php
namespace Magenest\Moneris\Gateway\Validator;

use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class VoidValidator
 * @package Magenest\Moneris\Gateway\Validator
 */
class VoidValidator extends AbstractResponseValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);

        $errorMessages = [];
        $validationResult = $this->validateErrors($response)
            && $this->validateTransactionId($response)
            && $this->validateResponseCode($response)
            && $this->validateResponseMessage($response);

        if (!$validationResult) {
            $errorMessages = [__('Transaction has been declined. Please try again later.')];
        }

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * @param array $response
     * @return bool
     */
    protected function validateResponseCode(array $response)
    {
        return isset($response[self::RESPONSE_CODE]);
    }
}
