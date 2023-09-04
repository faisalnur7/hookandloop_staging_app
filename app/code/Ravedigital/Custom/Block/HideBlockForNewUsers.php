<?php

namespace Ravedigital\Custom\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class HideBlockForNewUsers extends Template
{
    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_customerSessionFactory = $customerSessionFactory;
    }

    public function shouldShowBlock()
    {  
        // $customer = $this->customerSession->getCustomer(); //Get Current Customer Data
        $customer = $this->_customerSessionFactory->create();
        if ($customer->getCustomer()->getId()) {
            $createdAt = $customer->getCustomer()->getCreatedAt();
            $last2WeekDate = date("Y-m-d", strtotime("-14 days"));
            if ($createdAt >= $last2WeekDate) {
                return false; // Hide the block
            }
        }
        return true; // Show the block
    }
}