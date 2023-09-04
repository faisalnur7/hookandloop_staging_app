<?php
namespace Aheadworks\BlogSearch\ViewModel;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\BlogSearch\Model\Url as BlogSearchUrl;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\BlogSearch\Ui\DataProvider\Frontend\PostDataProvider;
use Magento\Framework\Validator\AbstractValidator as SearchQueryValidator;

/**
 * Class PostList
 */
class PostList implements ArgumentInterface
{
    /**
     * @var PostDataProvider
     */
    private $postDataProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SearchQueryValidator
     */
    private $searchQueryValidator;

    /**
     * @var PostInterface[]
     */
    private $postList;

    /**
     * PostList constructor.
     * @param PostDataProvider $postDataProvider
     * @param RequestInterface $request
     * @param SearchQueryValidator $searchQueryValidator
     */
    public function __construct(
        PostDataProvider $postDataProvider,
        RequestInterface $request,
        SearchQueryValidator $searchQueryValidator
    ) {
        $this->postDataProvider = $postDataProvider;
        $this->request = $request;
        $this->searchQueryValidator = $searchQueryValidator;
    }

    /**
     * Returns post list
     *
     * @return PostInterface[]
     */
    public function getPostList()
    {
        if (empty($this->postList)) {
            $this->postList = $this->postDataProvider->getItems();
        }

        return $this->postList;
    }

    /**
     * Returns message for empty result
     *
     * @return \Magento\Framework\Phrase|string|null
     */
    public function getEmptyResultMessage()
    {
        $message = null;
        if (!$this->getPostList()) {
            try {
                $searchQuery = $this->request->getParam(BlogSearchUrl::SEARCH_QUERY_PARAM);
                if (!$this->searchQueryValidator->isValid($searchQuery)) {
                    $messages = $this->searchQueryValidator->getMessages();
                    $message = array_shift($messages);
                } else {
                    $message = __('Sorry, no results were found for that query.');
                }
            } catch (\Exception $e) {
                $message = null;
            }
        }

        return $message;
    }
}
