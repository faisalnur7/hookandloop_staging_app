<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dckap\CustomFields\Controller\Express;

class SaveShippingMethod extends \Dckap\CustomFields\Controller\Express\AbstractExpress\SaveShippingMethod
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = \Magento\Paypal\Model\Config::class;

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = \Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = \Magento\Paypal\Model\Express\Checkout::class;
}
