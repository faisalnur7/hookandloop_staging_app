<?php
namespace Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver;

/**
 * Class Result
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver
 */
class Result
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Result
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retrieve email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Result
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }
}
