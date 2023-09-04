<?php
namespace Ravedigital\Addtocart\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\LocalizedToNormalized;

// use Zend_Filter_LocalizedToNormalized;


class Add extends \Magento\Checkout\Controller\Cart\Add
{
    protected $productRepository;
    protected $checkoutSession;
    protected $formKeyValidator;
    protected $scopeConfig;
    protected $cart;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->_checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->_formKeyValidator = $formKeyValidator;
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $productRepository);
    }

    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['super_attribute'][174]) && $params['super_attribute'][174]=='180-181') {

                $i= 1;
                $expl = explode('-', $params['super_attribute'][174]);

                 $this->_checkoutSession->setHasBothProducts(true);
                
                foreach ($expl as $hookandloopVal) {
                    $params['super_attribute'][174]= '';
                     $params['super_attribute'][174]= $hookandloopVal;

                    if (isset($params['qty'])) {
                        // $filter = new \Zend_Filter_LocalizedToNormalized
                        $filter = new LocalizedToNormalized();
                        (
                            ['locale' => $this->_objectManager->get(
                                \Magento\Framework\Locale\ResolverInterface::class
                            )->getLocale()]
                        );
                        $params['qty'] = $filter->filter($params['qty']);
                    }
        
                     //$product = $this->_initProduct();
                    $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($params['product']);
                   
                    $related = $this->getRequest()->getParam('related_product');

                    /**
                     * Check product availability
                     */
                    if (!$product) {
                        return $this->goBack();
                    }
                    $this->cart->addProduct($product, $params);
                    if (!empty($related)) {
                        $this->cart->addProductsByIds(explode(',', $related));
                    }

                   
                     $params['super_attribute'][174] = '';
                     $i++;
                }
                $this->cart->save();
                  
                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                );
            } else {

                if (isset($params['qty'])) {
                    $filter = new LocalizedToNormalized(
                        ['locale' => $this->_objectManager->get(
                            \Magento\Framework\Locale\ResolverInterface::class
                        )->getLocale()]
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }

                $product = $this->_initProduct();
                $related = $this->getRequest()->getParam('related_product');

                /**
                 * Check product availability
                 */
                if (!$product) {
                    return $this->goBack();
                }
                $this->cart->addProduct($product, $params);
                if (!empty($related)) {
                    $this->cart->addProductsByIds(explode(',', $related));
                }

                $this->cart->save();

                 $this->_eventManager->dispatch(
                     'checkout_cart_add_product_complete',
                     ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                 );
            }

            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
           /* $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );*/

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
                return $this->goBack(null, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->_objectManager->get(\Magento\Checkout\Helper\Cart::class)->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            return $this->goBack();
        }
    }
}
