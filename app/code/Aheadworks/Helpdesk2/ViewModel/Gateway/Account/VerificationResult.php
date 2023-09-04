<?php
declare(strict_types=1);

namespace Aheadworks\Helpdesk2\ViewModel\Gateway\Account;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Model\Data\Command\Gateway\VerifyAccount;

/**
 * Verification result view model
 */
class VerificationResult implements ArgumentInterface
{
    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $sessionManagement;

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $jsonSerializer;

    /**
     * @param SessionManagerInterface $sessionManagement
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        SessionManagerInterface $sessionManagement,
        JsonSerializer $jsonSerializer
    ) {
        $this->sessionManagement = $sessionManagement;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Get verification result
     *
     * @return string
     */
    public function getResult(): string
    {
        $result = $this->sessionManagement->getData(VerifyAccount::IS_VERIFIED) ?: false;
        if ($message = $this->sessionManagement->getData(VerifyAccount::ACCOUNT_VERIFY_ERROR)) {
            $result = ['error' => $message];
            $this->sessionManagement->unsetData(VerifyAccount::ACCOUNT_VERIFY_ERROR);
        }

        return $this->jsonSerializer->serialize($result);
    }
}
