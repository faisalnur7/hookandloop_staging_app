<?php

namespace Exinent\DimweightFedex\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'package_length',
                [
                    'group'             => 'Dimweight Dimensions',
                    'label'             => 'Package Length',
                    'type'              => 'text',
                    'input'             => 'text',
                    'default'           => '',
                    'class'             => '',
                    'backend'           => '',
                    'frontend'          => '',
                    'source'            => '',
                    'global'            => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => false,
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'visible_in_advanced_search' => false,
                    'unique'            => false
                ]
            )->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'package_width',
                [                   
                    'group'             => 'Dimweight Dimensions',
                    'label'             => 'Package Width',
                    'type'              => 'text',
                    'input'             => 'text',
                    'default'           => '',
                    'class'             => '',
                    'backend'           => '',
                    'frontend'          => '',
                    'source'            => '',
                    'global'            => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => false,
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'visible_in_advanced_search' => false,
                    'unique'            => false
                ]
            )->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'package_height',
                [
                    'group'             => 'Dimweight Dimensions',
                    'label'             => 'Package Height',
                    'type'              => 'text',
                    'input'             => 'text',
                    'default'           => '',
                    'class'             => '',
                    'backend'           => '',
                    'frontend'          => '',
                    'source'            => '',
                    'global'            => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => false,
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'visible_in_advanced_search' => false,
                    'unique'            => false
                ]
            );

        $setup->endSetup();
    }

}
