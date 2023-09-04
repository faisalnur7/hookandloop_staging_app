<?php
namespace Magecomp\Ordercomment\Block\Order;

use Magecomp\Ordercomment\Helper\Data\Ordercomment;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\View\Element\Template;

class Comment extends Template
{
    protected $coreRegistry = null;
    const COMMENT_FIELD_NAME = 'magecomp_order_comment';

    public function __construct(
        TemplateContext $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->_isScopePrivate = true;
        $this->_template = 'order/view/comment.phtml';
        parent::__construct($context, $data);
    }

    public function hasOrdercomment()
    {
        return strlen($this->getOrdercomment()) > 0;
    }

    public function getOrdercomment()
    {
        if($this->getOrder()->getData(Ordercomment::COMMENT_FIELD_NAME)){

        return trim($this->getOrder()->getData(Ordercomment::COMMENT_FIELD_NAME));
        }
    }

    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    public function getOrdercommentHtml()
    {
        return nl2br($this->escapeHtml($this->getOrdercomment()));
    }
}
