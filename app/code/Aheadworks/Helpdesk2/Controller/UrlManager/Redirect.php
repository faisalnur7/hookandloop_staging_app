<?php
namespace Aheadworks\Helpdesk2\Controller\UrlManager;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;

/**
 * Class Redirect
 *
 * @package Aheadworks\Helpdesk2\Controller\UrlManager\Redirect
 */
class Redirect extends Action
{
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * Redirect constructor.
     * @param ResultFactory $resultFactory
     * @param Context $context
     */
    public function __construct(
        ResultFactory $resultFactory,
        Context $context
    ) {
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $url = $this->getRequest()->getParam('url');
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $url = base64_decode($url);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $redirect->setUrl($this->_redirect->getRefererUrl());
        } else {
            $redirect->setUrl($url);
        }
        return $redirect;
    }
}
