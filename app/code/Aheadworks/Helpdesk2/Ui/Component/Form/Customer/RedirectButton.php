<?php
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Customer;

use Magento\Ui\Component\Container;

/**
 * Class RedirectButton
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Customer
 */
class RedirectButton extends Container
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $requestParam = $this->context->getRequestParam($config['requestParamName']);
        $config['urlToRedirect'] = $this->context->getUrl(
            $config['pathToRedirect'],
            [$config['paramName'] => $requestParam]
        );
        $this->setData('config', $config);

        parent::prepare();
    }
}
