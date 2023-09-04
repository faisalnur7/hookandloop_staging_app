<?php
namespace Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Posts;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\ProcessorInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Customer\Model\GroupManagement;

/**
 * Class CustomerGroups
 * @package Aheadworks\BlogGraphQl\Model\Resolver\ArgumentProcessor\Posts
 */
class CustomerGroups implements ProcessorInterface
{
    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @param GetCustomer $getCustomer
     */
    public function __construct(
        GetCustomer $getCustomer
    ) {
        $this->getCustomer = $getCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data, $context)
    {
        try {
            /** @var CustomerInterface $customer */
            $customer = $this->getCustomer->execute($context);
            $customerGroupId = $customer->getGroupId();
        } catch (\Exception $e) {
            $customerGroupId = GroupManagement::NOT_LOGGED_IN_ID;
        }

        $data['filter'][PostInterface::CUSTOMER_GROUPS]['eq'] = $customerGroupId;
        return $data;
    }
}