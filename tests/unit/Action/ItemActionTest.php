<?php namespace Doctrine\Rest\Tests\Unit\Action;

use Doctrine\Rest\Action\ItemAction;
use Doctrine\Rest\RestResponseFactory;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestRequest;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Doctrine\Rest\Tests\Entities\User;
use Doctrine\Rest\Tests\TestCase;

class ItemActionTest extends TestCase
{
    public function getItemAction()
    {
        return new ItemAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        );
    }

    public function test_item_action_complex_json_api()
    {
        $request = new RestRequest(new Request([
            'id' => '1',
            'include' => 'role,blogs',
            'fields' => [
                'user' => 'name,blogs,role',
                'blog' => 'content',
            ],
        ]));

        $request->http()->headers->set('Accept', RestRequest::JSON_API_CONTENT_TYPE);
        $this->assertInstanceOf(RestResponse::class, $response = $this->getItemAction()->dispatch($request));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    'id' => '1',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'User1Name',
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
                            ]
                        ],
                        'role' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'role',
                            ],
                            'links' => [
                                'self' => '/user/1/relationships/role',
                                'related' => '/user/1/role',
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/user/1',
                    ]
                ],
                'included' => [
                    [
                        'id' => '1',
                        'type' => 'blog',
                        'attributes' => [
                            'content' => 'User1 blog content 1',
                        ],
                        'links' => [
                            'self' => '/blog/1',
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'blog',
                        'attributes' => [
                            'content' => 'User1 blog content 2',
                        ],
                        'links' => [
                            'self' => '/blog/2',
                        ]
                    ],
                    [
                        'id' => '3',
                        'type' => 'blog',
                        'attributes' => [
                            'content' => 'User1 blog content 3',
                        ],
                        'links' => [
                            'self' => '/blog/3',
                        ]
                    ],
                    [
                        'id' => '1',
                        'type' => 'role',
                        'attributes' => [
                            'name' => 'Admin',
                        ],
                        'links' => [
                            'self' => '/role/1',
                        ]
                    ]
                ]
            ],
            json_decode($response->getContent(), true)
        );
    }
}
