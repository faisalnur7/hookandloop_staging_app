<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket\Message;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\ContentModifier;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageTypeSource;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class Content
 */
class Content implements ProcessorInterface
{
    /**
     * @var string[]
     */
    private $patternsToRemove = [
        'script_tag' => '/<script\b[^>]*>(.*?)<\/script>/is',
        'tag_events' => '/ on\w+=("[^"]*"|\'[^\']*\')/is',
        'href_js' => '/ href=("javascript:[^"]*"|\'javascript:[^\']*\')/is'
    ];

    /**
     * Content constructor.
     * @param ContentModifier $contentModifier
     * @param EncryptorInterface $encryptor
     * @param array $patternsToRemove
     */
    public function __construct(
        private readonly ContentModifier $contentModifier,
        private readonly EncryptorInterface $encryptor,
        array $patternsToRemove = []
    ) {
        $this->patternsToRemove = array_merge(
            $this->patternsToRemove,
            $patternsToRemove
        );
    }

    /**
     * Prepare model before save
     *
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function prepareModelBeforeSave($message)
    {
        $skipEscapeType = [
            MessageTypeSource::ADMIN,
            MessageTypeSource::SYSTEM
        ];
        if (!in_array($message->getType(), $skipEscapeType)) {
            $content = $message->getContent();
            foreach ($this->patternsToRemove as $pattern) {
                $content = preg_replace($pattern, '', $content);
            }
            $message->setContent($content);
        }
        $message->setContent($this->encryptor->encrypt(
            $this->contentModifier->modify($message->getContent(), $message->getTicketId())));
        $message->setIsEncrypted(true);

        return $message;
    }

    /**
     * Prepare model after load
     *
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function prepareModelAfterLoad($message)
    {
        if ($message->getContent() && $message->getIsEncrypted()) {
            $content = $message->getContent();
            $message->setContent($this->encryptor->decrypt($content));
        }

        return $message;
    }
}
