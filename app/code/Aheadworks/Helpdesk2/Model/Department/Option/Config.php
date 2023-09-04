<?php
namespace Aheadworks\Helpdesk2\Model\Department\Option;

use Magento\Framework\Config\Data as ConfigData;

/**
 * Class Config
 *
 * @package Aheadworks\Helpdesk2\Model\Department\Option
 */
class Config extends ConfigData
{
    /**
     * Get configuration of all registered storefront options
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }

    /**
     * Retrieve types by group
     *
     * @param string $groupCode
     * @return array
     */
    public function getTypesByGroup($groupCode)
    {
        return array_keys($this->get($groupCode . '/types'));
    }
}
