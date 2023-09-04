<?php
namespace Aheadworks\Helpdesk2\ViewModel\Order;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Helpdesk2\Model\UrlBuilder;

/**
 * Class CreateTicketButton
 *
 * @package Aheadworks\Helpdesk2\ViewModel\Order
 */
class CreateTicketButton implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param RequestInterface $request
     * @param UrlBuilder $urlBuilder
     */
    public function __construct(
        RequestInterface $request,
        UrlBuilder $urlBuilder
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        $orderId = $this->request->getParam('order_id');
        $params = $orderId ? ['order_id' => $orderId] : [];
        return $this->urlBuilder->getTicketCreateLinkFromBackend($params);
    }
}
