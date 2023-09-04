<?php
namespace Magecomp\Ordercomment\Model;
use Magento\Framework\Exception\AuthenticationException;
use Magecomp\Ordercomment\Helper\Data\Ordercomment;

class Orderaddcomment implements \Magecomp\Ordercomment\Api\OrdercommentInterface
{
    protected $quoteFactory;
    protected $helperOrderComment;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteFactory, 
        Ordercomment $helperOrderComment
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->helperOrderComment = $helperOrderComment;
    }
    public function addComment($quoteid,$comment)
    {
        $maxlength= $this->helperOrderComment->getMaxLength();
        $commentLength= strlen($comment);
        //return $commentLength;
        try {
            if (empty($quoteid) || empty($comment)) {
                $response = ["status"=>false, "message"=>__("Invalid parameter list.")];
            }
            elseif($commentLength>$maxlength)
            {
                $response = ["status"=>false, "message"=>__("The comment exceeds the maximum character length limit.")];
            }
            else {
                $quote = $this->quoteFactory->get($quoteid);
                $quote->setMagecomp_order_comment($comment);
                $quote->save();
                $response = ["status" =>true];
            }
            if(empty($maxlength))
            {
                $quote = $this->quoteFactory->get($quoteid);
                $quote->setMagecomp_order_comment($comment);
                $quote->save();
                $response = ["status" =>true];
            }
            return json_encode($response);
        }
        catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }

    }
}
