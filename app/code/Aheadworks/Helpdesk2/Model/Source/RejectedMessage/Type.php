<?php
namespace Aheadworks\Helpdesk2\Model\Source\RejectedMessage;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Type
 *
 * @package Aheadworks\Helpdesk2\Model\Source\RejectedMessage
 */
class Type implements OptionSourceInterface
{
    /**
     * Rejected message type values
     */
    const EMAIL = 'email';
    const CONTACT_US_FORM = 'contact_us_form';

    /**
     * @var array
     */
    private $options;

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options = [
                ['value' => self::EMAIL,  'label' => __('Email')],
                ['value' => self::CONTACT_US_FORM,  'label' => __('Contact Us form')],
            ];;
        }

        return $this->options;
    }
}
