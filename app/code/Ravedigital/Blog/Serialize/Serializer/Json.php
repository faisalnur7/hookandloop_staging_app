<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\Blog\Serialize\Serializer;

class Json extends \Magento\Framework\Serialize\Serializer\Json
{
    public function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
        }
        return $mixed;
    }

    public function serialize($data)
    {
        $result = json_encode($this->utf8ize($data));
        if (false === $result) {
            throw new \InvalidArgumentException("Unable to serialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }
}
