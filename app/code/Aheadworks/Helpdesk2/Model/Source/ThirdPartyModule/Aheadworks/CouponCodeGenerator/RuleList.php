<?php
namespace Aheadworks\Helpdesk2\Model\Source\ThirdPartyModule\Aheadworks\CouponCodeGenerator;

use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker as ThirdPartyModuleChecker;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class RuleList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\ThirdPartyModule\Aheadworks\CouponCodeGenerator
 */
class RuleList implements OptionSourceInterface
{
    const EMPTY_VALUE = '';

    /**
     * @var ThirdPartyModuleChecker
     */
    private $thirdPartyModuleChecker;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $options;

    /**
     * @param ThirdPartyModuleChecker $thirdPartyModuleChecker
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ThirdPartyModuleChecker $thirdPartyModuleChecker,
        ObjectManagerInterface $objectManager
    ) {
        $this->thirdPartyModuleChecker = $thirdPartyModuleChecker;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options[] = [
                'value' => self::EMPTY_VALUE,
                'label' => __('Please Select a Rule')
            ];

            $this->options = array_merge($this->options, $this->getCouponRuleOptions());
        }

        return $this->options;
    }

    /**
     * Retrieve rule options from Aw Coupon code generator
     * 
     * @return array
     */
    private function getCouponRuleOptions()
    {
        $options = [];
        if ($this->thirdPartyModuleChecker->isAwCouponCodeGeneratorEnabled()) {
            /** @var OptionSourceInterface $optionSource */
            $optionSource = $this->objectManager->get(\Aheadworks\Coupongenerator\Model\Source\Rule\Name::class);
            $options = $optionSource->toOptionArray();
        }

        return $options;
    }
}
