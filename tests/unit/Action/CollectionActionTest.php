<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Mockery as m;
use Pz\Doctrine\Rest\Action\CollectionAction;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\BlogComment;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\BlogCommentTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CollectionActionTest extends TestCase
{
    protected function getCollectionAction()
    {
        return new CollectionAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        );
    }

    public function test_index_action_complex_include_and_fieldset()
    {
        $action = new CollectionAction(
            new RestRepository($this->em, $this->em->getClassMetadata(BlogComment::class)),
            new BlogCommentTransformer()
        );

        $request = new RestRequest(new Request(['include' => 'user', 'fields' => ['user' => 'id,name']]));
        $this->assertInstanceOf(RestResponse::class, $response = $action->dispatch($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 1',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 1, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/1/relationships/user',
                                    'related' => '/blog_comment/1/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/1'
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 2',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 1, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/2/relationships/user',
                                    'related' => '/blog_comment/2/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/2'
                        ],
                    ],
                    [
                        'id' => '3',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 3',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 3, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/3/relationships/user',
                                    'related' => '/blog_comment/3/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/3'
                        ],
                    ],
                    [
                        'id' => '4',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 4',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 2, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/4/relationships/user',
                                    'related' => '/blog_comment/4/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/4'
                        ],
                    ],
                    [
                        'id' => '5',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 5',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 3, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/5/relationships/user',
                                    'related' => '/blog_comment/5/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/5'
                        ],
                    ],
                    [
                        'id' => '6',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 6',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 1, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/6/relationships/user',
                                    'related' => '/blog_comment/6/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/6'
                        ],
                    ],
                    [
                        'id' => '7',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 7',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 4, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/7/relationships/user',
                                    'related' => '/blog_comment/7/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/7'
                        ],
                    ],
                    [
                        'id' => '8',
                        'type' => 'blog_comment',
                        'attributes' => [
                            'content' => 'Comment content 8',
                        ],
                        'relationships' => [
                            'user' => [
                                'data' => ['id' => 4, 'type' => 'user'],
                                'links' => [
                                    'self' => '/blog_comment/8/relationships/user',
                                    'related' => '/blog_comment/8/user',
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => '/blog_comment/8'
                        ],
                    ],
                ],
                'included' => [
                    [
                        'id' => '1',
                        'type' => 'user',
                        'attributes' => [
                            'name' => 'User1Name',
                        ],
                        'links' => [
                            'self' => '/user/1',
                        ]
                    ],
                    [
                        'id' => '3',
                        'type' => 'user',
                        'attributes' => [
                            'name' => 'User3Name',
                        ],
                        'links' => [
                            'self' => '/user/3',
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'user',
                        'attributes' => [
                            'name' => 'User2Name',
                        ],
                        'links' => [
                            'self' => '/user/2',
                        ]
                    ],
                    [
                        'id' => '4',
                        'type' => 'user',
                        'attributes' => [
                            'name' => 'User4Name',
                        ],
                        'links' => [
                            'self' => '/user/4',
                        ]
                    ],
                ]
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function test_index_action_include()
    {
        $request = new RestRequest(new Request(['include' => 'blogs', 'exclude' => 'blogs.user']));
        $this->assertInstanceOf(RestResponse::class, $response = $this->getCollectionAction()->dispatch($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User1Name',
                        'email' => 'user1@test.com',
                    ],
                    'relationships' => [
                        'blogs' => [
                            'data' => [
                                [
                                    'id' => '1',
                                    'type' => 'blog',
                                ],
                                [
                                    'id' => '2',
                                    'type' => 'blog',
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'blog',
                                ],
                            ],
                            'links' => [
                                'self' => '/user/1/relationships/blogs',
                                'related' => '/user/1/blogs',
                            ],
                        ]
                    ],
                    'links' => [
                        'self' => '/user/1'
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User2Name',
                        'email' => 'user2@gmail.com',
                    ],
                    'relationships' => [
                        'blogs' => [
                            'data' => [],
                            'links' => [
                                'self' => '/user/2/relationships/blogs',
                                'related' => '/user/2/blogs',
                            ],
                        ]
                    ],
                    'links' => [
                        'self' => '/user/2'
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User3Name',
                        'email' => 'user3@test.com',
                    ],
                    'relationships' => [
                        'blogs' => [
                            'data' => [
                                [
                                    'id' => 4,
                                    'type' => 'blog',
                                ]
                            ],
                            'links' => [
                                'self' => '/user/3/relationships/blogs',
                                'related' => '/user/3/blogs',
                            ],
                        ]
                    ],
                    'links' => [
                        'self' => '/user/3'
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User4Name',
                        'email' => 'user4@test.com',
                    ],
                    'relationships' => [
                        'blogs' => [
                            'data' => [],
                            'links' => [
                                'self' => '/user/4/relationships/blogs',
                                'related' => '/user/4/blogs',
                            ],
                        ]
                    ],
                    'links' => [
                        'self' => '/user/4'
                    ]
                ],
                [
                    'id' => '5',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User5Name',
                        'email' => 'user5@test.com',
                    ],
                    'relationships' => [
                        'blogs' => [
                            'data' => [],
                            'links' => [
                                'self' => '/user/5/relationships/blogs',
                                'related' => '/user/5/blogs',
                            ],
                        ]
                    ],
                    'links' => [
                        'self' => '/user/5'
                    ]
                ],
            ],
            'included' => [
                [
                    'type' => 'blog',
                    'id' => '1',
                    'attributes' => [
                        'title' => 'User1 blog title 1',
                        'content' => 'User1 blog content 1',
                    ],
                    'links' => [
                        'self' => '/blog/1'
                    ]
                ],
                [
                    'type' => 'blog',
                    'id' => '2',
                    'attributes' => [
                        'title' => 'User1 blog title 2',
                        'content' => 'User1 blog content 2',
                    ],
                    'links' => [
                        'self' => '/blog/2'
                    ]
                ],
                [
                    'type' => 'blog',
                    'id' => '3',
                    'attributes' => [
                        'title' => 'User1 blog title 3',
                        'content' => 'User1 blog content 3',
                    ],
                    'links' => [
                        'self' => '/blog/3'
                    ]
                ],
                [
                    'type' => 'blog',
                    'id' => '4',
                    'attributes' => [
                        'title' => 'User3 blog title 1',
                        'content' => 'User3 blog content 1',
                    ],
                    'links' => [
                        'self' => '/blog/4'
                    ]
                ],
            ],
        ], json_decode($response->getContent(), true));
    }

    public function test_index_action_fieldset()
    {
        $request = new RestRequest(new Request(['fields' => ['user' => 'email']]));
        $response = $this->getCollectionAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'user',
                        'attributes' => [
                            'email' => 'user1@test.com',
                        ],
                        'links' => [
                            'self' => '/user/1',
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'user',
                        'attributes' => [
                            'email' => 'user2@gmail.com',
                        ],
                        'links' => [
                            'self' => '/user/2',
                        ]
                    ],
                    [
                        'id' => '3',
                        'type' => 'user',
                        'attributes' => [
                            'email' => 'user3@test.com',
                        ],
                        'links' => [
                            'self' => '/user/3',
                        ]
                    ],
                    [
                        'id' => '4',
                        'type' => 'user',
                        'attributes' => [
                            'email' => 'user4@test.com',
                        ],
                        'links' => [
                            'self' => '/user/4',
                        ]
                    ],
                    [
                        'id' => '5',
                        'type' => 'user',
                        'attributes' => [
                            'email' => 'user5@test.com',
                        ],
                        'links' => [
                            'self' => '/user/5',
                        ]
                    ],
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function test_index_action_filter()
    {
        $request = new RestRequest(new Request(['filter' => '@gmail.com']));
        $response = $this->getCollectionAction()->setFilterProperty('email')->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [[
                    'id' => '2',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User2Name',
                        'email' => 'user2@gmail.com',
                    ],
                    'links' => [
                        'self' => '/user/2',
                    ]
                ]],
            ],
            json_decode($response->getContent(), true)
        );

        $request = new RestRequest(new Request(['filter' => json_encode(['id' => ['start' => 2, 'end' => 3]])]));
        $response = $this->getCollectionAction()->setFilterable(['id'])->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [[
                    'id' => '2',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User2Name',
                        'email' => 'user2@gmail.com',
                    ],
                    'links' => [
                        'self' => '/user/2',
                    ]
                ]],
            ],
            json_decode($response->getContent(), true)
        );

    }

    public function test_index_action_pagination()
    {
        $request = new RestRequest(new Request(['page' => ['number' => 1, 'size' => 2]]));
        $this->assertInstanceOf(RestResponse::class, $response = $this->getCollectionAction()->dispatch($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    ['id' => '1'],
                    ['id' => '2'],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 5,
                        'count' => 2,
                        'per_page' => 2,
                        'current_page' => 1,
                        'total_pages' => 3,
                    ],
                ],
            ],
            json_decode($response->getContent(), true)
        );

        $request = new RestRequest(new Request(['page' => ['number' => 2, 'size' => 2]]));
        $this->assertInstanceOf(RestResponse::class, $response = $this->getCollectionAction()->dispatch($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    ['id' => '3'],
                    ['id' => '4'],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 5,
                        'count' => 2,
                        'per_page' => 2,
                        'current_page' => 2,
                        'total_pages' => 3,
                    ],
                ],
            ],
            json_decode($response->getContent(), true)
        );

        $request = new RestRequest(new Request(['page' => ['number' => 3, 'size' => 2]]));
        $this->assertInstanceOf(RestResponse::class, $response = $this->getCollectionAction()->dispatch($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    ['id' => '5'],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 5,
                        'count' => 1,
                        'per_page' => 2,
                        'current_page' => 3,
                        'total_pages' => 3,
                    ],
                ],
            ],
            json_decode($response->getContent(), true)
        );

        $request = new Request(['page' => ['number' => 1, 'size' => 2]]);
        $request->server->set('REQUEST_URI', '/user');
        $response = $this->getCollectionAction()->dispatch(new RestRequest($request));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    ['id' => '1', 'type' => 'user', 'links' => ['self' => '/user/1']],
                    ['id' => '2', 'type' => 'user', 'links' => ['self' => '/user/2']],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 5,
                        'count' => 2,
                        'per_page' => 2,
                        'current_page' => 1,
                        'total_pages' => 3,
                    ],
                ],
                'links' => [
                    'self' => '/user?page%5Bnumber%5D=1&page%5Bsize%5D=2',
                    'first' => '/user?page%5Bnumber%5D=1&page%5Bsize%5D=2',
                    'last' => '/user?page%5Bnumber%5D=3&page%5Bsize%5D=2',
                ],
            ],
            json_decode($response->getContent(), true)
        );

        $request = new Request(['page' => ['offset' => 2, 'limit' => 2]]);
        $request->server->set('REQUEST_URI', '/user');
        $response = $this->getCollectionAction()->dispatch(new RestRequest($request));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    ['id' => '3', 'type' => 'user', 'links' => ['self' => '/user/3']],
                    ['id' => '4', 'type' => 'user', 'links' => ['self' => '/user/4']],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 5,
                        'count' => 2,
                        'per_page' => 2,
                        'current_page' => 2,
                        'total_pages' => 3,
                    ],
                ],
                'links' => [
                    'self' => '/user?page%5Bnumber%5D=2&page%5Bsize%5D=2',
                    'first' => '/user?page%5Bnumber%5D=1&page%5Bsize%5D=2',
                    'last' => '/user?page%5Bnumber%5D=3&page%5Bsize%5D=2',
                ],
            ],
            json_decode($response->getContent(), true)
        );

        $request = new Request(['page' => ['offset' => 4, 'limit' => 2]]);
        $request->server->set('REQUEST_URI', '/user');
        $response = $this->getCollectionAction()->dispatch(new RestRequest($request));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    ['id' => '5', 'type' => 'user', 'links' => ['self' => '/user/5']],
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 5,
                        'count' => 1,
                        'per_page' => 2,
                        'current_page' => 3,
                        'total_pages' => 3,
                    ],
                ],
                'links' => [
                    'self' => '/user?page%5Bnumber%5D=3&page%5Bsize%5D=2',
                    'first' => '/user?page%5Bnumber%5D=1&page%5Bsize%5D=2',
                    'last' => '/user?page%5Bnumber%5D=3&page%5Bsize%5D=2',
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function test_index_action_sorting()
    {
        $request = new RestRequest(new Request(['sort' => '-id,name']));

        $this->assertInstanceOf(RestResponse::class, $response = $this->getCollectionAction()->dispatch($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                [
                    'id' => '5',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User5Name',
                        'email' => 'user5@test.com',
                    ],
                    'links' => [
                        'self' => '/user/5'
                    ]
                ],
                [
                    'id' => '4',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User4Name',
                        'email' => 'user4@test.com',
                    ],
                    'links' => [
                        'self' => '/user/4'
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User3Name',
                        'email' => 'user3@test.com',
                    ],
                    'links' => [
                        'self' => '/user/3'
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User2Name',
                        'email' => 'user2@gmail.com',
                    ],
                    'links' => [
                        'self' => '/user/2'
                    ]
                ],
                [
                    'id' => '1',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User1Name',
                        'email' => 'user1@test.com',
                    ],
                    'links' => [
                        'self' => '/user/1'
                    ]
                ],
            ],
        ], json_decode($response->getContent(), true));
    }
}
