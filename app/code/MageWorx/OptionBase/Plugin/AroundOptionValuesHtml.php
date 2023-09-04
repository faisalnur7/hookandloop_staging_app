<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionBase\Plugin;

use \Magento\Catalog\Block\Product\View\Options\Type\Select;
use MageWorx\OptionBase\Model\Product\Option\Value\AdditionalHtmlData;

/**
 * This plugin adds option_type_id to html elements.
 */
class AroundOptionValuesHtml
{
    protected AdditionalHtmlData $additionalHtmlData;

    public function __construct(
        AdditionalHtmlData $additionalHtmlData
    ) {
        $this->additionalHtmlData = $additionalHtmlData;
    }

    /**
     * @param Select $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetValuesHtml(Select $subject, \Closure $proceed)
    {
        $result = $proceed();
        $option = $subject->getOption();

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;

        $result = htmlentities($result);
        $result = htmlspecialchars_decode($result);

        libxml_use_internal_errors(true);
        $dom->loadHTML($result);
        libxml_clear_errors();

        foreach ($this->additionalHtmlData->getData() as $additionalHtmlItem) {
            $additionalHtmlItem->getAdditionalHtml($dom, $option);
        }

        $xpath = new \DOMXPath($dom);

        $count = 1;
        foreach ($option->getValues() as $value) {
            $count++;

            $select =
                $xpath->query('//option[@value="'.$value->getId().'"]')->item(0);

            $input =
                $xpath->query('//div/div[descendant::label[@for="options_'.$option->getId().'_'.$count.'"]]')->item(0);

            $element = $select ? $select : $input;

            if ($element) {
                $element->setAttribute("data-option_type_id", $value->getOptionTypeId());
            }
        }

        $resultBody = $dom->getElementsByTagName('body')->item(0);
        $result = $this->getInnerHtml($resultBody);
        return $result;
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    protected function getInnerHtml(\DOMElement $node)
    {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }

        return $innerHTML;
    }
}
