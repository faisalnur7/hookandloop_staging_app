<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Interface GatewayManagementInterface
 * @api
 */
interface GatewayManagementInterface
{
    /**
     * Check each stored email and convert it into ticket/message
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool
     */
    public function processEmails();
}
