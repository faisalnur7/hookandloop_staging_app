<?php
namespace Aheadworks\Helpdesk2\Model\Email\Template;

use Aheadworks\Helpdesk2\Model\Email\Mail\EmailMessage;
use Aheadworks\Helpdesk2\Model\Email\Mail\EmailMessageFactory;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\HeaderInterface;
use Zend\Mime\Mime;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder as FrameworkTransportBuilder;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\Exception\InvalidArgumentException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\MimeInterface;

/**
 * Class TransportBuilder
 *
 * @package Aheadworks\Helpdesk2\Model\Email\Template
 */
class TransportBuilder extends FrameworkTransportBuilder
{
    /**
     * Param that used for storing all message data until it will be used
     *
     * @var array
     */
    protected $messageData = [];

    /**
     * @var array
     */
    protected $messageBodyParts = [];

    /**
     * @var EmailMessageFactory
     */
    protected $emailMessageInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    protected $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    protected $mimePartInterfaceFactory;

    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @var HeaderInterface[]
     */
    private $headers;

    /**
     * TransportBuilder constructor.
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param EmailMessageFactory $emailMessageFactory
     * @param MimeMessageInterfaceFactory $mimeMessageInterfaceFactory
     * @param MimePartInterfaceFactory $mimePartInterfaceFactory
     * @param AddressConverter $addressConverter
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        EmailMessageFactory $emailMessageFactory,
        MimeMessageInterfaceFactory $mimeMessageInterfaceFactory,
        MimePartInterfaceFactory $mimePartInterfaceFactory,
        AddressConverter $addressConverter
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        $this->emailMessageInterfaceFactory = $emailMessageFactory;
        $this->mimeMessageInterfaceFactory = $mimeMessageInterfaceFactory;
        $this->mimePartInterfaceFactory = $mimePartInterfaceFactory;
        $this->addressConverter = $addressConverter;
    }

    /**
     * @inheritdoc
     */
    public function addCc($address, $name = '')
    {
        $this->addAddressByType('cc', $address, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTo($address, $name = '')
    {
        $this->addAddressByType('to', $address, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addBcc($address)
    {
        $this->addAddressByType('bcc', $address);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($email, $name = null)
    {
        $this->addAddressByType('replyTo', $email, $name);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws MailException
     */
    public function setFrom($from)
    {
        return $this->setFromByScope($from, null);
    }

    /**
     * @inheritdoc
     */
    public function setFromByScope($from, $scopeId = null)
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        $this->addAddressByType('from', $result['email'], $result['name']);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addAttachment(
        $body,
        $filename = null,
        $mimeType = Mime::TYPE_OCTETSTREAM,
        $disposition = Mime::DISPOSITION_ATTACHMENT,
        $encoding = Mime::ENCODING_BASE64
    ) {
        $attachmentPart = $this->mimePartInterfaceFactory->create(
            [
                'content' => $body,
                'type' => $mimeType,
                'fileName' => $filename,
                'disposition' => $disposition,
                'encoding' => $encoding,
            ]
        );
        $this->messageBodyParts[] = $attachmentPart;

        return $this;
    }

    /**
     * Add email headers
     *
     * @param HeaderInterface[] $header
     * @return $this
     */
    public function addHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function reset()
    {
        $this->messageData = [];
        $this->messageBodyParts = [];
        $this->headers = [];

        return parent::reset();
    }

    /**
     * @inheritdoc
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $content = $template->processTemplate();

        switch ($template->getType()) {
            case TemplateTypesInterface::TYPE_TEXT:
                $partType = MimeInterface::TYPE_TEXT;
                break;

            case TemplateTypesInterface::TYPE_HTML:
                $partType = MimeInterface::TYPE_HTML;
                break;

            default:
                throw new LocalizedException(
                    new \Magento\Framework\Phrase('Unknown template type')
                );
        }

        $contentPart = $this->mimePartInterfaceFactory->create(
            [
                'content' => $content,
                'type' => $partType
            ]);

        $this->messageBodyParts[] = $contentPart;

        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => $this->messageBodyParts]
        );

        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $this->messageData['subject'] = html_entity_decode(
            (string)$template->getSubject(),
            ENT_QUOTES
        );

        /** @var EmailMessage $message */
        $message = $this->emailMessageInterfaceFactory->create($this->messageData);
        if(!empty($this->headers)) {
            foreach ($this->headers as $header) {
                $message->setHeader($header);
            }
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Handles possible incoming types of email (string or array)
     *
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function addAddressByType(string $addressType, $email, ?string $name = null): void
    {
        if (is_string($email)) {
            $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
            return;
        }
        $convertedAddressArray = $this->addressConverter->convertMany($email);
        if (isset($this->messageData[$addressType])) {
            $this->messageData[$addressType] = array_merge(
                $this->messageData[$addressType],
                $convertedAddressArray
            );
        } else {
            $this->messageData[$addressType] = $convertedAddressArray;
        }
    }
}
