<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Model;

class QuoteRepository extends \Magento\Quote\Model\QuoteRepository
{
     /**
      * @inheritdoc
      */
    public function getActive($cartId, array $sharedStoreIds = [])
    {
        $quote = $this->get($cartId, $sharedStoreIds);
        if (!$quote->getIsActive() && !$quote->getReservedOrderId()) {
            throw \Magento\Framework\Exception\NoSuchEntityException::singleField('cartId', $cartId);
        }
        return $quote;
    }
}
