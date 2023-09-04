<?php
namespace Aheadworks\Helpdesk2\Api;

/**
 * Interface DepartmentManagementInterface
 * @api
 */
interface DepartmentManagementInterface
{
    /**
     * Run each active gateway and retrieve information
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool
     */
    public function processGateways();
}
