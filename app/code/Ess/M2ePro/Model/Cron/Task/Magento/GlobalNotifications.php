<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Cron\Task\Magento;

use Ess\M2ePro\Model\Issue\LocatorInterface;

/**
 * Class \Ess\M2ePro\Model\Cron\Task\Magento\GlobalNotifications
 */
class GlobalNotifications extends \Ess\M2ePro\Model\Cron\Task\AbstractModel
{
    public const NICK = 'magento/global_notifications';

    /**
     * @var int (in seconds)
     */
    protected $interval = 86400;

    //########################################

    protected function performActions()
    {
        /** @var \Ess\M2ePro\Model\Issue\Notification\Channel\Magento\GlobalMessage $notificationChannel */
        $notificationChannel = $this->modelFactory->getObject('Issue_Notification_Channel_Magento_GlobalMessage');
        $issueLocators = [
            'Ebay_Account_Issue_ExpiredTokens',
        ];

        foreach ($issueLocators as $locator) {
            /** @var LocatorInterface $locatorModel */
            $locatorModel = $this->modelFactory->getObject($locator);

            foreach ($locatorModel->getIssues() as $issue) {
                $notificationChannel->addMessage($issue);
            }
        }
    }

    //########################################
}
