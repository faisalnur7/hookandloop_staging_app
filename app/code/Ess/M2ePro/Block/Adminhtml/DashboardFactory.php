<?php

namespace Ess\M2ePro\Block\Adminhtml;

class DashboardFactory
{
    public function create(
        string $activeComponentNick,
        \Magento\Framework\View\LayoutInterface $layout,
        \Ess\M2ePro\Model\Dashboard\Sales\CalculatorInterface $salesCalculator,
        \Ess\M2ePro\Model\Dashboard\Products\CalculatorInterface $productsCalculator,
        \Ess\M2ePro\Model\Dashboard\Shipments\CalculatorInterface $shipmentsCalculator,
        \Ess\M2ePro\Block\Adminhtml\Dashboard\Shipments\UrlStorageInterface $shipmentsUrlStorage,
        \Ess\M2ePro\Model\Dashboard\Errors\CalculatorInterface $errorsCalculator,
        \Ess\M2ePro\Block\Adminhtml\Dashboard\Errors\UrlStorageInterface $errorsUrlStorage,
        \Ess\M2ePro\Model\Dashboard\ListingProductIssues\CalculatorInterface $listingProductIssuesCalculator = null
    ): Dashboard {
        $allowedNicks = [
            \Ess\M2ePro\Helper\Component\Ebay::NICK,
            \Ess\M2ePro\Helper\Component\Amazon::NICK,
            \Ess\M2ePro\Helper\Component\Walmart::NICK,
        ];

        if (!in_array($activeComponentNick, $allowedNicks)) {
            throw new \Ess\M2ePro\Model\Exception\Logic('Invalid component nick');
        }

        /** @var Dashboard\ComponentTabs $componentTabs */
        $componentTabs = $layout->createBlock(Dashboard\ComponentTabs::class);
        $componentTabs->setActiveComponentNick($activeComponentNick);

        /** @var Dashboard\Sales $sales */
        $sales = $layout->createBlock(Dashboard\Sales::class, 'dashboard_sales', [
            'calculator' => $salesCalculator,
        ]);

        /** @var Dashboard\Products $products */
        $products = $layout->createBlock(Dashboard\Products::class, 'dashboard_products', [
            'calculator' => $productsCalculator,
        ]);

        /** @var Dashboard\Shipments $shipments */
        $shipments = $layout->createBlock(Dashboard\Shipments::class, 'dashboard_shipments', [
            'calculator' => $shipmentsCalculator,
            'urlStorage' => $shipmentsUrlStorage,
        ]);

        /** @var Dashboard\Errors $errors */
        $errors = $layout->createBlock(Dashboard\Errors::class, 'dashboard_errors', [
            'calculator' => $errorsCalculator,
            'urlStorage' => $errorsUrlStorage,
        ]);

        /** @var Dashboard\ListingProductIssues $listingProductIssues */
        $listingProductIssues = $layout->createBlock(
            Dashboard\ListingProductIssues::class,
            'dashboard_listing_products_issues',
            [
                'componentNick' => $activeComponentNick,
                'productsCalculator' => $productsCalculator,
                'issuesCalculator' => $listingProductIssuesCalculator,
            ]
        );

        /** @var Dashboard $dashboard */
        $dashboard = $layout->createBlock(Dashboard::class, 'dashboard', [
            'componentTabs' => $componentTabs,
            'sales' => $sales,
            'products' => $products,
            'shipments' => $shipments,
            'errors' => $errors,
            'listingProductIssues' => $listingProductIssues,
        ]);

        $dashboard->appendHelpBlock([
            'content' => $this->getHelpBlockHtml(),
        ]);

        return $dashboard;
    }

    private function getHelpBlockHtml(): string
    {
        return __(
            '<p>The dashboard is to give you an instant overview of your key performance(s).</p>
            <p>With the help of the dashboard, you get a quick and clear insight into how your sales
            and shipments are doing and whether there are any issues that block updates of your items.</p>'
        );
    }
}
