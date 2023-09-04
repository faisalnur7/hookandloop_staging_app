<?php
namespace Aheadworks\Blog\ViewModel\Admin\System\Config;

use Aheadworks\Blog\Model\Serialize\Factory as SerializeFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Import
 */
class Import implements ArgumentInterface
{
    /**
     * @var SerializeFactory
     */
    private $serializer;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Import constructor.
     * @param SerializeFactory $serializer
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        SerializeFactory $serializer,
        RequestInterface $request,
        UrlInterface $urlBuilder
    ) {
        $this->serializer = $serializer->create();
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get script options
     *
     * @return bool|string
     */
    public function getScriptOptions()
    {
        $params = [
            'form' => [
                'method' => 'post',
                'enctype' => 'multipart/form-data',
                'action' => $this->getImportUrl()
            ],
            'sampleFilesBaseUrl' => $this->getSampleFilesBaseUrl()
        ];

        return $this->serializer->serialize($params);
    }

    /**
     * Get import url
     *
     * @return string
     */
    public function getImportUrl()
    {
        return $this->urlBuilder->getUrl(
            'aw_blog_admin/import/start',
            [
                '_current' => true,
                '_secure' => $this->request->isSecure()
            ]
        );
    }

    /**
     * Get Sample Files Base Url Url
     *
     * @return string
     */
    public function getSampleFilesBaseUrl()
    {
        return $this->urlBuilder->getUrl(
            '*/import/download/',
            ['filename' => 'entity-name']
        );
    }
}