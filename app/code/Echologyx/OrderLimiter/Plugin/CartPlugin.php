<?php
namespace Echologyx\OrderLimiter\Plugin;

use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class CartPlugin
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    protected $request;
    protected $dataHelper;
    protected $context;
    protected $jsonFactory;

    public function __construct(
        ManagerInterface $messageManager,
        Http $request,
        Context $context,
        JsonFactory $jsonFactory,
    ) {
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->context = $context;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Plugin method to check total cart quantity before updating items in the cart.
     *
     * @param CheckoutCart $subject
     * @param array $data
     * @return array
     */
    public function beforeUpdateItems(CheckoutCart $subject, $data)
    {
        // Calculate the total quantity of all items in the cart

        $requestValues = $this->request->getParams();

        $currentTotalQuantity = 0;
        foreach ($subject->getQuote()->getAllVisibleItems() as $cartItem) {
            $measurementsoldin = $cartItem->getProduct()->getMeasurementSoldInSize(); 
            $cartQty = $cartItem->getQty();
            $actualQty = (float) $cartQty / (float) $measurementsoldin;

            if($actualQty > 1){
                $currentTotalQuantity += $actualQty;
            }
            if(isset($requestValues['item_id']) && ($cartItem->getItemId() == $requestValues['item_id'])){
                $miniCartItemQty = $actualQty;
                $miniCartItemMeasurementSoldIn = $measurementsoldin;
            }
        }

        $proposedTotalQuantity = 0;
        if(isset($requestValues['nrml_qty'])){
            foreach($requestValues['nrml_qty'] as $qty){
                $proposedTotalQuantity+=$qty;
            }
        }else if(isset($requestValues['item_id'])){
            $proposedTotalQuantity = $currentTotalQuantity - $miniCartItemQty + ($requestValues['item_qty'] / round($miniCartItemMeasurementSoldIn));
        }


        $maxTotalQuantity = 1000;

        if ($proposedTotalQuantity > $maxTotalQuantity) {
            $this->messageManager->addErrorMessage(
                __('The maximum total cart quantity allowed is %1.', $maxTotalQuantity)
            );

            if(!isset($requestValues['item_id'])){
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('checkout/cart');

                return $resultRedirect;
            }else{
                $resultJson = $this->jsonFactory->create();
                $resultJson->setData(['error' => __('The maximum total cart quantity allowed is %1.', $maxTotalQuantity)]);
                return [$resultJson];
            }
        }

        return [$data];
    }
}