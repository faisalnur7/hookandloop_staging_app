<?php
namespace Exinent\TaxExempt\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{

protected $customerSetupFactory;
protected $attributeSetFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
      
        if (version_compare($context->getVersion(), '1.1.0', '<')) {

            $customerSetup->addAttribute(
                    \Magento\Customer\Model\Customer::ENTITY, 'tax_exempt_number', [
                        'type' => 'text',
                        'label' => 'Tax Exempt Number',
                        'input' => 'text',
                        'required' => false,
                        'visible' => true,
                        'user_defined' => true,
                        'sort_order' => 1000,
                        'position' => 1000,
                        'system' => 0,
                    ]
                );
            $Attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'tax_exempt_number')
            ->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_edit','adminhtml_checkout'],
            ]);

            $Attribute->save();

            $customerSetup->addAttribute(Customer::ENTITY, 'tax_exempt_state', [
                'type' => 'text',
                'label' => 'Tax Exempt State',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 1000,
                'position' => 1000,
                'system' => 0,
            ]);

            $Attribute2 = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'tax_exempt_state')
            ->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_edit','adminhtml_checkout'],
            ]);

            $Attribute2->save();

        }

        $setup->endSetup();
    }
}

