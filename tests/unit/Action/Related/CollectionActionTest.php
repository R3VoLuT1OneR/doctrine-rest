<?php namespace Pz\Doctrine\Rest\Tests\Action\Related;

use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionCreateAction;
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

    public function test_user_related_blog_manage_action()
    {
        $request = new RestRequest(new Request(['id' => 1], ['data' => [
            [
                'attributes' => [
                    'title' => 'Custom blog title',
                    'content' => 'Custom blog content',
                ]
            ]
        ]]));
        $response = $this->getUserRelatedBlogCollectionCreateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertResponseContent(['data' => [
            [
                'id' => '1',
                'type' => Blog::getResourceKey(),
            ],
            [
                'id' => '2',
                'type' => Blog::getResourceKey(),
            ],
            [
                'id' => '3',
                'type' => Blog::getResourceKey(),
            ],
            [
                'id' => '5',
                'type' => Blog::getResourceKey(),
                'attributes' => [
                    'title' => 'Custom blog title',
                    'content' => 'Custom blog content',
                ]
            ],
        ]
        ], $response);

        $request = new RestRequest(new Request(['id' => 5]));
        $response = $this->getBlogItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertResponseContent(['data' => [
            'id' => '5',
            'type' => Blog::getResourceKey(),
            'attributes' => [
                'title' => 'Custom blog title',
                'content' => 'Custom blog content',
            ]
        ]], $response);

        $request = new RestRequest(new Request(['id' => 5], ['data' => [
            ['id' => 2, 'type' => Blog::getResourceKey()],
            ['id' => 3, 'type' => Blog::getResourceKey()],
        ]]));
        $response = $this->getUserRelatedBlogCollectionDeleteAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(RestResponse::HTTP_NO_CONTENT, $response->getStatusCode());

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelatedBlogCollectionAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            [
                'id' => '1',
                'type' => Blog::getResourceKey(),
                'attributes' => [
                    'title' => 'User1 blog title 1',
                    'content' => 'User1 blog content 1',
                ],
                'links' => ['self' => '/blog/1'],
            ],
            [
                'id' => '5',
                'type' => Blog::getResourceKey(),
                'attributes' => [
                    'title' => 'Custom blog title',
                    'content' => 'Custom blog content',
                ],
                'links' => ['self' => '/blog/5'],
            ],
        ]
        ], json_decode($response->getContent(), true));


        $request = new RestRequest(new Request(['id' => 2]));
        $response = $this->getBlogItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $request = new RestRequest(new Request(['id' => 3]));
        $response = $this->getBlogItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_user_relation_blogs_index_action()
    {
        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelatedBlogCollectionAction()->dispatch($request);
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

        $response = $this->getUserRelatedBlogCollectionAction()->dispatch(new RestRequest($request));

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
        $response = $this->getUserRelatedBlogCollectionAction()->dispatch(new RestRequest($request));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => []], json_decode($response->getContent(), true));
    }
}
