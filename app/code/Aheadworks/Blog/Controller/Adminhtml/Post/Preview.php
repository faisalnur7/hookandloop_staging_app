<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Post;

use Aheadworks\Blog\Model\StoreResolver;
use Magento\Backend\App\Action;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\PostPreviewManagementInterface;
use Aheadworks\Blog\Model\Preview\UrlBuilder as PreviewUrlBuilder;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Aheadworks\Blog\Model\Data\Processor\ProcessorInterface as DataProcessorInterface;

/**
 * Class Preview
 * @package Aheadworks\Blog\Controller\Adminhtml\Post
 */
class Preview extends Action
{
    private const ALL_STORE_VIEWS = '0';

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Blog::posts';

    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var PostPreviewManagementInterface
     */
    private $postPreviewManager;

    /**
     * @var PreviewUrlBuilder
     */
    private $previewUrlBuilder;

    /**
     * @var DataProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param FormKeyValidator $formKeyValidator
     * @param PostPreviewManagementInterface $postPreviewManager
     * @param PreviewUrlBuilder $previewUrlBuilder
     * @param DataProcessorInterface $postDataProcessor
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        FormKeyValidator $formKeyValidator,
        PostPreviewManagementInterface $postPreviewManager,
        PreviewUrlBuilder $previewUrlBuilder,
        DataProcessorInterface $postDataProcessor,
        StoreResolver $storeResolver
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->previewUrlBuilder = $previewUrlBuilder;
        $this->postPreviewManager = $postPreviewManager;
        $this->postDataProcessor = $postDataProcessor;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeResolver = $storeResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $result = [
            'error'     => true,
            'message'   => __('Unknown error occured!')
        ];

        if ($postData = $this->getRequest()->getParam('post_data')) {
            if ($postData && $this->formKeyValidator->validate($this->getRequest())) {
                $postData = $this->postDataProcessor->process($postData);
                $storeIds = isset($postData[PostInterface::STORE_IDS])
                    ? $postData[PostInterface::STORE_IDS]
                    : [];
                if (in_array(self::ALL_STORE_VIEWS, $storeIds)) {
                    $storeIds = $this->storeResolver->getAllStoreIds();
                }
                $postPreviewKey =  $this->postPreviewManager->savePreviewData($postData);

                $result = [
                    'error'     => false,
                    'message'   => __('Success.'),
                    'url'       => $this->previewUrlBuilder->getUrl(
                        'aw_blog/post/previewcontent',
                        is_array($storeIds) ? array_shift($storeIds) : $storeIds,
                        [
                            'data' => $postPreviewKey,
                            '_scope_to_url' => true,
                        ],
                        'frontend'
                    )];
            } else {
                $this->_forward('noroute');
            }
        }

        return $resultJson->setData($result);
    }
}
