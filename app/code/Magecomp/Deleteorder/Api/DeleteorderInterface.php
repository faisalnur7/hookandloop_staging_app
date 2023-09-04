<?php
namespace Magecomp\Deleteorder\Api;

/**
 * Interface Deleteorder api
 * Magecomp\Deleteorder\Api
 */

interface DeleteorderInterface
{

    /**

     * @param int $orderid
     * @return int
     */

    public function getOrderid($orderid);
}
