<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Search Base for Magento 2
 */

namespace Amasty\Xsearch\Model;

use Magento\Customer\Model\Session as CustomerSession;

class Authentication
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    public function getCustomerIdentifier(): string
    {
        $customerIdentifier = $this->customerSession->getCustomerId() ?: $this->customerSession->getSessionId();

        return (string)$customerIdentifier;
    }

    public function isAuthenticated(): bool
    {
        return (bool)$this->customerSession->isLoggedIn();
    }
}
