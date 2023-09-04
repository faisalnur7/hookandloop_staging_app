<?php

namespace Ravedigital\Custom\Controller\Account;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Phrase;
use Magento\Framework\Validator\EmailAddress;
use Zend_Validate;

/**
 * ForgotPasswordPost controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
      /**
       * Forgot customer password action
       *
       * @return \Magento\Framework\Controller\Result\Redirect
       */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $email = (string)$this->getRequest()->getPost('email');
        if ($email) {
            // if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
                $emailValidator = new \Laminas\Validator\EmailAddress(); //adding this line to 2.4.6 
                if (!$emailValidator->isValid($email)) { //adding this line to 2.4.6
                $this->session->setForgottenEmail($email);
                $this->messageManager->addErrorMessage(
                    __('The email address is incorrect. Verify the email address and try again.')
                );
                return $resultRedirect->setPath('*/*/forgotpassword');
            }

            /* Check customer email exist in our system or not */
            if ($this->customerAccountManagement->isEmailAvailable($email, 1)) {
                $this->messageManager->addErrorMessage($this->getErrorMessage($email));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $this->customerAccountManagement->initiatePasswordReset(
                    $email,
                    AccountManagement::EMAIL_RESET
                );
            } catch (NoSuchEntityException $exception) {
                // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
            } catch (SecurityViolationException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
                return $resultRedirect->setPath('*/*/forgotpassword');
            } catch (Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We\'re unable to send the password reset email.')
                );
                return $resultRedirect->setPath('*/*/forgotpassword');
            }
            $this->messageManager->addSuccessMessage($this->getSuccessMessage($email));
            return $resultRedirect->setPath('*/*/');
        } else {
            $this->messageManager->addErrorMessage(__('Please enter your email.'));
            return $resultRedirect->setPath('*/*/forgotpassword');
        }
    }

    /**
     * Retrieve success message
     *
     * @param string $email
     * @return Phrase
     */
    protected function getSuccessMessage($email)
    {
        return __(
            'There is an account associated with %1 you will receive an email with a link to reset your password.',
            $this->escaper->escapeHtml($email)
        );
    }

    /**
     * Retrieve success message
     *
     * @param string $email
     * @return Phrase
     */
    protected function getErrorMessage($email)
    {
        return __(
            'There is no account associated with %1.',
            $this->escaper->escapeHtml($email)
        );
    }
}
