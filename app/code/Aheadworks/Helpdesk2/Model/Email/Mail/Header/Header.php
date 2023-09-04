<?php
namespace Aheadworks\Helpdesk2\Model\Email\Mail\Header;

use Magento\Framework\DataObject;

/**
 * Class Header
 *
 * @package Aheadworks\Helpdesk2\Model\Email\Mail\Header
 */
class Header extends DataObject implements HeaderInterface
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }
}
