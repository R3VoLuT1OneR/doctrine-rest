<?php namespace Pz\Doctrine\Rest\Tests\Action\Relationships;

use Pz\Doctrine\Rest\Action\Relationships\RelationshipsItemAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsItemDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsItemUpdateAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\RoleTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RelationshipsItemActionTest extends TestCase
{

    public function test_relationships_item_actions()
    {
        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelationshipsRoleItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            'id' => '1',
            'type' => Role::getResourceKey(),
            'links' => ['self' => '/role/1']
        ]], json_decode($response->getContent(), true));


        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelatinshipsRoleItemDeleteAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());


        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelationshipsRoleItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => null], json_decode($response->getContent(), true));


        $request = new RestRequest(new Request(['id' => 1], ['data' => ['id' => 2, 'type' => Role::getResourceKey()]]));
        $response = $this->getUserRelationshipsRoleUpdateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            'id' => '2',
            'type' => Role::getResourceKey(),
            'links' => ['self' => '/role/2']
        ]], json_decode($response->getContent(), true));

    }

    protected function getUserRelationshipsRoleUpdateAction()
    {
        return new RelationshipsItemUpdateAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    protected function getUserRelatinshipsRoleItemDeleteAction()
    {
        return new RelationshipsItemDeleteAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }

    protected function getUserRelationshipsRoleItemAction()
    {
        return new RelationshipsItemAction(
            RestRepository::create($this->em, User::class), 'role',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );
    }
}
