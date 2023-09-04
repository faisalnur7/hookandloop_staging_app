<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Magento\Renderer;

class JsTranslatorRenderer extends AbstractRenderer
{
    protected $jsTranslations = [];

    public function add($alias, $translation)
    {
        $this->jsTranslations[$alias] = $translation;

        return $this;
    }

    public function addTranslations(array $translations)
    {
        $this->jsTranslations = array_merge($this->jsTranslations, $translations);

        return $this;
    }

    public function render()
    {
        if (empty($this->jsTranslations)) {
            return '';
        }

        $translations = \Ess\M2ePro\Helper\Json::encode($this->jsTranslations);

        return "M2ePro.translator.add({$translations});";
    }
}
