<?php
namespace Aheadworks\Blog\ViewModel\Admin\Widget\Instance\Edit\Tab\Main;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Blog\Model\Widget\Instance;
use Aheadworks\Blog\Model\Serialize\Factory as SerializeFactory;

/**
 * Class WidgetInstance
 */
class WidgetInstance implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var SerializeFactory
     */
    private $serializer;

    /**
     * @var Instance
     */
    private $widgetInstance;

    /**
     * Renderer constructor.
     * @param UrlInterface $url
     * @param SerializeFactory $serializer
     * @param Instance $widgetInstance
     */
    public function __construct(
        UrlInterface $url,
        SerializeFactory $serializer,
        Instance $widgetInstance
    ) {
        $this->url = $url;
        $this->widgetInstance = $widgetInstance;
        $this->serializer = $serializer->create();
    }

    /**
     * Retrieve Script Options
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $params = [
            'displayOnContainers' => $this->widgetInstance->getDisplayOnContainersData(),
            'baseChooserUrl' => $this->getBaseChooserUrl()
        ];

        return $this->serializer->serialize($params);
    }

    /**
     * Retrieve Display On Containers Data
     *
     * @return array
     */
    public function getDisplayOnContainersData()
    {
        return $this->widgetInstance->getDisplayOnContainersData();
    }

    /**
     * Retrieve Display On Options Data
     *
     * @return array
     */
    public function getDisplayOnOptionsData()
    {
        return $this->widgetInstance->getDisplayOnOptionsData();
    }

    /**
     * Retrieve Base Chooser Url
     *
     * @return string
     */
    public function getBaseChooserUrl()
    {
        return $this->url->getUrl(
            'aw_blog_admin/widget/display'
        );
    }
}