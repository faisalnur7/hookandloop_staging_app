<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Gateway\Listing\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Button\SplitButton;
use Aheadworks\Helpdesk2\Block\Adminhtml\Button\AbstractButton;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Type as GatewayTypeSource;

/**
 * Class AddNewGateway
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Gateway\Listing\Button
 */
class AddNewGateway extends AbstractButton
{
    /**
     * @var GatewayTypeSource
     */
    private $gatewayTypeSource;

    /**
     * @param Context $context
     * @param GatewayTypeSource $gatewayTypeSource
     */
    public function __construct(
        Context $context,
        GatewayTypeSource $gatewayTypeSource
    ) {
        $this->gatewayTypeSource = $gatewayTypeSource;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        $buttonData = [
            'id' => 'add_new_gateway',
            'label' => __('Add New Gateway'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => SplitButton::class,
            'options' => $this->getProductButtonOptions(),
        ];

        return $buttonData;
    }

    /**
     * Retrieve options for 'Add Gateway' split button
     *
     * @return array
     */
    private function getProductButtonOptions()
    {
        $splitButtonOptions = [];
        $types = $this->gatewayTypeSource->toOptionArray();

        foreach ($types as $type) {
            $splitButtonOptions[$type['value']] = [
                'label' => __($type['label']),
                'onclick' => "setLocation('" . $this->prepareUrl($type['value']) . "')",
                'default' => GatewayTypeSource::DEFAULT_TYPE == $type['value'],
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve gateway creation url by specified gateway type
     *
     * @param string $type
     * @return string
     */
    private function prepareUrl($type)
    {
        return $this->getUrl('aw_helpdesk2/gateway/new', ['type' => $type]);
    }
}
