<?php namespace Doctrine\Rest\Tests\Action\Related;

use Doctrine\Rest\Action\Related\RelatedItemAction;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestRequest;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\Tests\Entities\Role;
use Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Doctrine\Rest\Tests\Entities\User;
use Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ItemActionTest extends TestCase
{

    public function test_item_related_manage_action()
    {
        $request = new RestRequest(new Request(['id' => 1], ['data' => [
            'attributes' => [
                'name' => 'new role',
            ]
        ]]));
        $response = $this->getUserRelatedRoleItemCreateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertResponseContent(['data' => [
            'id' => '3',
            'type' => Role::getResourceKey(),
            'attributes' => [
                'name' => 'new role',
            ]
        ]], $response);


        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelatedRoleItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            'id' => '3',
            'type' => Role::getResourceKey(),
            'attributes' => ['name' => 'new role'],
            'links' => ['self' => '/role/3']
        ]], json_decode($response->getContent(), true));


        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelatedRoleItemDeleteAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(RestResponse::HTTP_NO_CONTENT, $response->getStatusCode());

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelatedRoleItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => null], json_decode($response->getContent(), true));

        $request = new RestRequest(new Request(['id' => 3]));
        $response = $this->getRoleItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_item_related_action()
    {
        $request = new Request(['id' => 1]);
        $response = $this->getUserRelatedRoleItemAction()->dispatch(new RestRequest($request));

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
        $response = $this->getUserRelatedRoleItemAction()->dispatch(new RestRequest($request));

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => null], json_decode($response->getContent(), true));
    }
}
