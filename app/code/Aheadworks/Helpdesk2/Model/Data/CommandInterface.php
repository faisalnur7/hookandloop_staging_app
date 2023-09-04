<?php
namespace Aheadworks\Helpdesk2\Model\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;

/**
 * Interface CommandInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Data
 */
interface CommandInterface
{
    /**
     * Execute command
     *
     * @param array $data
     * @return DataObject
     * @throws LocalizedException
     */
    public function execute($data);
}
