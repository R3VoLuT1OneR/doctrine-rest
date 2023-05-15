<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\CreateAction;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CreateActionTest extends TestCase
{

    public function test_create_before_after_callbacks()
    {
        $beforeCalled = false;
        $afterCalled = false;

        $action = (new CreateAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        ))
            ->beforeCreate(function($entity) use (&$beforeCalled) {
                $this->assertInstanceOf(User::class, $entity);
                $beforeCalled = true;
            })
            ->afterCreated(function ($entity) use (&$afterCalled) {
                $this->assertInstanceOf(User::class, $entity);
                $afterCalled = true;
            });

        $request = new RestRequest(new Request(
            query: ['include' => 'role'],
            content: json_encode([
                'data' => [
                    'attributes' => [
                        'name' => 'New User',
                        'email' => 'test@teststst.com',
                    ],
                    'relationships' => [
                        'role' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'role',
                            ]
                        ]
                    ]
                ]
            ])
        ));

        $request->http()->headers->set('Accept', RestRequest::JSON_API_CONTENT_TYPE);
        $request->http()->headers->set('CONTENT_TYPE', RestRequest::JSON_API_CONTENT_TYPE);
        $action->dispatch($request);

        $this->assertTrue($beforeCalled);
        $this->assertTrue($afterCalled);
    }

    public function test_create_user_and_blog()
    {
        $action = new CreateAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        );

        $request = new RestRequest(new Request(
            query: ['include' => 'role'],
            content: json_encode([
                'data' => [
                    'attributes' => [
                        'name' => 'New User',
                        'email' => 'test@teststst.com',
                    ],
                    'relationships' => [
                        'role' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'role',
                            ]
                        ]
                    ]
                ]
            ])
        ));

        $request->http()->headers->set('Accept', RestRequest::JSON_API_CONTENT_TYPE);
        $request->http()->headers->set('CONTENT_TYPE', RestRequest::JSON_API_CONTENT_TYPE);
        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    'id' => '6',
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'New User',
                        'email' => 'test@teststst.com',
                    ],
                    'relationships' => [
                        'role' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'role'
                            ],
                            'links' => [
                                'self' => '/user/6/relationships/role',
                                'related' => '/user/6/role',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => '/user/6',
                    ]
                ],
                'included' => [
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

    public function test_create_role()
    {
        $action = new CreateAction(
            new RestRepository($this->em, $this->em->getClassMetadata(Role::class)),
            new RoleTransformer()
        );

        $request = new RestRequest(new Request(
            query: [],
            content: json_encode([
                'data' => [
                    'attributes' => [
                        'name' => 'New Role',
                    ]
                ]
            ])
        ));

        $request->http()->headers->set('CONTENT_TYPE', RestRequest::JSON_API_CONTENT_TYPE);
        $request->http()->headers->set('Accept', RestRequest::JSON_API_CONTENT_TYPE);
        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    'id' => '3',
                    'type' => 'role',
                    'attributes' => [
                        'name' => 'New Role',
                    ],
                    'links' => [
                        'self' => '/role/3',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }
}
