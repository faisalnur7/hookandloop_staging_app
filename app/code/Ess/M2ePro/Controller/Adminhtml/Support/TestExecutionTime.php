<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Support;

use Ess\M2ePro\Controller\Adminhtml\Support;

class TestExecutionTime extends Support
{
    /** @var \Ess\M2ePro\Helper\Client */
    private $clientHelper;

    public function __construct(
        \Ess\M2ePro\Helper\Client $clientHelper,
        \Ess\M2ePro\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->clientHelper = $clientHelper;
    }

    public function execute()
    {
        $this->clientHelper->testExecutionTime((int)$this->getRequest()->getParam('seconds'));

        $this->setJsonContent(['result' => true]);

        return $this->getResult();
    }
}
