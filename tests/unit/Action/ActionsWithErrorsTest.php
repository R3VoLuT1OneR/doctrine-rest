<?php namespace Doctrine\Rest\Tests\Action;

use Doctrine\Rest\Action\CollectionAction;
use Doctrine\Rest\Action\CreateAction;
use Doctrine\Rest\Action\ItemAction;
use Doctrine\Rest\Action\Related\RelatedItemAction;
use Doctrine\Rest\Action\Relationships\RelationshipsCollectionCreateAction;
use Doctrine\Rest\Action\Relationships\RelationshipsCollectionDeleteAction;
use Doctrine\Rest\Action\Relationships\RelationshipsItemDeleteAction;
use Doctrine\Rest\Exceptions\RestException;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestRequest;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\RestResponseFactory;
use Doctrine\Rest\Tests\Entities\Role;
use Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Doctrine\Rest\Tests\Entities\User;
use Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionsWithErrorsTest extends TestCase
{

    public function test_related_action_wrong_type()
    {
        $request = new RestRequest(new Request(['id' => 1], ['data' => ['id' => 33333, 'type' => 'fasfa']]));
        $response = $this->getUserRelationshipsRoleUpdateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => [
            [
                'code' => 'invalid-data',
                'source' => ['pointer' => 'role'],
                'detail' => 'Type is not in sync with relation.',
            ],
        ]], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 1], ['data' => ['type' => 'fasfa']]));
        $response = $this->getUserRelationshipsRoleUpdateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => [
            [
                'code' => 'invalid-data',
                'source' => ['pointer' => 'role'],
                'detail' => 'Relation item without `id` or `type`.',
            ],
        ]], json_decode($response->getContent(), true));
    }

    public function test_forbidden()
    {
        $this->assertEquals(Response::HTTP_FORBIDDEN, RestResponse::createForbidden()->getStatusCode());
        $this->assertEquals(Response::HTTP_FORBIDDEN, RestException::createForbidden()->getCode());
    }

    public function test_relationships_no_getter()
    {
        $action = new RelatedItemAction(
            RestRepository::create($this->em, User::class), 'not_exist',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => [
            [
                'code' => 'missing-getter',
                'source' => [
                    'entity' => User::class,
                    'pointer' => 'user.not_exist',
                    'getter' => 'getNot_exist'],
                'detail' => 'Missing field getter.',
            ],
        ]], json_decode($response->getContent(), true));
    }

    public function test_relationships_no_setter()
    {
        $action = new RelationshipsItemDeleteAction(
            RestRepository::create($this->em, User::class), 'not_exist',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => [
            [
                'code' => 'missing-setter',
                'source' => [
                    'entity' => User::class,
                    'pointer' => 'user.not_exist',
                    'setter' => 'setNot_exist'],
                'detail' => 'Missing field setter.',
            ],
        ]], json_decode($response->getContent(), true));
    }

    public function test_relationships_no_adder()
    {
        $action = new RelationshipsCollectionCreateAction(
            RestRepository::create($this->em, User::class), 'not_exist', 'not_exist',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        $request = new RestRequest(new Request(['id' => 1], ['data' => [['id' => 1, 'type' => Role::getResourceKey()]]]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => [
            [
                'code' => 'missing-adder',
                'source' => [
                    'entity' => User::class,
                    'pointer' => 'user.not_exist',
                    'adder' => 'addNot_exist'],
                'detail' => 'Missing collection adder.',
            ],
        ]], json_decode($response->getContent(), true));
    }

    public function test_relationships_no_remover()
    {
        $action = new RelationshipsCollectionDeleteAction(
            RestRepository::create($this->em, User::class), 'not_exist',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        $request = new RestRequest(new Request(['id' => 1], ['data' => [['id' => 1, 'type' => Role::getResourceKey()]]]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => [
            [
                'code' => 'missing-remover',
                'source' => [
                    'entity' => User::class,
                    'pointer' => 'user.not_exist',
                    'remover' => 'removeNot_exist'],
                'detail' => 'Missing collection remover.',
            ],
        ]], json_decode($response->getContent(), true));
    }

    public function test_validation()
    {
        $action = new CreateAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        );

        $request = new RestRequest(new Request([], ['data' => ['attributes' => []]]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(
            [
                'errors' => [
                    [
                        'code' => 'validation',
                        'source' => ['pointer' => 'email'],
                        'detail' => 'This value should not be null.'
                    ],
                    [
                        'code' => 'validation',
                        'source' => ['pointer' => 'name'],
                        'detail' => 'This value should not be null.'
                    ],
                ]
            ],
            json_decode($response->getContent(), true)
        );

        $request = new RestRequest(new Request([], ['data' => ['attributes' => [
            'name' => 'Test',
            'email' => 'wrong-email',
        ]]]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(
            [
                'errors' => [
                    [
                        'code' => 'validation',
                        'source' => ['pointer' => 'email'],
                        'detail' => 'This value is not a valid email address.'
                    ],
                ]
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function test_generate_rest_exception_from_exception()
    {
        $this->assertInstanceOf(RestResponse::class, RestResponse::exception(new \Exception()));
    }

    public function test_exception()
    {
        $action = new CreateAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            function() {}
        );

        $request = new RestRequest(new Request([]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            [
                'errors' => [
                    [
                        'code' => 'missing-root-data',
                        'source' => ['pointer' => ''],
                        'detail' => 'Missing `data` member at document top level.'
                    ],
                ]
            ],
            json_decode($response->getContent(), true)
        );
    }

    public function test_not_found()
    {
        $action = new ItemAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        );

        $response = $action->dispatch($request = new RestRequest(new Request(['id' => 666])));

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(RestResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
