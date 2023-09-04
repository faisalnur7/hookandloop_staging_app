<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element;

use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker as ThirdPartyModuleChecker;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class CouponCodeGenerator
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Ticket\Element
 */
class CouponCodeGenerator extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * @var ThirdPartyModuleChecker
     */
    private $thirdPartyModuleChecker;

    /**
     * CouponCodeGenerator constructor.
     *
     * @param ContextInterface $context
     * @param ThirdPartyModuleChecker $thirdPartyModuleChecker
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ThirdPartyModuleChecker $thirdPartyModuleChecker,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->thirdPartyModuleChecker = $thirdPartyModuleChecker;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        if (!$this->thirdPartyModuleChecker->isAwCouponCodeGeneratorEnabled()) {
            $config = $this->getData('config');
            $config['disabled'] = true;
            $config['visible'] = false;
            $this->setData('config', $config);
        }
    }
}
