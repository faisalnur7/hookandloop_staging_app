<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Setup;

use Plumrocket\Base\Setup\AbstractUninstall;
use Plumrocket\SocialLoginFree\Model\ResourceModel\AccountLink;

class Uninstall extends AbstractUninstall
{
    protected $_configSectionId = 'psloginfree';
    protected $_tables = [AccountLink::MAIN_TABLE];
    protected $_pathes = ['/app/code/Plumrocket/SocialLoginFree'];
}
