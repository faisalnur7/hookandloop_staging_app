<?php

namespace Exinent\CustomStrap\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;
class AddProduct extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(Context $context, Json $serializer = null ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context);
    }

    public function execute() {
        $sku = $this->getRequest()->getPost('sku');
        $qty = $this->getRequest()->getPost('qty');
        $price = $this->getRequest()->getPost('price');
        $strapLengthOrignal = $this->getRequest()->getPost('strapLength');
        $strapLength = ceil($strapLengthOrignal);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $productRepository = $objectManager->get('Magento\Catalog\Model\Product');

        $product = $productRepository->loadByAttribute('sku', $sku);
        $product_id = $product->getId();
        $options = array(
            array(
                'label' => 'Width',
                'value' => $product->getResource()->getAttribute('configuratble_width')->getFrontend()->getValue($product),
            ),
            array(
                'label' => 'Color',
                'value' => $product->getResource()->getAttribute('colors_available')->getFrontend()->getValue($product),
            ),
            array(
                'label' => 'Custom Strap Length',
                'value' => $strapLengthOrignal,
            )

        );
        $skuFound = false;
        $prdoduct = $objectManager->get('Magento\Catalog\Model\Product')->load($product_id);

        $cart = $objectManager->create('Magento\Checkout\Model\Cart');
        $cartQuoteItems = $cart->getQuote()->getAllItems();

        foreach ($cartQuoteItems as $cartQuoteItem) {

            if($cartQuoteItem->getSku()==$sku){
                 if ($cartQuoteItem->getCustomStrapLength() == $strapLengthOrignal) {
                $skuFound = true;
                
                $qty = $cartQuoteItem->getQty() + $qty;
                // $logger->info($qty);
                $cartQuoteItem->setQty($qty);

                $cartQuoteItem->setData('custom_strap_length', $strapLengthOrignal);
                $cartQuoteItem->addOption(array(
                    'product' => $product,
                    'code' => 'additional_options',
                    'value' => json_encode($options)
                ));

                $cart->save();
            }
             }
        }

        if (!$skuFound) {

            $cartItem = $cart->addProduct($product, $qty);
            $item = $cart->getQuote()->getItemByProduct($product);
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
            $item->setData('custom_strap_length', $strapLengthOrignal);
            $item->addOption(array(
                'code' => 'additional_options',
                'value' => $this->serializer->serialize($options)
            ));
            $cart->save();
        }
        $result->setData('https://hookandloop.99stockpics.com/checkout/cart/');
        return $result;
    }

}
