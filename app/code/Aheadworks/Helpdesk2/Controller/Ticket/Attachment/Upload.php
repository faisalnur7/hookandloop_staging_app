<?php
namespace Aheadworks\Helpdesk2\Controller\Ticket\Attachment;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;
use Aheadworks\Helpdesk2\Model\FileSystem\FileUploader;
use Aheadworks\Helpdesk2\Model\Config;

/**
 * Class Upload
 *
 * @package Aheadworks\Helpdesk2\Controller\Ticket\Attachment
 */
class Upload extends Action
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param FileUploader $fileUploader
     * @param Config $config
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader,
        Config $config
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $result = $this->fileUploader->upload('attachments');
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @inheritdoc
     *
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->config->isAllowedToAttachFiles()) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }
}
