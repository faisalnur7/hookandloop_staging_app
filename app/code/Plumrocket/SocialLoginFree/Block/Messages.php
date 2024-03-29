<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Block;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;

class Messages extends \Magento\Framework\View\Element\Messages
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Framework\Message\NoticeFactory
     */
    private $noticeFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                        $context
     * @param \Magento\Framework\Message\Factory                                      $messageFactory
     * @param \Magento\Framework\Message\CollectionFactory                            $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface                             $messageManager
     * @param \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $interpretationStrategy
     * @param \Magento\Customer\Model\Session                                         $customerSession
     * @param \Magento\Store\Model\Store                                              $store
     * @param \Magento\Framework\Message\NoticeFactory                                $noticeFactory
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail                $fakeEmail
     * @param \Plumrocket\SocialLoginFree\Helper\Config                               $config
     * @param array                                                                   $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\Message\NoticeFactory $noticeFactory,
        FakeEmail $fakeEmail,
        \Plumrocket\SocialLoginFree\Helper\Config $config,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
        $this->customerSession = $customerSession;
        $this->store = $store;
        $this->noticeFactory = $noticeFactory;
        $this->fakeEmail = $fakeEmail;
        $this->config = $config;
    }

    protected function _prepareLayout()
    {
        if ($this->config->isModuleEnabled()) {
            $this->_fakeEmailMessage();
            $this->addMessages($this->messageManager->getMessages(true));
        }
        return parent::_prepareLayout();
    }

    protected function _fakeEmailMessage()
    {
        // Check email.
        $requestString = $this->_request->getRequestString();
        $module = $this->_request->getModuleName();

        $editUri = 'customer/account/edit';

        switch (true) {

            case (stripos($requestString, 'customer/account/logout') !== false
                || stripos($requestString, 'customer/section/load') !== false):
                break;

            case $moduleName = (stripos($module, 'customer') !== false) ? 'customer' : null:
                if ($this->customerSession->isLoggedIn() &&
                    $this->fakeEmail->detect($this->customerSession->getCustomer()->getEmail())
                ) {
                    $this->messageManager->getMessages()->deleteMessageByIdentifier('fakeemail');
                    $message = $this->getChangeEmailWithLinkMessage($editUri);

                    if ('customer' === $moduleName) {
                        if (stripos($requestString, $editUri) !== false) {
                            $message = $this->getChangeEmailWithoutLinkMessage();
                        }
                        $noticeMessage = $this->noticeFactory->create(['text' => $message])->setIdentifier('fakeemail');
                        $this->messageManager->addUniqueMessages([$noticeMessage]);
                    }

                }
                break;
        }
    }

    /**
     * @param string $editUri
     * @return \Magento\Framework\Phrase
     */
    protected function getChangeEmailWithLinkMessage(string $editUri): Phrase
    {
        return __(
            'Your account needs to be updated. The email address in your profile is invalid. ' .
            'Please indicate your valid email address by going to the <a href="%1">Account edit page</a>',
            $this->store->getUrl($editUri)
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getChangeEmailWithoutLinkMessage(): Phrase
    {
        return __('Your account needs to be updated. The email address in your profile is invalid. ' .
                  'Please indicate your valid email address.');
    }
}
