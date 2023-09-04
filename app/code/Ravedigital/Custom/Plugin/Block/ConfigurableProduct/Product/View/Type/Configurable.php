<?php
namespace Ravedigital\Custom\Plugin\Block\ConfigurableProduct\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;

class Configurable
{
    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Configurable constructor.
     *
     * @param EncoderInterface $jsonEncoder
     * @param DecoderInterface $jsonDecoder
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;;
        $this->stockRegistry = $stockRegistry;
    }

    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    ) {
        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);
        $productsCollection = $subject->getAllowProducts();
        $stockInfo = array();
        foreach ($productsCollection as $product) {
            $productId = $product->getId();
            $stockItem = $this->stockRegistry->getStockItem($product->getId());
            if ($stockItem->getQty() <= 0 || !($stockItem->getIsInStock())) {
                $stockInfo[$productId] = array(
                    "stockLabel" => __('Out of stock'),
                    "stockQty" => intval($stockItem->getQty()),
                    "is_in_stock" => false
                );
            } else {
                $stockInfo[$productId] = array(
                    "stockLabel" => __('In Stock'),
                    "stockQty" => intval($stockItem->getQty()),
                    "is_in_stock" => true
                );
            }
        }

        $config['stockInfo'] = $stockInfo;
        return $this->jsonEncoder->encode($config);
    }
}