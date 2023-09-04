<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Search Base for Magento 2
 */

namespace Amasty\Xsearch\Model\ResourceModel\Query;

class Collection extends \Magento\Search\Model\ResourceModel\Query\Collection
{
    /**
     * @param $termId
     * @return \Magento\Framework\DataObject[]
     */
    public function getRelatedTerms($termId)
    {
        $joinConditions = [
            'related_terms.related_term_id = main_table.query_id',
            $this->getConnection()->quoteInto('related_terms.term_id = ?', $termId)
        ];

        $this->getSelect()->joinInner(
            ['related_terms' => $this->getResource()->getTable('amasty_xsearch_related_term')],
            implode(' AND ', $joinConditions),
            []
        )->where('num_results > 0')->order('related_terms.position ASC');

        return $this->getItems();
    }
}
