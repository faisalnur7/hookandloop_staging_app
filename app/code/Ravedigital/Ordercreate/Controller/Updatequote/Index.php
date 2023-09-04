<?php
namespace Ravedigital\Ordercreate\Controller\Updatequote;

class Index extends \Magento\Framework\App\Action\Action
{
    /** @var CheckoutSession */
    protected $checkoutSession;

    protected $resultJsonFactory;

    /**
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute() {
        /** @var \Magento\Quote\Model\Quote  */
        $quote = $this->checkoutSession->getQuote();
        $result = $this->resultJsonFactory->create();
        $sof = json_decode($this->getRequest()->getContent(), true);
        if (count($sof) && $sof['shipping_options_method'] && $sof['shipping_options_service'] && $sof['shipping_options_account_number'] && $sof['shipping_options_account_zip_codes']) {
            $quote->setShippingOptionsMethod($sof['shipping_options_method']);
            $quote->setShippingOptionsService($sof['shipping_options_service']);
            $quote->setShippingOptionsAccountNumber($sof['shipping_options_account_number']);
            $quote->setShippingOptionsAccountZipCodes($sof['shipping_options_account_zip_codes']);
            $quote->save();

            $this->checkoutSession->setShippingOptionsMethod($sof['shipping_options_method']);
            $this->checkoutSession->setShippingOptionsService($sof['shipping_options_service']);
            $this->checkoutSession->setShippingOptionsAccountNumber($sof['shipping_options_account_number']);
            $this->checkoutSession->setShippingOptionsAccountZipCodes($sof['shipping_options_account_zip_codes']);
            $result->setData(['success' => true, 'data' => $sof]);
            return $result;
        }
       
        $result->setData(['success' => false, 'data' => $sof]);
        return $result;
    }
}
