<?php
namespace Magecomp\Ordercomment\Api;

interface OrdercommentmanagementInterface
{
    /**
     * @param int $cartId
     * @param \Magecomp\Ordercomment\Api\Data\OrdercommentInterface $orderComment
     * @return string
     */
    public function saveOrdercomment(
        $cartId,
        \Magecomp\Ordercomment\Api\Data\OrdercommentInterface $orderComment
    );
}

