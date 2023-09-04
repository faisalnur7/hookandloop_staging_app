<?php
namespace Magecomp\Ordercomment\Api;

interface GuestordercommentmanagementInterface
{
    /**
     * @param string $cartId
     * @param \Magecomp\Ordercomment\Api\Data\OrdercommentInterface $orderComment
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */


    public function saveOrdercomment(
        $cartId,
        \Magecomp\Ordercomment\Api\Data\OrdercommentInterface $orderComment
    );
}


