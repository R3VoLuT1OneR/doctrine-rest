<?php namespace Doctrine\Rest\Fractal;

use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\JsonApiSerializer;
use Psr\Http\Message\ServerRequestInterface;

class BaseManagerFactory implements ManagerFactoryInterface
{
    const PARAM_INCLUDE = 'include';
    const PARAM_EXCLUDE = 'exclude';
    const PARAM_FIELDS = 'fields';

    const CONTENT_TYPE_JSONAPI = 'application/vnd.api+json';
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @param ServerRequestInterface $request
     * @return Manager
     */
    public function createManager(ServerRequestInterface $request): Manager
    {
        $manager = new Manager();

        $this->setupSerializer($manager, $request);
        $this->setupFieldsAndIncludes($manager, $request);

        return $manager;
    }

    /**
     * This method must setup serializer that will be used by fractal.
     * Depends on content type we may setup serializer.
     *
     * @param Manager $manager
     * @param ServerRequestInterface $request
     */
    protected function setupSerializer(Manager $manager, ServerRequestInterface $request): void
    {
        $acceptContentType = $request->getHeaderLine('Accept');

        if ($acceptContentType === static::CONTENT_TYPE_JSONAPI) {
            $manager->setSerializer(new JsonApiSerializer());
            return;
        }

        if ($acceptContentType === static::CONTENT_TYPE_JSON) {
            $manager->setSerializer(new ArraySerializer());
            return;
        }
    }

    /**
     * Setup manager that includes and fields.
     *
     * @param Manager $manager
     * @param ServerRequestInterface $request
     */
    protected function setupFieldsAndIncludes(Manager $manager, ServerRequestInterface $request): void
    {
        $params = $request->getQueryParams();

        if (isset($params[static::PARAM_INCLUDE])) {
            $manager->parseIncludes($params[static::PARAM_INCLUDE]);
        }

        if (isset($params[static::PARAM_EXCLUDE])) {
            $manager->parseExcludes($params[static::PARAM_EXCLUDE]);
        }

        if (isset($params[static::PARAM_FIELDS])) {
            $manager->parseFieldsets($params[static::PARAM_FIELDS]);
        }
    }
}