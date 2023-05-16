<?php namespace Pz\Doctrine\Rest\Tests\Action\Relationships;

use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Role;
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
        $response = $this->getUserRelationshipsRoleItemDeleteAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());


        $request = new RestRequest(new Request(['id' => 1]));
        $response = $this->getUserRelationshipsRoleItemAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => null], json_decode($response->getContent(), true));


        $request = new RestRequest(new Request(['id' => 1], content: json_encode([
            'data' => ['id' => 2, 'type' => Role::getResourceKey()]
        ])));
        $response = $this->getUserRelationshipsRoleUpdateAction()->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['data' => [
            'id' => '2',
            'type' => Role::getResourceKey(),
            'links' => ['self' => '/role/2']
        ]], json_decode($response->getContent(), true));

    }
}
