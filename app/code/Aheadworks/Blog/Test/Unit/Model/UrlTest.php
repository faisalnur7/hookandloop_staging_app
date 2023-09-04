<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Aheadworks\Blog\Api\Data\AuthorInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Blog\Model\Url;
use Magento\Framework\UrlInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\UrlRewrites\PathBuilder;

/**
 * Test for \Aheadworks\Blog\Model\Url
 */
class UrlTest extends \PHPUnit\Framework\TestCase
{
    /**#@+
     * Constants defined for test
     */
    const ROUTE_TO_BLOG = 'blog';
    const POST_URL_KEY = 'post';
    const CATEGORY_URL_KEY = 'cat';
    const AUTHOR_URL_KEY = 'author';
    const AUTHORS_URL_KEY = 'authors';
    const TAG_NAME = 'tag';
    const URL_SUFFIX_FOR_OTHER_PAGES = '/';
    const POST_URL_SUFFIX = '/';
    const AUTHOR_URL_SUFFIX = '/';
    /**#@-*/

    /**
     * @var Url
     */
    private $urlModel;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var PostInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $postMock;

    /**
     * @var CategoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryMock;

    /**
     * @var TagInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);

        $configMock = $this->createMock(Config::class);
        $configMock->expects($this->any())
            ->method('getRouteToBlog')
            ->willReturn(self::ROUTE_TO_BLOG);
        $configMock->expects($this->any())
            ->method('getRouteToAuthors')
            ->willReturn(self::AUTHORS_URL_KEY);
        $configMock->expects($this->any())
            ->method('getUrlSuffixForOtherPages')
            ->willReturn(self::URL_SUFFIX_FOR_OTHER_PAGES);
        $configMock->expects($this->any())
            ->method('getPostUrlSuffix')
            ->willReturn(self::POST_URL_SUFFIX);
        $configMock->expects($this->any())
            ->method('getAuthorUrlSuffix')
            ->willReturn(self::AUTHOR_URL_SUFFIX);

        $this->urlBuilderMock = $this->createMock(UrlInterface::class);
        $this->postMock = $this->createMock(PostInterface::class);
        $this->categoryMock = $this->createMock(CategoryInterface::class);
        $this->tagMock = $this->createMock(TagInterface::class);

        $this->urlModel = $objectManager->getObject(
            Url::class,
            [
                'config' => $configMock,
                'urlBuilder' => $this->urlBuilderMock
            ]
        );
    }

    /**
     * Testing that blog home url is built correctly
     */
    public function testGetBlogHomeUrlBuild()
    {
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->with(self::ROUTE_TO_BLOG);
        $this->urlModel->getBlogHomeUrl();
    }

    /**
     * Testing return value of 'getBlogHomeUrl' method
     */
    public function testGetBlogHomeUrlResult()
    {
        $blogHomeUrl = 'http://localhost/blog';
        $this->urlBuilderMock->expects($this->any())
            ->method('getDirectUrl')
            ->willReturn($blogHomeUrl);
        $this->assertEquals($blogHomeUrl, $this->urlModel->getBlogHomeUrl());
    }

    /**
     * Testing return value of 'getAuthorsPageUrl' method
     */
    public function testGetAuthorsUrl()
    {
        $authorsUrl = 'http://localhost/blog/authors';
        $this->urlBuilderMock->expects($this->any())
            ->method('getDirectUrl')
            ->willReturn($authorsUrl);
        $this->assertEquals($authorsUrl, $this->urlModel->getAuthorsPageUrl());
    }

    /**
     * Testing of 'getPostUrl' method
     */
    public function testGetPostUrl()
    {
        $blogPostUrl = 'http://localhost/blog/post';
        $this->postMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::POST_URL_KEY);
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->willReturn($blogPostUrl);
        $this->assertEquals($blogPostUrl, $this->urlModel->getPostUrl($this->postMock));
    }

    /**
     * Testing return value of 'getPostRoute' method
     */
    public function testGetPostRouteResult()
    {
        $this->postMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::POST_URL_KEY);
        $this->assertEquals(
            self::ROUTE_TO_BLOG . '/' . self::POST_URL_KEY . '/',
            $this->urlModel->getPostRoute($this->postMock)
        );
    }

    /**
     * Testing that category url is built correctly
     */
    public function testGetCategoryUrlBuild()
    {
        $this->categoryMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::CATEGORY_URL_KEY);
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->with(
                self::ROUTE_TO_BLOG . '/' . self::CATEGORY_URL_KEY . self::URL_SUFFIX_FOR_OTHER_PAGES
            );
        $this->urlModel->getCategoryUrl($this->categoryMock);
    }

    /**
     * Testing return value of 'getCategoryUrl' method
     */
    public function testGetCategoryUrlResult()
    {
        $blogCategoryUrl = 'http://localhost/blog/cat';
        $this->categoryMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::CATEGORY_URL_KEY);
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->willReturn($blogCategoryUrl);
        $this->assertEquals($blogCategoryUrl, $this->urlModel->getCategoryUrl($this->categoryMock));
    }

    /**
     * Testing return value of 'getCategoryRoute' method
     */
    public function testGetCategoryRouteResult()
    {
        $this->categoryMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::CATEGORY_URL_KEY);
        $this->assertEquals(
            self::ROUTE_TO_BLOG . '/' . self::CATEGORY_URL_KEY . '/',
            $this->urlModel->getCategoryRoute($this->categoryMock)
        );
    }
    
    /**
     * Testing that author url is built correctly
     */
    public function testGetAuthorUrlBuild()
    {
        $authorMock = $this->createMock(AuthorInterface::class);

        $authorMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::AUTHOR_URL_KEY);
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->with(
                self::ROUTE_TO_BLOG
                . '/' . self::AUTHORS_URL_KEY
                . '/' . self::AUTHOR_URL_KEY . self::AUTHOR_URL_SUFFIX
            );
        $this->urlModel->getAuthorUrl($authorMock);
    }

    /**
     * Testing return value of 'getAuthorUrl' method
     */
    public function testGetAuthorUrlResult()
    {
        $blogAuthorUrl = 'http://localhost/blog/authors/author';
        $authorMock = $this->createMock(AuthorInterface::class);

        $authorMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::AUTHOR_URL_KEY);
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->willReturn($blogAuthorUrl);
        $this->assertEquals($blogAuthorUrl, $this->urlModel->getAuthorUrl($authorMock));
    }

    /**
     * Testing return value of 'getAuthorRoute' method
     */
    public function testGetAuthorRouteResult()
    {
        $authorMock = $this->createMock(AuthorInterface::class);

        $authorMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn(self::AUTHOR_URL_KEY);
        $this->assertEquals(
            self::ROUTE_TO_BLOG . '/' . self::AUTHORS_URL_KEY . '/' . self::AUTHOR_URL_KEY . '/',
            $this->urlModel->getAuthorRoute($authorMock)
        );
    }

    /**
     * Testing that search by tag url is built correctly
     */
    public function testGetSearchByTagUrlBuild()
    {
        $this->tagMock->expects($this->any())
            ->method('getName')
            ->willReturn(self::TAG_NAME);
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->with(
                self::ROUTE_TO_BLOG . '/' . PathBuilder::TAG_PAGE_KEY . '/' . self::TAG_NAME . self::URL_SUFFIX_FOR_OTHER_PAGES
            );
        $this->urlModel->getSearchByTagUrl($this->tagMock);
    }

    /**
     * Testing that search by tag name url is built correctly
     */
    public function testGetSearchByTagNameUrlBuild()
    {
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->with(
                self::ROUTE_TO_BLOG . '/' . PathBuilder::TAG_PAGE_KEY . '/' . self::TAG_NAME . self::URL_SUFFIX_FOR_OTHER_PAGES
            );
        $this->urlModel->getSearchByTagUrl(self::TAG_NAME);
    }

    /**
     * Testing return value of 'getSearchByTagUrl' method
     */
    public function testGetSearchByTagResult()
    {
        $blogSearchByTagUrl = 'http://localhost/blog/tag/tag';
        $this->tagMock->expects($this->any())
            ->method('getName')
            ->willReturn(self::TAG_NAME);
        $this->urlBuilderMock->expects($this->once())
            ->method('getDirectUrl')
            ->willReturn($blogSearchByTagUrl);
        $this->assertEquals($blogSearchByTagUrl, $this->urlModel->getSearchByTagUrl($this->tagMock));
    }
}
