<?php namespace Pz\Doctrine\Rest\Response;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

use Pz\Doctrine\Rest\Action\Index\ResponseDataInterface;
use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\Doctrine\Rest\RestRequestInterface;
use Pz\Doctrine\Rest\RestResponseInterface;

class FractalResponse implements RestResponseInterface
{
    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * @var Manager|null
     */
    protected $fractal;

    /**
     * FractalResponse constructor.
     *
     * @param TransformerAbstract $transformer
     */
    public function __construct(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Return configured fractal by request format.
     *
     * @param RestRequestInterface $request
     *
     * @return Manager
     */
    protected function fractal(RestRequestInterface $request)
    {
        if ($this->fractal === null) {
            $this->fractal = new Manager();
        }

        return $this->fractal;
    }

    /**
     * @return TransformerAbstract
     */
    protected function transformer()
    {
        return $this->transformer;
    }

    /**
     * @param IndexRequestInterface $request
     * @param ResponseDataInterface $response
     *
     * @return array
     */
    public function index(IndexRequestInterface $request, ResponseDataInterface $response)
    {
        $resource = new Collection($response->data(), $this->transformer());
        $resource->setMetaValue('count', $response->count());
        $resource->setMetaValue('limit', $response->limit());
        $resource->setMetaValue('start', $response->start());

        return $this->fractal($request)
            ->createData($resource)
            ->toArray();
    }

    /**
     * @param ShowRequestInterface $request
     * @param             $entity
     *
     * @return array
     */
    public function show(ShowRequestInterface $request, $entity)
    {
        $resource = new Item($entity, $this->transformer());

        return $this->fractal($request)
            ->createData($resource)
            ->toArray();
    }

    /**
     * @param CreateRequestInterface $request
     * @param               $entity
     *
     * @return array
     */
    public function create(CreateRequestInterface $request, $entity)
    {
        $resource = new Item($entity, $this->transformer());

        return $this->fractal($request)
            ->createData($resource)
            ->toArray();
    }

    /**
     * @param UpdateRequestInterface $request
     * @param               $entity
     *
     * @return array
     */
    public function update(UpdateRequestInterface $request, $entity)
    {
        $resource = new Item($entity, $this->transformer());

        return $this->fractal($request)
            ->createData($resource)
            ->toArray();
    }

    /**
     * @param DeleteRequestInterface $request
     * @param               $entity
     *
     * @return array
     */
    public function delete(DeleteRequestInterface $request, $entity)
    {
        return null;
    }
}
