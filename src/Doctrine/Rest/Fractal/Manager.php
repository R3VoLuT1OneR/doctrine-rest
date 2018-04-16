<?php namespace Pz\Doctrine\Rest\Fractal;

use League\Fractal\ScopeFactoryInterface;
use Pz\Doctrine\Rest\Contracts\RestRequestContract;

class Manager extends \League\Fractal\Manager
{
    /**
     * @var RestRequestContract
     */
    protected $request;

    public function __construct(ScopeFactoryInterface $scopeFactory, RestRequestContract $request)
    {
        parent::__construct($scopeFactory);
        $this->request = $request;
    }

    /**
     * @return RestRequestContract
     */
    public function request()
    {
        return $this->request;
    }
}
