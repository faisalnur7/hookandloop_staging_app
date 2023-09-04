<?php
namespace Magecomp\Deleteorder\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magecomp\Deleteorder\Model\OrderFactory;

class DeleteOrderid implements \Magecomp\Deleteorder\Api\DeleteorderInterface
{

    protected $helper;
    protected $store;
    protected $_modelOrderFactory;

    public function __construct(
        \Magecomp\Deleteorder\Helper\Data $helper,
        \Magento\Store\Model\App\Emulation $store,
        OrderFactory $modelOrderFactory
    ) {
        $this->helper = $helper;
        $this->store=$store;
        $this->_modelOrderFactory = $modelOrderFactory;
    }

    public function getOrderid($orderid)
    {
        try {
            $return=$this->helper->getEntityid();
            foreach ($return as $item) {
                if ($item==$orderid) {
                    $this->_modelOrderFactory->create()->deleteOrder([$orderid]);
                    $response = ['data' => 'Sucessfully Deleted'];
                    break;
                } else {
                    $response = ['data' => 'Invalid Order Id'];
                }
            }


        } catch (\Exception $e) {
            $response=['return' => $e->getMessage()];
        }
        return json_encode($response);
    }
}
