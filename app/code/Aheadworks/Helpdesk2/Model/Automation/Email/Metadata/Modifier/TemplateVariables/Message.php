<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class Message
 */
class Message implements ModifierInterface
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @param FilterProvider $filterProvider
     */
    public function __construct(FilterProvider $filterProvider)
    {
        $this->filterProvider = $filterProvider;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        if (!$eventData->getMessage()) {
            throw new LocalizedException(__('Message must be present in event data'));
        }
        $filter = $this->filterProvider->getPageFilter();
        $message = $eventData->getMessage();
        $message->setContent($filter->filter($message->getContent()));

        $templateVariables = $emailMetadata->getTemplateVariables();
        $message->setContent(str_replace(["\r\n", "\n\r"], '<br>', (string)$message->getContent()));
        $templateVariables[EmailVariables::MESSAGE] = $message;
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
