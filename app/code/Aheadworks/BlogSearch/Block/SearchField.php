<?php
namespace Aheadworks\BlogSearch\Block;

use Magento\Framework\View\Element\Template;
use Aheadworks\BlogSearch\Model\SearchAllowedChecker;

/**
 * Class SearchField
 */
class SearchField extends Template
{
    /**
     * @var SearchAllowedChecker
     */
    private $searchAllowedChecker;

    /**
     * SearchField constructor.
     * @param Template\Context $context
     * @param SearchAllowedChecker $searchAllowedChecker
     * @param array $data
     */
    public function __construct
    (
        Template\Context $context,
        SearchAllowedChecker $searchAllowedChecker,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->searchAllowedChecker = $searchAllowedChecker;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->searchAllowedChecker->execute()) {
            return '';
        }
        return parent::_toHtml();
    }
}
