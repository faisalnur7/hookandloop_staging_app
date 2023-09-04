<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Block\Sales\Adminhtml\Order\Invoice\Create;

/**
 * Adminhtml invoice items grid
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items
{
    

    /**
     * Check if invoice can be captured
     *
     * @return bool
     */
    public function canCapture()
    {
        return true;
        // return $this->getInvoice()->canCapture();
    }
}
