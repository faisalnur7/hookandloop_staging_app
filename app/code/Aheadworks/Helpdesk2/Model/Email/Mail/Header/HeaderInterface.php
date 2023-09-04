<?php
namespace Aheadworks\Helpdesk2\Model\Email\Mail\Header;

/**
 * Interface HeaderInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Email\Mail\Header
 */
interface HeaderInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const NAME = 'name';
    const VALUE = 'value';
    /**#@-*/

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);
}
