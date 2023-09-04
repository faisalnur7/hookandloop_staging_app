<?php

namespace Ravedigital\M2ePro\Observer;

use Magento\Framework\Event\ObserverInterface;

class NewOrder implements ObserverInterface {

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Get extension configuration helper
     * @var \Ravedigital_M2ePro\Helper\Config
     */
    public $dataHelper;
    
    /**
     * Pricing Helper
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    public $curHelper;
    
    /**
     * Sales order Model
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    protected $_addressConfig;


    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Ravedigital\M2ePro\Helper\Config $dataHelper
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order $order,
        \Ravedigital\M2ePro\Helper\Data $dataHelper,
        \Magento\Framework\Pricing\Helper\Data $curHelper,
        \Magento\Customer\Model\Address\Config $addressConfig
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->order = $order;
        $this->dataHelper = $dataHelper;
        $this->curHelper = $curHelper;
        $this->_addressConfig = $addressConfig;
    }

    protected function _sendEmail($from, $to, $templateId, $vars, $store, $area = \Magento\Framework\App\Area::AREA_FRONTEND)
    {
        $this->inlineTranslation->suspend();
        $this->_transportBuilder
        ->setTemplateIdentifier($templateId)
        ->setTemplateOptions([
            'area' => $area,
            'store' => $store->getId()
        ])
        ->setTemplateVars($vars)
        ->setFrom($from)
        ->addTo($to['email'], $to['name']);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        
        $this->inlineTranslation->resume();
        
        return true;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isNewOrderEnable = $this->dataHelper->getConfigVal('newordersection/generalgroup/enabled');
        $order = $observer->getEvent()->getOrder();
        if ($isNewOrderEnable && $order->getShippingMethod() === 'm2eproshipping_m2eproshipping') {
            $orderId = $order->getEntityId();
            $orderIncId = $order->getIncrementId();
            $payment = $order->getPayment();
            $payment_additional_info = $payment->getAdditionalInformation();
            $order = $this->order->load($orderId);
            $shipping_address = $order->getShippingAddress();
            $billing_address = $order->getBillingAddress();
            $addressrenderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
            $payment_additional_info =  $payment_additional_info['method_title'].'<br>'.$payment_additional_info['component_mode'].' Order ID: <b>'.$payment_additional_info['channel_order_id'].'<b>';
                    // Set email config options
            $store = $this->_storeManager->getStore();
            $from = $this->dataHelper->getConfigVal('newordersection/generalgroup/send_from');
            $to = [
                'email' => $this->dataHelper->getConfigVal('newordersection/generalgroup/email'),
                'name' => 'Administrator'
            ];
            $templateId = $this->dataHelper->getConfigVal('newordersection/generalgroup/template');
            
                    // Set email template variables
            $vars = [
                'order_id' => "#".$orderIncId,
                'order'=>$order,
                'entity_id'=>$orderId,
                'order.shipping_description'=>$order->getShippingDescription(),
                'formattedShippingAddress' =>$addressrenderer->renderArray($shipping_address),
                'formattedBillingAddress'=>$addressrenderer->renderArray($billing_address),
                'orderNumber' => $order->getIncrementId(),
                'payment_html' => $payment_additional_info,
                'customer_name' => $order->getCustomerName(),
                'items'=> $order->getAllItems(),
            ];
            // Call send email function with the necessary parameters
            if(!empty($orderId)){
                $this->_sendEmail($from, $to, $templateId, $vars, $store);
            }
        }
    }
}
