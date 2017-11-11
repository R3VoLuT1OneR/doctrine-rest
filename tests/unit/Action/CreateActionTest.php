<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\CreateAction;
use Pz\Doctrine\Rest\Response\FractalResponseFactory;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;

class CreateActionTest extends TestCase
{

    public function test_create_user_and_blog()
    {
        $action = new CreateAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new FractalResponseFactory('http://localhost/api', new UserTransformer())
        );

        $request = new RestRequest();
        $request->initialize(['include' => 'role'], [
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
        ]);

        $request->headers->set('Accept', RestRequest::JSON_API_CONTENT_TYPE);
        $request->headers->set('CONTENT_TYPE', RestRequest::JSON_API_CONTENT_TYPE);
        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
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
                                'self' => 'http://localhost/api/user/6/relationships/role',
                                'related' => 'http://localhost/api/user/6/role',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/user/6',
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
                            'self' => 'http://localhost/api/role/1',
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
            new FractalResponseFactory('http://localhost/api', new RoleTransformer())
        );

        $request = new RestRequest();
        $request->initialize([], [
            'name' => 'New Role',
        ]);

        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    'id' => 3,
                    'name' => 'New Role',
                ],
            ],
            json_decode($response->getContent(), true)
        );

        $request = new RestRequest();
        $request->initialize([], [
            'data' => [
                'attributes' => [
                    'name' => 'New Role',
                ]
            ]
        ]);

        $request->headers->set('CONTENT_TYPE', RestRequest::JSON_API_CONTENT_TYPE);
        $request->headers->set('Accept', RestRequest::JSON_API_CONTENT_TYPE);
        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            [
                'data' => [
                    'id' => '4',
                    'type' => 'role',
                    'attributes' => [
                        'name' => 'New Role',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/role/4',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }
}
