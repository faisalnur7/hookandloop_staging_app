<?php
namespace Aheadworks\Blog\Block\Adminhtml\Widget\Instance\Custom;

use Aheadworks\Blog\Block\Adminhtml\Category\Tree;

/**
 * Class Categories
 */
class Categories extends Tree
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Blog::widget/instance/categories/tree.phtml';

    /**
     * Retrieve config
     *
     * @return string
     */
    public function getConfig()
    {
        $config = $this->jsonSerializer->unserialize(parent::getConfig());
        $config['treeConfig']['plugins'] = ['checkbox'];
        $config['treeConfig']['checkbox'] = ['three_state' => false];
        $config['initSelector'] = '.tree-init_' . $this->getId();
        $config = $this->setSelectedCategories($config);

        return $this->jsonSerializer->serialize($config);
    }

    /**
     * Set selected categories
     *
     * @param array $config
     * @return array
     */
    private function setSelectedCategories($config)
    {
        foreach ($this->getSelectedNodes() as $selectNode) {
            $selectNode = trim($selectNode);
            $index = array_search($selectNode, array_column($config['categories'], 'id'), true);
            if ($index !== false) {
                $config['categories'][$index]['state']['selected'] = true;
            }
        }

        return $config;
    }
}