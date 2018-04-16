<?php namespace Pz\Doctrine\Rest\Tests\Action\Relationships;

use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionCreateAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionUpdateAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Blog;
use Pz\Doctrine\Rest\Tests\Entities\Tag;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\BlogTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\TagTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RelationshipsCollectionActionTest extends TestCase
{

    public function getRelationshipsRoleCollectionAction()
    {
        return new RelationshipsCollectionAction(
            RestRepository::create($this->em, User::class), 'user',
            RestRepository::create($this->em, Blog::class),
            new BlogTransformer()
        );
    }

    protected function getRelationshipsTagCollectionAction()
    {
        return new RelationshipsCollectionAction(
            RestRepository::create($this->em, User::class), 'users',
            RestRepository::create($this->em, Tag::class),
            new TagTransformer()
        );
    }

    protected function getRelationshipsTagCollectionCreateAction()
    {
        return new RelationshipsCollectionCreateAction(
            RestRepository::create($this->em, User::class), 'tags', 'users',
            RestRepository::create($this->em, Tag::class),
            new TagTransformer()
        );
    }

    protected function getRelationshipsTagCollectionUpdateAction()
    {
        return new RelationshipsCollectionUpdateAction(
            RestRepository::create($this->em, User::class), 'tags', 'users',
            RestRepository::create($this->em, Tag::class),
            new TagTransformer()
        );
    }

    protected function getRelationshipsTagCollectionDeleteAction()
    {
        return new RelationshipsCollectionDeleteAction(
            RestRepository::create($this->em, User::class), 'tags',
            RestRepository::create($this->em, Tag::class),
            new TagTransformer()
        );
    }

    public function test_user_relation_tags_create_action()
    {
        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getRelationshipsTagCollectionAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            [
                'id' => '1',
                'type' => Tag::getResourceKey(),
                'links' => ['self' => '/tag/1'],
            ],
            [
                'id' => '2',
                'type' => Tag::getResourceKey(),
                'links' => ['self' => '/tag/2'],
            ],
            [
                'id' => '3',
                'type' => Tag::getResourceKey(),
                'links' => ['self' => '/tag/3'],
            ],
        ]], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1], ['data' => [
            ['id' => 4, 'type' => Tag::getResourceKey()]
        ]]));
        $response = $this->getRelationshipsTagCollectionCreateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            [
                'id' => 1,
                'type' => Tag::getResourceKey(),
                'attributes' => ['name' => 'test1'],
                'links' => ['self' => '/tag/1'],
            ],
            [
                'id' => 2,
                'type' => Tag::getResourceKey(),
                'attributes' => ['name' => 'test2'],
                'links' => ['self' => '/tag/2'],
            ],
            [
                'id' => 3,
                'type' => Tag::getResourceKey(),
                'attributes' => ['name' => 'test3'],
                'links' => ['self' => '/tag/3'],
            ],
            [
                'id' => 4,
                'type' => Tag::getResourceKey(),
                'attributes' => ['name' => 'test4'],
                'links' => ['self' => '/tag/4'],
            ],
        ]], json_decode($response->getContent(), true));

    }

    public function test_user_relation_tags_update_action()
    {
        $request = new RestRequest(new Request(['id' => 1], ['data' => []]));
        $response = $this->getRelationshipsTagCollectionUpdateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => []], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1], ['data' => [
            ['id' => 4, 'type' => Tag::getResourceKey()],
            ['id' => 2, 'type' => Tag::getResourceKey()],
        ]]));
        $response = $this->getRelationshipsTagCollectionUpdateAction()->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            [
                'id' => 2,
                'type' => Tag::getResourceKey(),
                'attributes' => [
                    'name' => 'test2',
                ],
                'links' => [
                    'self' => '/tag/2'
                ]
            ],
            [
                'id' => 4,
                'type' => Tag::getResourceKey(),
                'attributes' => [
                    'name' => 'test4',
                ],
                'links' => [
                    'self' => '/tag/4'
                ]
            ],
        ]], json_decode($response->getContent(), true));
    }

    public function test_user_relation_tags_delete_action()
    {
        $request = new RestRequest(new Request(['id' => 1], ['data' => []]));
        $response = $this->getRelationshipsTagCollectionDeleteAction()->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals([], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1], ['data' => [
            ['id' => 1, 'type' => Tag::getResourceKey()],
        ]]));
        $response = $this->getRelationshipsTagCollectionDeleteAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals([], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getRelationshipsTagCollectionAction()->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                [
                    'id' => '2',
                    'type' => Tag::getResourceKey(),
                    'links' => ['self' => '/tag/2'],
                ],
                [
                    'id' => '3',
                    'type' => Tag::getResourceKey(),
                    'links' => ['self' => '/tag/3'],
                ],
            ]
        ], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1], ['data' => [
            ['id' => 2, 'type' => Tag::getResourceKey()],
            ['id' => 3, 'type' => Tag::getResourceKey()],
        ]]));

        $response = $this->getRelationshipsTagCollectionDeleteAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals([], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getRelationshipsTagCollectionAction()->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => []], json_decode($response->getContent(), true));
    }

    public function test_relationships_collection_test()
    {
        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getRelationshipsRoleCollectionAction()->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            [
                'id' => '1',
                'type' => Blog::getResourceKey(),
                'links' => ['self' => '/blog/1']
            ],
            [
                'id' => '2',
                'type' => Blog::getResourceKey(),
                'links' => ['self' => '/blog/2']
            ],
            [
                'id' => '3',
                'type' => Blog::getResourceKey(),
                'links' => ['self' => '/blog/3']
            ],
        ]], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1, 'include' => ['user']]));
        $response = $this->getRelationshipsRoleCollectionAction()->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                [
                    'id' => '1',
                    'type' => Blog::getResourceKey(),
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => '1',
                                'type' => User::getResourceKey()
                            ],
                            'links' => [
                                'self' => '/blog/1/relationships/user',
                                'related' => '/blog/1/user',
                            ],
                        ]
                    ],
                    'links' => ['self' => '/blog/1']
                ],
                [
                    'id' => '2',
                    'type' => Blog::getResourceKey(),
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => '1',
                                'type' => User::getResourceKey()
                            ],
                            'links' => [
                                'self' => '/blog/2/relationships/user',
                                'related' => '/blog/2/user',
                            ],
                        ]
                    ],
                    'links' => ['self' => '/blog/2']
                ],
                [
                    'id' => '3',
                    'type' => Blog::getResourceKey(),
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => '1',
                                'type' => User::getResourceKey()
                            ],
                            'links' => [
                                'self' => '/blog/3/relationships/user',
                                'related' => '/blog/3/user',
                            ],
                        ]
                    ],
                    'links' => ['self' => '/blog/3']
                ],
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => User::getResourceKey(),
                    'attributes' => [
                        'name' => 'User1Name',
                        'email' => 'user1@test.com',
                    ],
                    'links' => ['self' => '/user/1']
                ]
            ]
        ], json_decode($response->getContent(), true));

    }

}
