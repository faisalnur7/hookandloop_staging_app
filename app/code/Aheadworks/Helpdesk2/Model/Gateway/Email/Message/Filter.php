<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Message;

/**
 * Class Filter
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Message
 */
class Filter
{
    /**
     * RegEx pattern to detect previous replies
     */
    const REPLIES_HISTORY_REGEX = '/(<!--){1}(\sHD2_REPLY_MARKER)[\s\S]*/';
    const REPLIES_HISTORY_MARKER = '<!-- HD2_REPLY_MARKER -->';

    /**
     * Cut history of previous replies
     *
     * @param string $content
     * @return string
     */
    public function cutRepliesHistory($content)
    {
        return preg_replace(self::REPLIES_HISTORY_REGEX, '', $content);
    }
}
