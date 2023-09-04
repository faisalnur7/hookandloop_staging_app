<?php
namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Zend\Mail\Storage\Message as MailMessage;
use Zend\Mime\Mime;
use Zend\Mail\Header\ContentTransferEncoding;
use Zend\Mail\Header\ContentType;
use Zend\Mail\Header\Subject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Attachment\Factory as AttachmentFactory;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Message\Filter as MessageFilter;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Email\Status as EmailStatus;
use Magento\Framework\Validation\ValidationException;

/**
 * Class Factory
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email
 */
class Factory
{
    /**
     * @var MessageFilter
     */
    private $messageFilter;

    /**
     * @var EmailInterfaceFactory
     */
    private $emailFactory;

    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;

    /**
     * @param EmailInterfaceFactory $emailFactory
     * @param MessageFilter $messageFilter
     * @param AttachmentFactory $attachmentFactory
     */
    public function __construct(
        EmailInterfaceFactory $emailFactory,
        MessageFilter $messageFilter,
        AttachmentFactory $attachmentFactory
    ) {
        $this->emailFactory = $emailFactory;
        $this->messageFilter = $messageFilter;
        $this->attachmentFactory = $attachmentFactory;
    }

    /**
     * Create email from message
     *
     * @param MailMessage $message
     * @return EmailInterface
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function create($message)
    {
        /** @var EmailInterface $email */
        $email = $this->emailFactory->create();
        $email
            ->setFrom($this->getMessageFrom($message))
            ->setTo($this->getMessageTo($message))
            ->setCcRecipients($this->getCcRecipients($message))
            ->setSubject($this->getMessageSubject($message))
            ->setBody($this->getMessageBody($message))
            ->setContentType(strtok($this->getMessageContentType($message), ';'))
            ->setStatus(EmailStatus::UNPROCESSED);
        try {
            $email->setAttachments($this->getMessageAttachments($message));
        } catch (ValidationException $exception) {
            $email->setBody($email->getBody() . __("This message contains an invalid attachment"));
        }

        return $email;
    }

    /**
     * Get message sender
     *
     * @param MailMessage $message
     * @return string
     */
    private function getMessageFrom($message)
    {
        $from = $message->from;
        if (!$from) {
            $from = __('Unknown');
        }

        return $from;
    }

    /**
     * Get message recipient
     *
     * @param MailMessage $message
     * @return bool|string
     */
    private function getMessageTo($message)
    {
        $to = $this->decodeMimeHeader($message->to);
        if (!$to) {
            $to = __('Unknown');
        }

        return $to;
    }

    /**
     * Get cc recipients
     *
     * @param MailMessage $message
     * @return string|null
     */
    private function getCcRecipients($message)
    {
        $cc = null;
        if (isset($message->cc)) {
            $cc = $this->decodeMimeHeader($message->cc);
            if (!$cc) {
                $cc = null;
            }
        }

        return $cc;
    }

    /**
     * Decode mime header
     *
     * @param string $value
     * @return bool|string
     */
    public function decodeMimeHeader($value)
    {
        $encoding = mb_detect_encoding($value, 'auto', true);
        if ($encoding === false) {
            try {
                $encoding = iconv_get_encoding();
                $encodedValue = iconv($encoding['internal_encoding'], 'UTF-8', $value);
            } catch (\Exception $e) {
                $encodedValue = false;
            }

            return $encodedValue;
        }

        if ($encoding == 'UTF-8') {
            $encodedValue = $value;
        } else {
            //phpcs:ignore Magento2.Functions.DiscouragedFunction
            $encodedValue = iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        }

        return $encodedValue;
    }

    /**
     * Get message body
     *
     * @param MailMessage $message
     * @return string
     */
    private function getMessageBody($message)
    {
        $part = $this->getMainPart($message);
        $headers = $part->getHeaders();
        $encodedContent = $part->getContent();

        /** @var ContentTransferEncoding $encodingHeader */
        $encodingHeader = $headers->get('content-transfer-encoding');
        $content = $encodingHeader
            ? $this->decodeContent($encodingHeader->getTransferEncoding(), $encodedContent)
            : $encodedContent;

        $contentType = $this->getMessageContentType($message);
        foreach (explode(";", (string)$contentType) as $headerPart) {
            $headerPart = strtolower(trim((string)$headerPart));
            if (strpos($headerPart, 'charset=') !== false) {
                $charset = preg_replace('/charset=[^a-z0-9\-_]*([a-z\-_0-9]+)[^a-z0-9\-]*/i', "$1", $headerPart);
                $content = iconv($charset, 'UTF-8//TRANSLIT//IGNORE', $content);
                break;
            }
        }

        return $this->messageFilter->cutRepliesHistory($content);
    }

    /**
     * Decode content
     *
     * @param string $encoding
     * @param string $content
     * @return string
     */
    private function decodeContent($encoding, $content)
    {
        // Decoding transfer-encoding
        switch ($encoding) {
            case Mime::ENCODING_QUOTEDPRINTABLE:
                $content = quoted_printable_decode($content);
                break;
            case Mime::ENCODING_BASE64:
                //phpcs:ignore Magento2.Functions.DiscouragedFunction
                $content = base64_decode($content);
                break;
            default:
                $content = quoted_printable_decode($content);
        }

        return $content;
    }

    /**
     * Returns main mail part
     *
     * @param MailMessage $message
     * @return MailMessage
     */
    private function getMainPart($message)
    {
        $result = null;
        if ($message->isMultipart()) {
            $result = $this->getPartOfMessage($message);
        }
        try {
            return $result ?? $message->getPart(1);
        } catch (\Exception $e) {
            return $message;
        }
    }

    /**
     * Returns mail part
     *
     * @param MailMessage $message
     * @return MailMessage|null
     */
    private function getPartOfMessage($message)
    {
        $result = null;
        for ($i = 1; $i <= $message->countParts(); $i++) {
            $part = $message->getPart($i);
            $xattachmentid = $part->getHeaders()->get('X-Attachment-Id') ?? false;
            if ($xattachmentid) {
                return $result;
            }
            $type = $part->getHeaders()->get('content-type')->getType();
            if ($type == 'text/html' || ($type == 'text/plain' && !$result)) {
                $result = $part;
            } else {
                if ($part->countParts()) {
                    $result = $this->getPartOfMessage($part);
                }
            }
        }

        return $result;
    }

    /**
     * Get message content type
     *
     * @param MailMessage $message
     * @return string
     */
    private function getMessageContentType($message)
    {
        $part = $this->getMainPart($message);
        try {
            $headers = $part->getHeaders();
            /** @var ContentType $contentTypeHeader */
            $contentTypeHeader = $headers->get('content-type');
            if ($contentTypeHeader) {
                $contentType = $contentTypeHeader ? $contentTypeHeader->getType() : Mime::TYPE_TEXT;
                if ($contentTypeHeader->getParameter('charset') != null) {
                    $contentType = $contentType . ';charset=' . $contentTypeHeader->getParameter('charset');
                }
            }
        } catch (\Exception $e) {
            $contentType = Mime::TYPE_TEXT;
        }

        return $contentType;
    }

    /**
     * Get message subject
     *
     * @param MailMessage $message
     * @return string
     */
    private function getMessageSubject($message)
    {
        $subject = false;
        $headers = $message->getHeaders();
        /** @var Subject $subjectHeader */
        $subjectHeader = $headers->get('subject');
        if ($subjectHeader) {
            $subject = $message->subject;
        }

        if (!$subject) {
            $subject = __('No Subject');
        }

        return $subject;
    }

    /**
     * Get message attachments
     *
     * @param MailMessage $message
     * @return EmailAttachmentInterface[]
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getMessageAttachments($message)
    {
        $emailAttachments = [];
        $attachments = $this->retrieveAttachments($message);
        if (is_array($attachments)) {
            foreach ($attachments as $attachmentData) {
                if (is_array($attachmentData)) {
                    /** @var EmailAttachmentInterface $attachment */
                    $attachment = $this->attachmentFactory->create($attachmentData);
                    $emailAttachments[] = $attachment;
                }
            }
        }

        return $emailAttachments;
    }

    /**
     * Retrieve attachments from gateway message
     *
     * @param MailMessage $message
     * @return array|bool
     * @throws FileSystemException
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function retrieveAttachments($message)
    {
        $data = [];

        if ($message->isMultipart()) {
            $parts = $message;
            foreach (new \RecursiveIteratorIterator($parts) as $part) {
                $attachment = $this->retrieveAttachments($part);
                if ($attachment) {
                    $data[] = $attachment;
                }
            }
        } else {
            $pattern = '/(name|filename)="{0,1}([^;\"]*)"{0,1}/si';
            $isAttachment = false;
            $filename = '';
            $headers = $message->getHeaders();
            foreach ($headers as $header) {
                if ($isAttachment = preg_match($pattern, (string)$header->getFieldValue(), $matches)) {
                    $filename = $matches[2];
                    break;
                }
            }

            if ($isAttachment) {
                $encodedContent = $message->getContent();
                /** @var ContentTransferEncoding $encodingHeader */
                $encodingHeader = $headers->get('content-transfer-encoding');
                $content = $encodingHeader
                    ? $this->decodeContent($encodingHeader->getTransferEncoding(), $encodedContent)
                    : $encodedContent;

                return [
                    'filename' => $this->prepareAttachmentFilename($filename),
                    'content' => $content
                ];
            }

            return false;
        }

        return $data;
    }

    /**
     * Prepare attachment filename
     *
     * @param string $filename
     * @return string
     */
    private function prepareAttachmentFilename($filename)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $filename = iconv_mime_decode($filename, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $pathInfo = pathinfo($filename);
        $preparedFilename = $filename;

        if ($pathInfo['filename'] === '') {
            $preparedFilename = 'file' . $filename;
        } elseif (str_replace(' ', '', $pathInfo['filename']) === '') {
            $preparedFilename = 'file.' . $pathInfo['extension'];
        }

        return $preparedFilename;
    }
}
