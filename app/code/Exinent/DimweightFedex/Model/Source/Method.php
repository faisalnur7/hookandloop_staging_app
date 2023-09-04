<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Fedex method source implementation
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Exinent\DimweightFedex\Model\Source;

class Method extends \Exinent\DimweightFedex\Model\Source\Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'method';
}
