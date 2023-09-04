<?php
namespace Aheadworks\Blog\Model\Import;

use Magento\Framework\Message\ManagerInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\Import;

/**
 * Class messageProvider
 */
class MessageManager
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * messageProvider constructor.
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
    }

    /**
     * Add Operation Result Messages
     *
     * @param ProcessingErrorAggregatorInterface $validationResult
     * @param Import $import
     */
    public function addOperationResultMessages($validationResult, $import)
    {
        if ($import->getProcessedRowsCount()) {
            if ($validationResult->isErrorLimitExceeded()) {
                $this->messageManager->addErrorMessage(
                    __('Data validation failed. Please fix the following errors and upload the file again.')
                );

                // errors info
                foreach ($validationResult->getRowsGroupedByErrorCode() as $errorMessage => $rows) {
                    $error = $errorMessage . ' ' . __('in row(s)') . ': ' . implode(', ', $rows);
                    $this->messageManager->addErrorMessage($error);
                }
            } else {
                if ($import->isImportAllowed()) {
                    $this->messageManager->addSuccessMessage(__('Your file was succesfully imported.'));
                } else {
                    $this->messageManager->addErrorMessage(
                        __('The file is valid, but we can\'t import it for some reason.')
                    );
                }
            }

            $message = __(
                'Checked rows: %1, checked entities: %2, invalid rows: %3, total errors: %4',
                $import->getProcessedRowsCount(),
                $import->getProcessedEntitiesCount(),
                $validationResult->getInvalidRowsCount(),
                $validationResult->getErrorsCount(
                    [
                        ProcessingError::ERROR_LEVEL_CRITICAL,
                        ProcessingError::ERROR_LEVEL_NOT_CRITICAL
                    ]
                )
            );

            $this->messageManager->addNoticeMessage($message);
        } else {
            $this->messageManager->addErrorMessage(__('This file does not contain any data.'));
        }
    }
}