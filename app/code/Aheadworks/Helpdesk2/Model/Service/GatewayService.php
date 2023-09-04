<?php
namespace Aheadworks\Helpdesk2\Model\Service;

use Aheadworks\Helpdesk2\Api\GatewayManagementInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Loader as EmailLoader;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Processor as EmailProcessor;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as EmailResourceModel;

/**
 * Class GatewayService
 *
 * @package Aheadworks\Helpdesk2\Model\Service
 */
class GatewayService implements GatewayManagementInterface
{
    /**
     * @var EmailLoader
     */
    private $emailLoader;

    /**
     * @var EmailResourceModel
     */
    private $emailResource;

    /**
     * @var EmailProcessor
     */
    private $emailProcessor;

    /**
     * @param EmailLoader $emailLoader
     * @param EmailResourceModel $emailResource
     * @param EmailProcessor $emailProcessor
     */
    public function __construct(
        EmailLoader $emailLoader,
        EmailResourceModel $emailResource,
        EmailProcessor $emailProcessor
    ) {
        $this->emailLoader = $emailLoader;
        $this->emailResource = $emailResource;
        $this->emailProcessor = $emailProcessor;
    }

    /**
     * @inheritdoc
     */
    public function processEmails()
    {
        $emails = $this->emailLoader->loadUnprocessedEmails();
        foreach ($emails as $email) {
            $email = $this->emailProcessor->process($email);
            $this->emailResource->save($email);
        }

        return true;
    }
}
