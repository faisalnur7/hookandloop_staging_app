<?php
namespace Ravedigital\CoreUpdate\Block;

/**
 * Class View
 * @package Vsourz\HtmlSitemap\Block
 */
 
 // This file is override for the module vsourz_htmlsitemap
 //Used in listing of category only on sitemap page
class CategoryList extends \Vsourz\HtmlSitemap\Block\CategoryList
{
    
   
    public function catagorylistrecursiveHtml($categoryy)
    {
        $html = '';

        $html .= '<ul class="cms-page-list">';
        foreach ($categoryy as $catt) :
            $haschildren = '';
            if (count($catt->getChildrenCategories())) {
                $haschildren = ' isparent';
            }

            $html .= '<li class="category-item level-' . ($catt->getLevel() - 1) . $haschildren .'">';
                $html .= '<a href="'.$catt->getUrl(). '" title="' . $catt->getName() . '">';
                    $html .= $catt->getName();
                $html .= '</a>';
            if ($haschildren) :
                $html .= $this->catagorylistrecursiveHtml($catt->getChildrenCategories());
            endif;
                $html .= '</li>';
        endforeach;
        $html .= '</ul>';
        return $html;
    }
}
