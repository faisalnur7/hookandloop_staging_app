<?php
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\Thread;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageTypeSource;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class WysiwygDirectives
 */
class WysiwygDirectives implements ProcessorInterface
{
    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @param FilterProvider $filterProvider
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        FilterProvider $filterProvider,
        JsonHelper $jsonHelper,
    ) {
        $this->filterProvider = $filterProvider;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $filter = $this->filterProvider->getPageFilter();
        foreach ($data['items'] as &$item) {
            if ($item[MessageInterface::TYPE] == MessageTypeSource::ADMIN) {
                $item[MessageInterface::CONTENT] = $filter->filter($item[MessageInterface::CONTENT]);
            }

            $beforeQuotedPrintableDecode = $item;
            $item[MessageInterface::CONTENT] = quoted_printable_decode($item[MessageInterface::CONTENT]);
            $item[MessageInterface::CONTENT] = $this->removeStyles($item[MessageInterface::CONTENT]);
            $item[MessageInterface::CONTENT] = $this->removeReply($item[MessageInterface::CONTENT]);
            $item[MessageInterface::CONTENT] = $this->removeEmptyImageTag($item[MessageInterface::CONTENT]);
            if (!$this->jsonHelper->jsonEncode($item)) {
                $item = $beforeQuotedPrintableDecode;
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }

    /**
     * Get replace symbols
     *
     * @return string[]
     */
    public function getReplaceSymbols()
    {
        return ['<br>', '<br />'];
    }

    /**
     * Remove Replay tags from content
     *
     * @param string $content
     * @return string $content
     */
    public function removeReply($content)
    {
        $content = preg_replace('#<div class="gmail_quote"([\s\S]*?)</div>#', '', $content);
        $content = preg_replace('#<blockquote class="gmail_quote"([\s\S]*?)</blockquote>#', '', $content);
        $content = explode('<b>From:</b> awsupport', $content);
        $content = array_shift($content);
        $content = explode('aw-helpdesk2-reply-marker', $content);
        $content = array_shift($content);
        return $content;
    }

    /**
     * Remove some style tags from content
     *
     * @param string $content
     * @return string $content
     */
    public function removeStyles($content)
    {
        $content = preg_replace('#<style\s?([\s\S]*?)</style>#', '', $content);
        return $content;
    }

    /**
     * Remove empty image tags from content
     *
     * @param string $content
     * @return string
     */
    public function removeEmptyImageTag($content)
    {
        return preg_replace ('/<img([\s\S]*?)src="cid:[^>]*>/', '', $content);
    }
}
