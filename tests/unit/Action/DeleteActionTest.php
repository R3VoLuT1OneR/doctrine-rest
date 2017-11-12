<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\DeleteAction;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DeleteActionTest extends TestCase
{
    public function test_delete_user()
    {
        $action = new DeleteAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new RestResponseFactory(),
            new UserTransformer()
        );

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
