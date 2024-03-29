<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Variation\Manage;

use Ess\M2ePro\Block\Adminhtml\Amazon\Listing\Product\Variation\Manage\Tabs\Variations\NewChild\Form;
use Ess\M2ePro\Controller\Adminhtml\Amazon\Main;

/**
 * Class \Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Variation\Manage\GetNewChildForm
 */
class GetNewChildForm extends Main
{
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');

        if (empty($productId)) {
            return $this->getResponse()->setBody('You should provide correct parameters.');
        }

        $form = $this->getLayout()->createBlock(Form::class);
        $form->setListingProduct($this->amazonFactory->getObjectLoaded('Listing\Product', $productId));

        $this->setAjaxContent($form);

        return $this->getResult();
    }
}
