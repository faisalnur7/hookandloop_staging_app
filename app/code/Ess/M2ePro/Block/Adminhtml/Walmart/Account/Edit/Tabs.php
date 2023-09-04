<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Walmart\Account\Edit;

use Ess\M2ePro\Block\Adminhtml\Magento\Tabs\AbstractTabs;

class Tabs extends AbstractTabs
{
    public const TAB_ID_GENERAL = 'general';
    public const TAB_ID_LISTING_OTHER = 'listingOther';
    public const TAB_ID_ORDERS = 'orders';
    public const TAB_ID_INVOICES_AND_SHIPMENTS = 'invoices_and_shipments';

    protected function _construct()
    {
        parent::_construct();

        $this->setId('walmartAccountEditTabs');
        $this->setDestElementId('edit_form');
    }

    protected function _prepareLayout()
    {
        $this->addTab(
            self::TAB_ID_GENERAL,
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()
                                  ->createBlock(\Ess\M2ePro\Block\Adminhtml\Walmart\Account\Edit\Tabs\General::class)
                                  ->toHtml(),
            ]
        );

        $this->addTab(
            self::TAB_ID_LISTING_OTHER,
            [
                'label' => __('Unmanaged Listings'),
                'title' => __('Unmanaged Listings'),
                'content' => $this->getLayout()
                                  ->createBlock(
                                      \Ess\M2ePro\Block\Adminhtml\Walmart\Account\Edit\Tabs\ListingOther::class
                                  )
                                  ->toHtml(),
            ]
        );

        $this->addTab(
            self::TAB_ID_ORDERS,
            [
                'label' => __('Orders'),
                'title' => __('Orders'),
                'content' => $this->getLayout()
                                  ->createBlock(\Ess\M2ePro\Block\Adminhtml\Walmart\Account\Edit\Tabs\Order::class)
                                  ->toHtml(),
            ]
        );

        $this->addTab(
            self::TAB_ID_INVOICES_AND_SHIPMENTS,
            [
                'label' => __('Invoices & Shipments'),
                'title' => __('Invoices & Shipments'),
                'content' => $this->getLayout()
                                  ->createBlock(
                                      \Ess\M2ePro\Block\Adminhtml\Walmart\Account\Edit\Tabs\InvoicesAndShipments::class
                                  )
                                  ->toHtml(),
            ]
        );

        $this->setActiveTab($this->getRequest()->getParam('tab', self::TAB_ID_GENERAL));

        return parent::_prepareLayout();
    }
}
