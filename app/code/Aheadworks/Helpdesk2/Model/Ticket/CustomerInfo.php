<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CustomerInfo
 */
class CustomerInfo
{
    /**
     * Get customer telephone
     *
     * @param CustomerInterface $customer
     * @return string|null
     */
    public function getCustomerTelephone(CustomerInterface $customer): ?string
    {
        $addresses = $customer->getAddresses();
        $telephone = null;
        if(!empty($addresses)) {
            foreach ($addresses as $address) {
                if($address->getTelephone()) {
                    $telephone = $address->getTelephone();
                    break;
                }
            }
        }

        return $telephone;
    }
}
