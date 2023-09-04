<?php
/**
 * Ravedigital_Ordercreate
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2016 Avalara, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Ravedigital\Ordercreate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Class PaypalExpressOrderSuccess
 */
class PaypalExpressOrderSuccess implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        //$quote = $observer->getEvent()->getQuote();

        //Session set in Ravedigital\Ordercreate\Controller\Updatequote\Index
        $som = $this->checkoutSession->getShippingOptionsMethod();
        $sos = $this->checkoutSession->getShippingOptionsService();
        $soan = $this->checkoutSession->getShippingOptionsAccountNumber();
        $soazc = $this->checkoutSession->getShippingOptionsAccountZipCodes();
        if ($som && $sos && $soan && $soazc) {
            if (!$order->getShippingOptionsMethod() && !$order->getShippingOptionsAccountNumber()) {
                $order->setShippingOptionsMethod($som);
                $order->setShippingOptionsService($sos);
                $order->setShippingOptionsAccountNumber($soan);
                $order->setShippingOptionsAccountZipCodes($soazc);
                $order->save();
            }

            //Session set in Ravedigital\Ordercreate\Controller\Updatequote\Index
            $this->checkoutSession->unsShippingOptionsMethod();
            $this->checkoutSession->unsShippingOptionsService();
            $this->checkoutSession->unsShippingOptionsAccountNumber();
            $this->checkoutSession->unsShippingOptionsAccountZipCodes();
        }

        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ravi.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('paypal_express_place_order_success called, som:'. $som);*/
    }
}
