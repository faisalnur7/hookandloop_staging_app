<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface;

class Sortable extends Field
{
    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public $element;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface
     */
    private $buttonProvider;

    /**
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Plumrocket\SocialLoginFree\Api\NetworkButtonProviderInterface $buttonProvider
     * @param array                                                          $data
     */
    public function __construct(
        Context $context,
        NetworkButtonProviderInterface $buttonProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->buttonProvider = $buttonProvider;
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('system/config/sortable.phtml');
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
        return $this->toHtml();
    }

    /**
     * Get default buttons list.
     *
     * @return \Plumrocket\SocialLoginFree\Api\Data\ButtonInterface[]
     */
    public function getButtons(): array
    {
        return $this->buttonProvider->getDefaultList();
    }
}
