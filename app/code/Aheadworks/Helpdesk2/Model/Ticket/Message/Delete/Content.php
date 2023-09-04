<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Ticket\Message\Delete;

use Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver as AuthorResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Model\DateTime\Formatter;

class Content
{
    /**
     * @param Formatter $dateTimeFormatter
     * @param AuthorResolver $authorResolver
     */
    public function __construct(
        private Formatter $dateTimeFormatter,
        private AuthorResolver $authorResolver,
    ) {
    }

    /**
     * Get content of the delete message
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getContent(): string
    {
        $autor = $this->authorResolver->resolveAgent();
        $currentDateAndTimeZone = $this->dateTimeFormatter->getCurrentDateAndTimezoneAsString();

        $contentMessage = __(
            'The message was deleted by %1 on %2.',
            $autor->getName(),
            $currentDateAndTimeZone
        );

        return $contentMessage->render();
    }
}
