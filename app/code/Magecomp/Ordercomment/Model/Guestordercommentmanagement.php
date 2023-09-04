<?php
namespace Magecomp\Ordercomment\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;
use Magecomp\Ordercomment\Api\GuestordercommentmanagementInterface;
use Magecomp\Ordercomment\Api\OrdercommentmanagementInterface;
use Magecomp\Ordercomment\Api\Data\OrdercommentInterface;

class Guestordercommentmanagement implements GuestordercommentmanagementInterface
{
    protected $quoteIdMaskFactory;
    protected $orderCommentManagement;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        OrdercommentmanagementInterface $orderCommentManagement
    )
    {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->orderCommentManagement = $orderCommentManagement;
    }

    public function saveOrdercomment($cartId, \Magecomp\Ordercomment\Api\Data\OrdercommentInterface $orderComment)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
       
        return $this->orderCommentManagement->saveOrdercomment($quoteIdMask->getQuoteId(),$orderComment);
    }
}
