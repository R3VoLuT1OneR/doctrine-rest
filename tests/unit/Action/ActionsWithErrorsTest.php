<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\ItemAction;
use Pz\Doctrine\Rest\RestException;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;

class ActionsWithErrorsTest extends TestCase
{

    public function test_exception()
    {
        $action = new ItemAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new RestResponseFactory(),
            function () {
                throw new RestException();
            }
        );

        $response = $action->dispatch($request = new RestRequest(['id' => 1]));
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(RestResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_not_found()
    {
        $action = new ItemAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new RestResponseFactory(),
            new UserTransformer()
        );

        $response = $action->dispatch($request = new RestRequest(['id' => 666]));

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(RestResponse::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
