<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\CollectionAction;
use Pz\Doctrine\Rest\Action\CreateAction;
use Pz\Doctrine\Rest\Action\ItemAction;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionsWithErrorsTest extends TestCase
{

    public function test_forbidden()
    {
        $this->assertEquals(Response::HTTP_FORBIDDEN, RestResponse::createForbidden()->getStatusCode());
        $this->assertEquals(Response::HTTP_FORBIDDEN, RestException::createForbidden()->getCode());
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

    public function test_exception()
    {
        $action = new ItemAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            function () {
                throw new \Exception();
            }
        );

        $request = new RestRequest(new Request(['id' => 1]));
        $response = $action->dispatch($request);
        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(RestResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

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
