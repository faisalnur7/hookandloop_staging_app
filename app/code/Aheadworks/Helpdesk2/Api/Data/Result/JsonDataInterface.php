<?php
namespace Aheadworks\Helpdesk2\Api\Data\Result;

use JsonSerializable;

/**
 * Interface JsonDataResultInterface
 *
 * @api
 */
interface JsonDataInterface extends JsonSerializable
{
    /**
     * Get is error
     *
     * @return bool
     */
    public function isError();

    /**
     * Set error status
     *
     * @return $this
     */
    public function setError();
    
    /**
     * Add message
     *
     * @param string|\Magento\Framework\Phrase $message
     * @return $this
     */
    public function addMessage($message);

    /**
     * Set messages
     *
     * @param string[]|\Magento\Framework\Phrase[] $messages
     * @return $this
     */
    public function setMessages(array $messages);

    /**
     * Set response data
     * 
     * @param array $data
     * @return $this
     */
    public function setData(array $data);

    /**
     * Add response data
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addData(string $key, $value);

    /**
     * Clear response data
     *
     * @param string|null $key
     * @return $this
     */
    public function clearData(string $key = null);
}
