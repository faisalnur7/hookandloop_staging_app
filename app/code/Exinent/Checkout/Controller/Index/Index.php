<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Exinent\Checkout\Controller\Index;

class Index extends \Magento\Checkout\Controller\Index\Index
{
    /**
     * Checkout page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
        /** @var \Magento\Checkout\Helper\Data $checkoutHelper */

        $checkoutHelper = $this->_objectManager->get(\Magento\Checkout\Helper\Data::class);
        if (!$checkoutHelper->canOnepageCheckout()) {
            $this->messageManager->addError(__('One-page checkout is turned off.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        $dupOrder = $this->getDuplicateOrder();
        if($dupOrder) {
            $this->getOnepage()->duplicateOrder($dupOrder);
            return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');

        }
        if (!$this->_customerSession->isLoggedIn() && !$checkoutHelper->isAllowedGuestCheckout($quote)) {
            $this->messageManager->addError(__('Guest checkout is disabled.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $this->_customerSession->regenerateId();
        $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Checkout'));
        return $resultPage;
    }

    public function getDuplicateOrder()
    {
        $quote = $this->getOnepage()->getQuote();
        $quoteId = $quote->getId();

        $order = $this->_objectManager->create('Magento\Sales\Model\Order')
        ->getCollection()
        ->addFieldToFilter('quote_id', $quoteId)
        ->getFirstItem();
        if($order->getId() && $quote->getIsActive()) {
            return $this->_objectManager->create('Magento\Sales\Model\Order')->load($order->getId());
        }
        return false;

    }
}
