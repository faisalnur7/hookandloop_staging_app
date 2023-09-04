<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Columns;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Filter\FilterManager;
use Aheadworks\Helpdesk2\Model\DateTime\Formatter;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;

/**
 * Class DataFormatter
 */
class DataFormatter
{
    /**
     * @param FilterManager $filterManager
     * @param Formatter $dateTimeFormatter
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly FilterManager $filterManager,
        private readonly Formatter $dateTimeFormatter,
        private readonly EncryptorInterface $encryptor
    ) {}

    /**
     * Prepare message content
     *
     * @param array $item
     * @return string
     */
    public function prepareMessageContent($item)
    {
        $content = '';
        if (!empty($item[GridInterface::LAST_MESSAGE_CONTENT])) {
            if ($item[GridInterface::IS_ENCRYPTED]) {
                $content = $this->encryptor->decrypt($item[GridInterface::LAST_MESSAGE_CONTENT]);
            } else {
                $content = $item[GridInterface::LAST_MESSAGE_CONTENT];
            }
            $content = preg_replace('#<head([\s\S]*?)</head>#', '', $content);
            $content = $this->filterManager->truncate(
                strip_tags($content),
                ['length' => 500]
            );

            $content = preg_replace('/=[A-Z0-9_]\w/', '', $content);
            $content = trim($content);
            $content = preg_replace('~[\r\n]+~', '', $content);
            $content = str_replace(
                ['=', "\n", "\r\n", "\n\r"],
                "",
                $content
            );
        }

        return $content;
    }

    /**
     * Prepare message date
     *
     * @param array $item
     * @return string
     */
    public function prepareMessageDate($item)
    {
        $messageDate = '';
        if (isset($item[GridInterface::LAST_MESSAGE_DATE])
            && $item[GridInterface::LAST_MESSAGE_DATE] !== "0000-00-00 00:00:00"
        ) {
            $messageDate = $this->dateTimeFormatter->formatDate($item[GridInterface::LAST_MESSAGE_DATE]);
        }

        return $messageDate;
    }
}
