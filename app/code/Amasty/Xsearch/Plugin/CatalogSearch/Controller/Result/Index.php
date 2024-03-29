<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Search Base for Magento 2
 */

namespace Amasty\Xsearch\Plugin\CatalogSearch\Controller\Result;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Search\Model\QueryFactory;

class Index
{
    /**
     * @var \Amasty\Xsearch\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Search\Helper\Data
     */
    private $searchHelper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        \Amasty\Xsearch\Helper\Data $helper,
        \Magento\Search\Helper\Data $searchHelper,
        \Magento\Framework\App\RequestInterface $request,
        Escaper $escaper,
        UrlInterface $urlBuilder
    ) {
        $this->helper = $helper;
        $this->searchHelper = $searchHelper;
        $this->request = $request;
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @return mixed|void
     */
    public function aroundExecute(
        $subject,
        \Closure $proceed
    ) {
        $seoKey = $this->helper->getSeoKey();
        $identifier = trim($this->request->getPathInfo(), '/');
        $identifier = explode('/', $identifier);
        $identifier = array_shift($identifier);
        $query = $this->request->getParam(QueryFactory::QUERY_VAR_NAME);

        if (!$this->request->isForwarded()
            && $this->helper->isSeoUrlsEnabled()
            && $seoKey
            && $seoKey != $identifier
            && $query
        ) {
            $query = $this->escaper->escapeUrl($query);
            // redirect to seo url
            $url = $this->searchHelper->getResultUrl($query);
            $subject->getResponse()->setRedirect($url);
        } else {
            return $proceed();
        }
    }
}
