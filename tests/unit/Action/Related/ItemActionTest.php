<?php namespace Pz\Doctrine\Rest\Tests\Action\Related;

use Pz\Doctrine\Rest\Action\Related\RelatedItemAction;
use Pz\Doctrine\Rest\Action\Related\RelatedItemCreateAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ItemActionTest extends TestCase
{

    public function getRelatedItemAction()
    {
        return new RelatedItemAction(
            RestRepository::create($this->em, User::class), Role::getResourceKey(),
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    public function getRelatedItemCreateAction()
    {
        return new RelatedItemCreateAction(
            RestRepository::create($this->em, User::class), Role::getResourceKey(),
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    public function test_item_related_role_create_action()
    {
        $request = new Request(['id' => 1], ['data' => [
            'attributes' => [
                'name' => 'test_role',
            ]
        ]]);

        $response = $this->getRelatedItemCreateAction()->dispatch(new RestRequest($request));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            'id' => 3,
            'type' => Role::getResourceKey(),
            'attributes' => ['name' => 'test_role'],
            'links' => ['self' => '/role/3'],
        ]], json_decode($response->getContent(), true));

        $request = new Request(['id' => 1]);
        $response = $this->getRelatedItemAction()->dispatch(new RestRequest($request));

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                'id' => '3',
                'type' => 'role',
                'attributes' => [
                    'name' => 'test_role',
                ],
                'links' => [
                    'self' => '/role/3',
                ]
            ]
        ], json_decode($response->getContent(), true));

        $request = new Request(['id' => 1], ['data' => ['type' => Role::getResourceKey(), 'id' => '1']]);
        $response = $this->getRelatedItemCreateAction()->dispatch(new RestRequest($request));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            'id' => '1',
            'type' => Role::getResourceKey(),
            'attributes' => ['name' => 'Admin'],
            'links' => ['self' => '/role/1'],
        ]], json_decode($response->getContent(), true));
    }

    public function test_item_related_action()
    {
        $request = new Request(['id' => 1]);
        $response = $this->getRelatedItemAction()->dispatch(new RestRequest($request));

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'data' => [
                'id' => '1',
                'type' => 'role',
                'attributes' => [
                    'name' => 'Admin',
                ],
                'links' => [
                    'self' => '/role/1',
                ]
            ]
        ], json_decode($response->getContent(), true));

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setName('test');

        $this->em->persist($user);
        $this->em->flush($user);

        $request = new Request(['id' => $user->getId()]);
        $response = $this->getRelatedItemAction()->dispatch(new RestRequest($request));

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => null], json_decode($response->getContent(), true));
    }
}
