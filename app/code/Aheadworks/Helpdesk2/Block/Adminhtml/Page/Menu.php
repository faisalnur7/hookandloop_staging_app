<?php
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Page;

use Magento\Backend\Block\Template;

/**
 * Class Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Page
 */
class Menu extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_Helpdesk2::page/menu.phtml';
}
