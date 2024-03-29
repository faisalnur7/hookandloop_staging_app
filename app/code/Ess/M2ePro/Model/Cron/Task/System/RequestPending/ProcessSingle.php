<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model\Cron\Task\System\RequestPending;

class ProcessSingle extends \Ess\M2ePro\Model\Cron\Task\AbstractModel
{
    public const NICK = 'system/request_pending/process_single';

    public const STATUS_NOT_FOUND = 'not_found';
    public const STATUS_COMPLETE = 'completed';
    public const STATUS_PROCESSING = 'processing';

    public const MAX_HASHES_PER_REQUEST = 100;

    //####################################

    public function isPossibleToRun()
    {
        if ($this->getHelper('Server\Maintenance')->isNow()) {
            return false;
        }

        return parent::isPossibleToRun();
    }

    //####################################

    protected function performActions()
    {
        $this->removeOutdated();
        $this->completeExpired();
        $this->executeInProgress();
    }

    //####################################

    protected function removeOutdated()
    {
        $requestPendingSingleCollection = $this->activeRecordFactory->getObject('Request_Pending_Single')
                                                                    ->getCollection();
        $requestPendingSingleCollection->setOnlyOutdatedItemsFilter();
        $requestPendingSingleCollection->addFieldToFilter('is_completed', 1);

        /** @var \Ess\M2ePro\Model\Request\Pending\Single[] $requestsPendingSingleObjects */
        $requestsPendingSingleObjects = $requestPendingSingleCollection->getItems();

        foreach ($requestsPendingSingleObjects as $requestsPendingSingleObject) {
            $requestsPendingSingleObject->delete();
        }
    }

    protected function completeExpired()
    {
        $requestPendingSingleCollection = $this->activeRecordFactory->getObject('Request_Pending_Single')
                                                                    ->getCollection();
        $requestPendingSingleCollection->setOnlyExpiredItemsFilter();
        $requestPendingSingleCollection->addFieldToFilter('is_completed', 0);

        /** @var \Ess\M2ePro\Model\Request\Pending\Single[] $expiredRequestPendingSingleObjects */
        $expiredRequestPendingSingleObjects = $requestPendingSingleCollection->getItems();

        foreach ($expiredRequestPendingSingleObjects as $requestPendingSingle) {
            $this->completeRequest($requestPendingSingle, [], [$this->getFailedMessage()->asArray()]);
        }
    }

    protected function executeInProgress()
    {
        $componentsInProgress = $this->activeRecordFactory->getObject('Request_Pending_Single')->getResource()
                                                          ->getComponentsInProgress();

        foreach ($componentsInProgress as $component) {
            $requestPendingSingleCollection = $this->activeRecordFactory->getObject('Request_Pending_Single')
                                                                        ->getCollection();
            $requestPendingSingleCollection->addFieldToFilter('component', $component);
            $requestPendingSingleCollection->addFieldToFilter('is_completed', 0);

            $serverHashes = $requestPendingSingleCollection->getColumnValues('server_hash');
            $serverHashesPacks = array_chunk($serverHashes, self::MAX_HASHES_PER_REQUEST);

            foreach ($serverHashesPacks as $serverHashesPack) {
                $results = $this->getResultsFromServer($component, $serverHashesPack);

                foreach ($serverHashesPack as $serverHash) {
                    /** @var \Ess\M2ePro\Model\Request\Pending\Single $requestPendingSingle */
                    $requestPendingSingle = $requestPendingSingleCollection->getItemByColumnValue(
                        'server_hash',
                        $serverHash
                    );

                    if (
                        !isset($results[$serverHash]['status']) ||
                        $results[$serverHash]['status'] == self::STATUS_NOT_FOUND
                    ) {
                        $this->completeRequest(
                            $requestPendingSingle,
                            [],
                            [$this->getFailedMessage()->asArray()]
                        );
                        continue;
                    }

                    if ($results[$serverHash]['status'] != self::STATUS_COMPLETE) {
                        continue;
                    }

                    $data = [];
                    if (isset($results[$serverHash]['data'])) {
                        $data = $results[$serverHash]['data'];
                    }

                    $messages = [];
                    if (isset($results[$serverHash]['messages'])) {
                        $messages = $results[$serverHash]['messages'];
                    }

                    $this->completeRequest($requestPendingSingle, $data, $messages);
                }
            }
        }
    }

    //####################################

    protected function getResultsFromServer($component, array $serverHashes)
    {
        $dispatcher = $this->modelFactory->getObject(ucfirst($component) . '\Connector\Dispatcher');
        $connector = $dispatcher->getVirtualConnector(
            'processing',
            'get',
            'results',
            ['processing_ids' => $serverHashes],
            'results',
            null,
            null
        );

        $dispatcher->process($connector);

        return $connector->getResponseData();
    }

    protected function getFailedMessage()
    {
        /** @var \Ess\M2ePro\Model\Connector\Connection\Response\Message $message */
        $message = $this->modelFactory->getObject('Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            'Request wait timeout exceeded.',
            \Ess\M2ePro\Model\Connector\Connection\Response\Message::TYPE_ERROR
        );

        return $message;
    }

    protected function completeRequest(
        \Ess\M2ePro\Model\Request\Pending\Single $requestPendingSingle,
        array $data,
        array $messages
    ) {
        $requestPendingSingle->setSettings('result_data', $data);
        $requestPendingSingle->setSettings('result_messages', $messages);

        $requestPendingSingle->setData('is_completed', 1);

        $requestPendingSingle->save();
    }

    //####################################
}
