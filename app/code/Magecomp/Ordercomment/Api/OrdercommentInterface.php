<?php
namespace Magecomp\Ordercomment\Api;

/**
 * Interface OrdercommentInterface
 * Magecomp\Ordercomment\Api
 */
interface OrdercommentInterface
{
    /**
     * @param int $quoteid
     * @param string $comment
     * @return string
     */
    public function addComment($quoteid,$comment);

}
