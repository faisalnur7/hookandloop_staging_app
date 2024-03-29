<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Connector\Command\Pending\Processing;

/**
 * Class \Ess\M2ePro\Model\Connector\Command\Pending\Processing\Single
 */
class Single extends \Ess\M2ePro\Model\ActiveRecord\AbstractModel
{
    /** @var \Ess\M2ePro\Model\Processing $processing */
    protected $processing = null;

    /** @var \Ess\M2ePro\Model\Request\Pending\Single $requestPendingSingle */
    protected $requestPendingSingle = null;

    //########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Ess\M2ePro\Model\ResourceModel\Connector\Command\Pending\Processing\Single::class);
    }

    //########################################

    public function getProcessing()
    {
        if ($this->processing !== null) {
            return $this->processing;
        }

        return $this->processing = $this->activeRecordFactory->getObjectLoaded(
            'Processing',
            $this->getProcessingId(),
            null,
            false
        );
    }

    public function getRequestPendingSingle()
    {
        if ($this->requestPendingSingle !== null) {
            return $this->requestPendingSingle;
        }

        return $this->requestPendingSingle = $this->activeRecordFactory->getObjectLoaded(
            'Request_Pending_Single',
            $this->getRequestPendingSingleId(),
            null,
            false
        );
    }

    //########################################

    public function getProcessingId()
    {
        return (int)$this->getData('processing_id');
    }

    public function getRequestPendingSingleId()
    {
        return (int)$this->getData('request_pending_single_id');
    }

    //########################################
}
