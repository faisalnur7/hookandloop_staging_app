<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Field;
use Aheadworks\Helpdesk2\Model\Department\Search\Builder as SearchBuilder;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;

/**
 * Class AgentSelectField
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Ticket
 */
class AgentSelectField extends Field
{
    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @var AgentList
     */
    private $agentList;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SearchBuilder $searchBuilder
     * @param AgentList $agentList
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SearchBuilder $searchBuilder,
        AgentList $agentList,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->searchBuilder = $searchBuilder;
        $this->agentList = $agentList;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['optionSetArray'] = $this->getAgentOptionSet();
        $this->setData('config', $config);
    }

    /**
     * Get agent option set
     *
     * @return array
     * @throws LocalizedException
     */
    private function getAgentOptionSet()
    {
        $agentSource = [];
        $departmentList = $this->searchBuilder->searchDepartments();
        foreach ($departmentList as $department) {
            $agentIds = $department->getAgentIds();
            if ($department->getPrimaryAgentId() && !in_array($department->getPrimaryAgentId(), $agentIds)) {
                $agentIds[] = $department->getPrimaryAgentId();
            }
            $agentSource[$department->getId()] = $this->agentList->getOptionArrayByIds($agentIds);
        }

        return $agentSource;
    }
}
