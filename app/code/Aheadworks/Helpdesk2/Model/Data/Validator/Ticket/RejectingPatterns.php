<?php
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket;

use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\Data\Validator\Ticket\RejectingPatterns\ScopeDataResolver;
use Aheadworks\Helpdesk2\Model\Rejection\Pattern\Search\Builder as PatternSearchBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class RejectingPatterns
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket
 */
class RejectingPatterns extends AbstractValidator
{
    /**
     * @var PatternSearchBuilder
     */
    private $patternSearchBuilder;

    /**
     * @var ScopeDataResolver
     */
    private $dataResolver;

    /**
     * @var RejectingPatternInterface[]
     */
    private $patterns;

    /**
     * @param PatternSearchBuilder $patternSearchBuilder
     * @param ScopeDataResolver $dataResolver
     */
    public function __construct(
        PatternSearchBuilder $patternSearchBuilder,
        ScopeDataResolver $dataResolver
    ) {
        $this->patternSearchBuilder = $patternSearchBuilder;
        $this->dataResolver = $dataResolver;
    }

    /**
     * Check if rejected
     *
     * @param array $ticketData
     * @return bool
     * @throws LocalizedException
     */
    public function isValid($ticketData)
    {
        $patterns = $this->getPatterns();
        foreach ($patterns as $pattern) {
            if ($this->isMatching($pattern, $ticketData)) {
                $this->_addMessages([__('Ticket was rejected')]);
            }
        }

        return empty($this->getMessages());
    }

    /**
     * Get patterns
     *
     * @return RejectingPatternInterface[]
     * @throws LocalizedException
     */
    private function getPatterns()
    {
        if ($this->patterns === null) {
            $this->patterns = $this->patternSearchBuilder->addIsActiveFilter()->searchPatterns();
        }

        return $this->patterns;
    }

    /**
     * Check if data is matching pattern
     *
     * @param RejectingPatternInterface $pattern
     * @param array $ticketData
     * @return bool
     */
    private function isMatching($pattern, $ticketData)
    {
        foreach ($pattern->getScopeTypes() as $type) {
            if ($this->dataResolver->hasData($ticketData, $type)) {
                if (preg_match($pattern->getPattern(), (string)$this->dataResolver->getData($ticketData, $type))) {
                    return true;
                }
            }
        }

        return false;
    }
}
