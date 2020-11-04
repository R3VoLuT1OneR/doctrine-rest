<?php namespace Doctrine\Rest\Tests\Action;

use Doctrine\Rest\Action\DeleteAction;
use Doctrine\Rest\RestResponseFactory;
use Doctrine\Rest\RestRepository;
use Doctrine\Rest\RestRequest;
use Doctrine\Rest\RestResponse;
use Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Doctrine\Rest\Tests\Entities\User;
use Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class DeleteActionTest extends TestCase
{
    public function test_delete_user()
    {
        $action = new DeleteAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        );

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
