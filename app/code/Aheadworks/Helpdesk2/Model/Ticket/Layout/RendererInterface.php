<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout;

/**
 * Interface RendererInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout
 */
interface RendererInterface
{
    const STORE_ID = 'id';

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);
}
