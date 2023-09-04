<?php

namespace Exinent\Customzip\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\QuoteRepository;
use \Magento\Checkout\Model\Session;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $quoteRepository;
    protected $_checkoutSession;

    public function __construct(
        Context $context, PageFactory $resultPageFactory, JsonFactory $resultJsonFactory, QuoteRepository $quoteRepository, Session $_checkoutSession) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $_checkoutSession;
        return parent::__construct($context);
    }

    public function execute() {
        $result =  $this->resultJsonFactory->create();
        $post = $this->getRequest()->getPost();
        $postcode =$post['postcode'];
        $temp=false;
        if (strpos($postcode, ' ') !== false) {
            $pst = preg_replace('/\s+/', '%20', $postcode);
            $temp=true;

        }
        if($temp){
            $url = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$pst.'&key=AIzaSyDUNkPpkaRcljAa00ThJBJLoT9rZ7c1Uyw');
        }else{
            $url = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$postcode.'&key=AIzaSyDUNkPpkaRcljAa00ThJBJLoT9rZ7c1Uyw');
        }

        $array = json_decode($url,TRUE);

        $kkk='';
        $country='';
        if(!empty($array['results'])){
            foreach($array['results'][0]['address_components'] as $kk) {
                if(in_array('administrative_area_level_1',$kk['types'])){
                  $kkk  =  $kk['long_name'];
                }
                if(in_array('country',$kk['types'])){
                 $country  =  $kk['short_name'];
                }
            }
        }
        $response = ['success' => 'true','state'=>$kkk,'country'=>$country];

        $result->setData($response);

        return $result;

    }
}