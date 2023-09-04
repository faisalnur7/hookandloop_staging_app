<?php

namespace Exinent\TaxExempt\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;

class Index extends Action {

    protected $resultJsonFactory;
    protected $context;

    public function __construct(
    JsonFactory $resultJsonFactory, Context $context
    ) {
       $this->resultJsonFactory = $resultJsonFactory;
       return parent::__construct($context);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();

        $taxcode = $this->getRequest()->getParam('tax');
        $region = $this->getRequest()->getParam('region');

        if (($taxcode !== '' ) && ($region !== 'Please select region, state or province' ) && ($taxcode > 0)) {

            $result->setData(['taxcode' => $taxcode, 'region' => $region, 'result' => 'Tax Exempted']);
        } else {
            $result->setData(['taxcode' => $taxcode, 'region' => $region, 'result' => 'Tax Not Exempted']);
        }

        return $result;
    }

}
