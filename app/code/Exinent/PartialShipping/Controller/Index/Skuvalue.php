<?php

namespace Exinent\PartialShipping\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;


class Skuvalue extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $productFactory;
    protected $productloader;

    public function __construct(
	    Context $context,
	    PageFactory $resultPageFactory,
	    JsonFactory $resultJsonFactory,
	    \Magento\Catalog\Api\ProductRepositoryInterface $productFactory,
	    \Magento\Catalog\Model\ProductFactory $productloader
	    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
	$this->productFactory = $productFactory;
	$this->productloader = $productloader;
        return parent::__construct($context);
    }

    public function execute() {   

	$i = 0;
	$result = $this->resultJsonFactory->create();
	$postvalue = $this->getRequest()->getPost();
		if($postvalue['is_ajax']==1){

			$spl = $postvalue['spl'];
			$product = $this->productloader->create()->load($spl);
			$proType = $product->getTypeId();
			if($proType=='simple'){
				$prosize = $product->getMeasurementSoldInSize();
				$result->setData(['size' => $prosize,'type'=>$proType]);
				return $result;
			}
			return;
		}
		$productid = $this->getRequest()->getParam('parentid');

	$simplesku = $this->getRequest()->getParam('simplesku');

	$product = $this->productloader->create()->load($productid);

	$pr = $this->productFactory->get($simplesku);
	 $_attributes = $product->getTypeInstance()->getConfigurableOptions($product);
	 $options = array();
	 $additionaldata = array();
	 
	$additionaldata['isBothExist'] = $pr->getHasHookAndLoopBoth();

	 foreach ($_attributes as $_attribute) {
	    foreach ($_attribute as $p) {
		$options[$p['sku']][$p['attribute_code']] = $p['option_title'];
	    }
	}
	foreach($options as $sku =>$d){
	    if($sku == $simplesku){
		foreach($d as $k => $v){
		    $_getMyAttr = $pr->getResource()->getAttribute($k);
		    $attrTestLabel = $_getMyAttr->getStoreLabel();
		    //echo $attrTestLabel.' - '.$v.' ';
		     $additionaldata[$i]['label'] = $attrTestLabel;
		     $additionaldata[$i]['value'] = $v;
		     $i++;
//		    //echo ' : '.$pr->getPrice()."\n";
		    //$result->setData([$attrTestLabel => $v]);
		}	

		
		$additionaldata[$i]['label'] = 'Brands';
		$additionaldata[$i]['value'] = $pr->getResource()->getAttribute('amazon_brand')->getFrontend()->getValue($pr);
		$i++;

		 $additionaldata[$i]['label'] = 'Measurement Sold In Unit';
                 $soldSize=$pr->getResource()->getAttribute('measurement_sold_in_unit')->getFrontend()->getValue($pr);
		  if (preg_match('/[0-9]/', $soldSize)) {
                    $additionaldata[$i]['value'] = $soldSize;
                } else {
                    $additionaldata[$i]['value'] = $soldSize;
                }
                 $i++;
                 $additionaldata[$i]['label'] = 'Soldunit';
                
                if (preg_match('/[0-9]/', $soldSize)) {
                    $additionaldata[$i]['value'] = 'Length: '.$soldSize;
                } else {
                    $additionaldata[$i]['value'] = 'Length: '.$soldSize;
                }
                 $i++;
                $additionaldata[$i]['label'] = 'Soldsize';
                $additionaldata[$i]['value']=$pr->getResource()->getAttribute('measurement_sold_in_size')->getFrontend()->getValue($pr);                
	    }
	}
	$result->setData(['result' => $additionaldata]);
	return $result;
    }

}
