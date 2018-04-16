<?php namespace Pz\Doctrine\Rest\Tests\Action\Related;

use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionCreateAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionUpdateAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Blog;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Tag;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\BlogTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\TagTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CollectionActionTest extends TestCase
{
    /**
     * @return RelatedCollectionAction
     */
    protected function getRelatedBlogCollectionAction()
    {
        return new RelatedCollectionAction(
            RestRepository::create($this->em, User::class), 'user',
            RestRepository::create($this->em, Blog::class),
            new BlogTransformer()
        );
    }

    protected function getRelatedTagCollectionCreateAction()
    {
        return new RelationshipsCollectionCreateAction(
            RestRepository::create($this->em, User::class), 'tags', 'users',
            RestRepository::create($this->em, Tag::class),
            new TagTransformer()
        );
    }

    protected function getRelatedTagCollectionUpdateAction()
    {
        return new RelationshipsCollectionUpdateAction(
            RestRepository::create($this->em, User::class), 'tags', 'users',
            RestRepository::create($this->em, Tag::class),
            new TagTransformer()
        );
    }

    public function test_user_relation_blogs_index_action()
    {
        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getRelatedBlogCollectionAction()->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                [
                    'id' => '1',
                    'type' => Blog::getResourceKey(),
                    'attributes' => [
                        'title' => 'User1 blog title 1',
                        'content' => 'User1 blog content 1',
                    ],
                    'links' => [
                        'self' => '/blog/1',
                    ]
                ],
                [
                    'id' => '2',
                    'type' => Blog::getResourceKey(),
                    'attributes' => [
                        'title' => 'User1 blog title 2',
                        'content' => 'User1 blog content 2',
                    ],
                    'links' => [
                        'self' => '/blog/2',
                    ]
                ],
                [
                    'id' => '3',
                    'type' => Blog::getResourceKey(),
                    'attributes' => [
                        'title' => 'User1 blog title 3',
                        'content' => 'User1 blog content 3',
                    ],
                    'links' => [
                        'self' => '/blog/3',
                    ]
                ],
            ]
        ], json_decode($response->getContent(), true));

        $request = new Request(['id' => 1, 'page' => ['number' => 1, 'size' => 1], 'fields' => [Blog::getResourceKey() => 'title']]);
        $request->server->set('REQUEST_URI', '/user/1/blog');

        $response = $this->getRelatedBlogCollectionAction()->dispatch(new RestRequest($request));

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                [
                    'id' => '1',
                    'type' => Blog::getResourceKey(),
                    'attributes' => [
                        'title' => 'User1 blog title 1',
                    ],
                    'links' => [
                        'self' => '/blog/1',
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'total' => 3,
                    'count' => 1,
                    'per_page' => 1,
                    'current_page' => 1,
                    'total_pages' => 3,
                ]
            ],
            'links' => [
                'self' => '/user/1/blog?page%5Bnumber%5D=1&page%5Bsize%5D=1',
                'first' => '/user/1/blog?page%5Bnumber%5D=1&page%5Bsize%5D=1',
                'next' => '/user/1/blog?page%5Bnumber%5D=2&page%5Bsize%5D=1',
                'last' => '/user/1/blog?page%5Bnumber%5D=3&page%5Bsize%5D=1',
            ]
        ], json_decode($response->getContent(), true));

        $request = new Request(['id' => 2]);
        $response = $this->getRelatedBlogCollectionAction()->dispatch(new RestRequest($request));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => []], json_decode($response->getContent(), true));
    }
}
