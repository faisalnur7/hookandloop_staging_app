<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dckap\CustomFields\Controller\Express\AbstractExpress;

/**
 * Creates order on backend and prepares session to show appropriate next step in flow
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveShippingMethod extends \Magento\Paypal\Controller\Express\AbstractExpress
{
    /**
     * Update shipping method (combined action for ajax and regular request)
     *
     * @return void
     */
    public function execute()
    {   try {
            $isAjax = $this->getRequest()->getParam('isAjax');
            $this->_initCheckout();
            if(!empty($this->getRequest()->getParam('shipping_options_method') ) ){
                $this->_getQuote()->setShippingOptionsMethod($this->getRequest()->getParam('shipping_options_method'));
                $this->_getQuote()->setShippingOptionsService($this->getRequest()->getParam('shipping_options_service'));
                $this->_getQuote()->setShippingOptionsAccountNumber($this->getRequest()->getParam('shipping_options_account_number'));
                $this->_getQuote()->setShippingOptionsAccountZipCodes($this->getRequest()->getParam('shipping_options_account_zip_codes'));
                $this->_getQuote()->save();
            }
            $this->_checkout->updateShippingMethod($this->getRequest()->getParam('shipping_method'));
            if ($isAjax) {
                $this->_view->loadLayout('paypal_express_review_details', true, true, false);
                $this->getResponse()->setBody(
                    $this->_view->getLayout()->getBlock('page.block')->setQuote($this->_getQuote())->toHtml()
                );
                return;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            echo $e->getMessage(); 
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t update shipping method.')
            );
        }
        if ($isAjax) {
            $this->getResponse()->setBody(
                '<script>window.location.href = '
                . $this->_url->getUrl('*/*/review')
                . ';</script>'
            );
        } else {
            $this->_redirect('*/*/review');
        }
    }
}
