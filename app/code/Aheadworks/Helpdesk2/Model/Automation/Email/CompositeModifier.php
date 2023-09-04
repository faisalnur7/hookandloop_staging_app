<?php
namespace Aheadworks\Helpdesk2\Model\Automation\Email;

/**
 * Class CompositeModifier
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email
 */
class CompositeModifier implements ModifierInterface
{
    /**
     * @var ModifierInterface[]
     */
    private $modifierList;

    /**
     * @param ModifierInterface[] $modifierList
     */
    public function __construct(array $modifierList = [])
    {
        $this->modifierList = $modifierList;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        foreach ($this->modifierList as $modifier) {
            if (!$modifier instanceof ModifierInterface) {
                throw new \InvalidArgumentException(
                    __('Email meta data modifier must implement %1', ModifierInterface::class)
                );
            }
            $emailMetadata = $modifier->addMetadata($emailMetadata, $eventData);
        }

        return $emailMetadata;
    }
}
