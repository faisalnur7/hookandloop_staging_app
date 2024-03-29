<?php
namespace Aheadworks\Helpdesk2\Model\Source\Email;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Email\Model\ResourceModel\Template\Collection;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory;
use Magento\Email\Model\Template\Config as EmailConfig;

/**
 * Class TemplateList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Email
 */
class TemplateList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EmailConfig
     */
    private $emailConfig;

    /**
     * @var array
     */
    private $options;

    /**
     * @param CollectionFactory $collectionFactory
     * @param EmailConfig $emailConfig
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        EmailConfig $emailConfig
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->emailConfig = $emailConfig;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /** @var Collection $templateCollection */
            $templateCollection = $this->collectionFactory->create();
            $emailTemplates = $templateCollection->load()->toOptionArray();
            $this->options = array_merge($this->emailConfig->getAvailableTemplates(), $emailTemplates);
        }

        return $this->options;
    }
}
