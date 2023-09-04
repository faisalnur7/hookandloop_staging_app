<?php
namespace Aheadworks\Helpdesk2\Model\Email\Mail;

use Aheadworks\Helpdesk2\Model\Email\Mail\Header\HeaderInterface;

/**
 * Class EmailMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Email\Mail
 */
class EmailMessage extends \Magento\Framework\Mail\EmailMessage
{
    /**
     * Set email header
     *
     * @param HeaderInterface $header
     * @return $this
     */
    public function setHeader($header)
    {
        $headers = $this->zendMessage->getHeaders();
        if ($headers->has($header->getName())) {
            $headers->removeHeader($header->getName());
        }
        $headers->addHeaderLine($header->getName(), $header->getValue());

        return $this;
    }
}
