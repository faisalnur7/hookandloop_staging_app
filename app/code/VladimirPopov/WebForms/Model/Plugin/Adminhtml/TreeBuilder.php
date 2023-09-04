<?php
/**
 * @author      Vladimir Popov
 * @copyright   Copyright © 2020 Vladimir Popov. All rights reserved.
 */

namespace VladimirPopov\WebForms\Model\Plugin\Adminhtml;

use Magento\Framework\Acl\AclResource\Provider;
use VladimirPopov\WebForms\Model\ResourceModel\Form\CollectionFactory;

class TreeBuilder
{
    protected $_formCollectionFactory;

    public function __construct(
        CollectionFactory $formCollectionFactory
    )
    {
        $this->_formCollectionFactory = $formCollectionFactory;
    }

//    public function beforeBuild(\Magento\Framework\Acl\AclResource\TreeBuilder $treeBuilder, $resourceList)
//    {
//        foreach ($resourceList as $i => $list) {
//            if ($list['id'] == 'VladimirPopov_WebForms::webforms') {
//                $resourceList[$i]['children'] = array_merge($list['children'], $this->getChildren());
//            }
//        }
//        return [$resourceList];
//    }

    protected function getChildren()
    {
        $formList = [];
        $collection = $this->_formCollectionFactory->create()
            ->addOrder('name', 'asc');
        $i = 1;
        foreach ($collection as $form) {
            $formList[] = [
                "id" => "VladimirPopov_WebForms::form" . $form->getId(),
                "title" => $form->getName(),
                "sortOrder" => $i,
                "disabled" => false,
                "children" => []
            ];
            $i++;
        }
        return $formList;
    }

    public function afterGetAclResources(Provider $provider, $tree){
        if(count($tree)){
            foreach($tree as &$resourceList){
                foreach ($resourceList['children'] as &$resourceList2) {
                    if ($resourceList2['id'] == 'Magento_Backend::content') {
                        foreach($resourceList2['children'] as &$resourceList3)
                            if ($resourceList3['id'] == 'VladimirPopov_WebForms::webforms') {
                                $resourceList3['children'] = array_merge($resourceList3['children'], $this->getChildren());
                            }
                    }
                }
            }
        }
        return $tree;
    }
}
