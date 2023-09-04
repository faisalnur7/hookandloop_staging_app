<?php
namespace Aheadworks\Helpdesk2\Model\Email;

use Magento\Framework\Mail\TemplateInterface;
use Magento\Email\Model\Template as MagentoEmailTemplate;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Message\Filter as MessageFilter;
use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables\Marker;

/**
 * Class Template
 *
 * @package Aheadworks\Helpdesk2\Model\Email
 */
class Template extends MagentoEmailTemplate implements TemplateInterface
{
    /**
     * @inheritDoc
     */
    public function load($modelId, $field = null)
    {
        parent::load($modelId, $field);
        $this->setData('is_legacy', true);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadDefault($templateId)
    {
        parent::loadDefault($templateId);
        $this->setData('is_legacy', true);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProcessedTemplate(array $variables = [])
    {
        $variables = $this->getUpdatedContent($variables);
        $processedTemplate = parent::getProcessedTemplate($variables);
        if (isset($variables[Marker::HISTORY_MARKER_FLAG_NAME])
            && $variables[Marker::HISTORY_MARKER_FLAG_NAME]
        ) {
            $processedTemplate = $this->getRepliesHistoryMarker() . $processedTemplate;
        }
        return $processedTemplate;
    }

    /**
     * Update messages before insert
     *
     * @return array
     */
    private function getUpdatedContent(array $variables = [])
    {
        if (isset($variables['message'])) {
            $message = $variables['message'];
            $content = $message->getData('content');
            $message->setData('content', quoted_printable_decode($content));
        }

        return $variables;
    }

    /**
     * Get replies history marker
     *
     * @return string
     */
    private function getRepliesHistoryMarker()
    {
        $message = __('Please type your reply above this line.');
        $markerHtml = '<br><div class="aw-helpdesk2-reply-marker">' . $message . '</div><br>';

        return MessageFilter::REPLIES_HISTORY_MARKER . $markerHtml;
    }
}
