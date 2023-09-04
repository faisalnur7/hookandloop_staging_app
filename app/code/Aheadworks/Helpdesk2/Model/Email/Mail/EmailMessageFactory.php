<?php
namespace Aheadworks\Helpdesk2\Model\Email\Mail;

use Magento\Framework\ObjectManagerInterface;

class EmailMessageFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return EmailMessage
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create(EmailMessage::class, $data);
    }
}
