<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Magento\Renderer;

class JsPhpRenderer extends AbstractRenderer
{
    protected $jsPhp = [];

    public function addConstants($constants)
    {
        $this->jsPhp = array_merge($this->jsPhp, $constants);

        return $this;
    }

    public function render()
    {
        if (empty($this->jsPhp)) {
            return '';
        }

        $constants = \Ess\M2ePro\Helper\Json::encode($this->jsPhp);

        return "M2ePro.php.add({$constants});";
    }
}
