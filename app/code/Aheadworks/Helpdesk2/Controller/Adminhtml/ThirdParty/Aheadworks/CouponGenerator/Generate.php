<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\Controller\Adminhtml\ThirdParty\Aheadworks\CouponGenerator;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Controller\Adminhtml\ActionWithJsonResponse;
use Aheadworks\Helpdesk2\Model\Result\JsonDataFactory as JsonDataFactory;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\ModuleChecker as ThirdPartyModuleChecker;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManagerInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface;
use Magento\Framework\Phrase;
use Magento\SalesRule\Model\Data\Rule;

/**
 * Class Generate
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\ThirdParty\Aheadworks\CouponCodeGenerator
 */
class Generate extends ActionWithJsonResponse
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Coupongenerator::generate_coupons';

    /**
     * @var ThirdPartyModuleChecker
     */
    private $thirdPartyModuleChecker;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @var PermissionManager
     */
    private $permissionManager;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param JsonDataFactory $jsonDataFactory
     * @param ThirdPartyModuleChecker $thirdPartyModuleChecker
     * @param ObjectManagerInterface $objectManager
     * @param TicketRepositoryInterface $ticketRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param TicketManagementInterface $ticketManagement
     * @param PermissionManager $permissionManager
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        JsonDataFactory $jsonDataFactory,
        ThirdPartyModuleChecker $thirdPartyModuleChecker,
        ObjectManagerInterface $objectManager,
        TicketRepositoryInterface $ticketRepository,
        CustomerRepositoryInterface $customerRepository,
        TicketManagementInterface $ticketManagement,
        PermissionManager $permissionManager,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context, $resultJsonFactory, $jsonDataFactory);
        $this->thirdPartyModuleChecker = $thirdPartyModuleChecker;
        $this->objectManager = $objectManager;
        $this->ticketRepository = $ticketRepository;
        $this->customerRepository = $customerRepository;
        $this->ticketManagement = $ticketManagement;
        $this->permissionManager = $permissionManager;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->thirdPartyModuleChecker->isAwCouponCodeGeneratorEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }
        return parent::dispatch($request);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $result = $this->createJsonDataObject();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $ticketId = $data['ticket_id'] ?? null;
                $ruleId = $data['rule_id'] ?? null;
                $isSendSeparateEmail = (bool)$data['send_separate_email'] ?? false;
                if (isset($ticketId)
                    && !$this->permissionManager->isAdminAbleToPerformTicketAction(
                        $ticketId,
                        DepartmentPermissionInterface::TYPE_UPDATE
                    )) {
                    return $this->createErrorResponse(__('Not enough permissions to generate coupon'));
                }
                if (!$ruleId) {
                    throw new LocalizedException(__('Please select rule'));
                }

                $ticket = $this->ticketRepository->getById($ticketId);
                try {
                    $customer = $this->customerRepository->getById($ticket->getCustomerId());
                    $generationResult = $this->getCouponManager()->generateForCustomer(
                        $ruleId,
                        $customer->getId(),
                        $isSendSeparateEmail
                    );
                } catch (NoSuchEntityException $e) {
                    $generationResult = $this->getCouponManager()->generateForEmail(
                        $ruleId,
                        $ticket->getCustomerEmail(),
                        $isSendSeparateEmail
                    );
                }

                if ($generationResult->getCoupon()) {
                    $ticket->setInternalNote(implode('. ', $generationResult->getMessages()));
                    $this->ticketManagement->updateTicket($ticket);
                    $rule = $this->ruleRepository->getById($generationResult->getCoupon()->getRuleId());
                    $result
                        ->setMessages($generationResult->getMessages())
                        ->setData([
                            TicketInterface::INTERNAL_NOTE => $ticket->getInternalNote(),
                            'couponMessage' => $this->getCouponMessage(
                                $generationResult,
                                $rule
                            ),
                            'discountMessage' => $this->getDiscountMessage(
                                $generationResult,
                                $rule
                            )
                        ]);
                    return $this->createResponse($result);
                }
            } catch (LocalizedException $e) {
                $result->addMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $result->addMessage($e->getMessage());
            } catch (\Exception $e) {
                $result->addMessage(__('Something went wrong while generated the coupon code'));
            }
        } else {
            $result->addMessage(__('Something went wrong while generated the coupon code'));
        }

        return $this->createResponse($result->setError());
    }

    /**
     * Retrieve coupon management
     *
     * @return \Aheadworks\Coupongenerator\Api\CouponManagerInterface
     */
    private function getCouponManager()
    {
        return $this->objectManager->get(\Aheadworks\Coupongenerator\Api\CouponManagerInterface::class);
    }

    /**
     * Get coupon message
     *
     * @param CouponGenerationResultInterface $generationResult
     * @param Rule $rule
     * @return Phrase
     */
    private function getCouponMessage(CouponGenerationResultInterface $generationResult, Rule $rule): Phrase
    {
        $code = $generationResult->getCoupon()->getCode();
        return __('Your coupon code is %1', $code);
    }

    /**
     * Get discount message
     *
     * @param CouponGenerationResultInterface $generationResult
     * @param Rule $rule
     * @return Phrase
     */
    private function getDiscountMessage(CouponGenerationResultInterface $generationResult, Rule $rule): Phrase
    {
        $discountAmount = round((float)$rule->getDiscountAmount(), 2);
        if ($rule->getSimpleAction() == 'by_percent') {
            $discountAmount .= '%';
        }
        return __('The coupon will give you %1 discount', $discountAmount);
    }
}
