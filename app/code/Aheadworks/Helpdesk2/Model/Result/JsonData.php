<?php
declare(strict_types = 1);

namespace Aheadworks\Helpdesk2\Model\Result;

/**
 * Class JsonData
 *
 * @package Aheadworks\Helpdesk2\Model\Result
 */
class JsonData implements \Aheadworks\Helpdesk2\Api\Data\Result\JsonDataInterface
{
    /**
     * @var bool
     */
    private $error;

    /**
     * @var string[]
     */
    private $messages;

    /**
     * @var array
     */
    private $data;

    /**
     * Json constructor.
     */
    public function __construct()
    {
        $this->error = false;
        $this->messages = [];
        $this->data = [];
    }

    /**
     * Get is error
     *
     * @return bool
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * Set error status
     *
     * @return $this
     */
    public function setError()
    {
        $this->error = true;
        
        return $this;
    }

    /**
     * Add message
     *
     * @param string|\Magento\Framework\Phrase $message
     * @return $this
     */
    public function addMessage($message)
    {
        $this->messages[] = $message instanceof \Magento\Framework\Phrase
            ? $message->render()
            : $message;
        
        return $this;
    }

    /**
     * Set messages
     *
     * @param string[]|\Magento\Framework\Phrase[] $messages
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = [];
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
        
        return $this;
    }

    /**
     * Set response data
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Add response data
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addData(string $key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Clear response data
     *
     * @param string|null $key
     * @return $this
     */
    public function clearData(string $key = null)
    {
        if ($key && array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        } else {
            $this->data = [];
        }

        return $this;
    }

    /**
     * Retrieve first message
     *
     * @return string
     */
    private function getFirstMessage()
    {
        return $this->messages[0] ?? '';
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
    {
        return [
            'success' => !$this->isError(),
            'error' => $this->isError(),
            'message' => $this->getFirstMessage(),
            'messages' => $this->messages,
            'data' => $this->data
        ];
    }
}
