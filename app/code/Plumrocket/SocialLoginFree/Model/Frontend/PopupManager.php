<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Frontend;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * @deprecated since 4.0.0
 * @see \Plumrocket\SocialLoginFree\Model\Network\Modal\Manager
 */
class PopupManager
{

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    /**
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @param bool $isAjax
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function close(bool $isAjax): ResultInterface
    {
        if ($isAjax) {
            /** @var \Magento\Framework\Controller\Result\Json $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setData(['windowClose' => true]);
        } else {
            /** @var \Magento\Framework\Controller\Result\Raw $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        }

        return $result;
    }
}
