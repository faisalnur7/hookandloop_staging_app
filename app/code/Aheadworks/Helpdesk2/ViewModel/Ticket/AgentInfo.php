<?php
namespace Aheadworks\Helpdesk2\ViewModel\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Model\UserProfile\ImageInfo;
use Aheadworks\Helpdesk2\Model\Ticket\Agent\Locator as AgentLocator;

/**
 * Class AgentInfo
 *
 * @package Aheadworks\Helpdesk2\ViewModel\Ticket
 */
class AgentInfo implements ArgumentInterface
{
    /**
     * @var AgentLocator
     */
    private $agentLocator;

    /**
     * @var ImageInfo
     */
    private $imageInfo;

    /**
     * @param ImageInfo $imageInfo
     * @param AgentLocator $agentLocator
     */
    public function __construct(
        ImageInfo $imageInfo,
        AgentLocator $agentLocator
    ) {
        $this->imageInfo = $imageInfo;
        $this->agentLocator = $agentLocator;
    }

    /**
     * Get agent info by request
     *
     * @return UserProfileInterface|null
     * @throws LocalizedException
     */
    public function getAgentProfileByRequest()
    {
        $userProfile = $this->agentLocator->locateAgentByTicketIdRequest();
        return $this->checkProfile($userProfile);
    }

    /**
     * Get agent info for ticket
     *
     * @param TicketInterface $ticket
     * @return UserProfileInterface|null
     * @throws LocalizedException
     */
    public function getAgentProfileForTicket($ticket)
    {
        $userProfile = $this->agentLocator->locateAgentByTicket($ticket);
        return $this->checkProfile($userProfile);
    }

    /**
     * Retrieve url by image path
     *
     * @param string $imagePath
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($imagePath)
    {
        return $this->imageInfo->getMediaUrl($imagePath);
    }

    /**
     * Check profile
     *
     * @param UserProfileInterface|null $userProfile
     * @return UserProfileInterface|null
     */
    private function checkProfile($userProfile)
    {
        return $userProfile && $userProfile->getStatus() ? $userProfile : null;
    }
}
