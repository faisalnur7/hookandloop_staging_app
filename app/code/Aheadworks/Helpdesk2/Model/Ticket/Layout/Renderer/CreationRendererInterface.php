<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer;

use Aheadworks\Helpdesk2\Model\Ticket\Layout\RendererInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Interface CreationRendererInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer
 */
interface CreationRendererInterface extends RendererInterface
{
    const DEPARTMENTS = 'departments';
    const REQUEST = 'request';

    /**
     * Get departments
     *
     * @return DepartmentInterface[]
     */
    public function getDepartments();

    /**
     * Set departments
     *
     * @param DepartmentInterface[] $departments
     * @return $this
     */
    public function setDepartments($departments);

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * Set request
     *
     * @param $request
     * @return $this
     */
    public function setRequest($request);
}
