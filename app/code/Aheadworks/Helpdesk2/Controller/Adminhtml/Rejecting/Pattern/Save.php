<?php
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Pattern;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Ui\DataProvider\Rejecting\Pattern\FormDataProvider as PatternFormDataProvider;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Pattern
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::rejecting_patterns';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CommandInterface $saveCommand
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CommandInterface $saveCommand
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->saveCommand = $saveCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                /** @var RejectingPatternInterface $pattern */
                $pattern = $this->saveCommand->execute($data);
                $this->dataPersistor->clear(PatternFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Pattern was successfully saved'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [RejectingPatternInterface::ID => $pattern->getId()]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the pattern'));
            }

            $this->dataPersistor->set(PatternFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            return $this->getPreparedRedirectOnError($resultRedirect, $data);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Retrieve prepared redirect to the entity form on error
     *
     * @param ResultRedirect $resultRedirect
     * @param array $data
     * @return ResultRedirect
     */
    private function getPreparedRedirectOnError($resultRedirect, $data)
    {
        $path = '*/*/new';
        $params = [
            '_current' => true,
        ];
        $patternId = $data[RejectingPatternInterface::ID] ?? null;
        if ($patternId) {
            $path = '*/*/edit';
            $params[RejectingPatternInterface::ID] = $patternId;
        }
        return $resultRedirect->setPath($path, $params);
    }
}
