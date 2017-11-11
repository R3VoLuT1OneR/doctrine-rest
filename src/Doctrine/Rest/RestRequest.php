<?php namespace Pz\Doctrine\Rest;

use Symfony\Component\HttpFoundation\Request;

class RestRequest extends Request
{
    /**
     * Json API type.
     */
    const JSON_API_CONTENT_TYPE = 'application/vnd.api+json';

    /**
     * Default limit for list.
     */
    const DEFAULT_LIMIT = 1000;

    /**
     * Authorize rest request.
     * Entity will be object for get,update,delete actions.
     * Entity will be string for index,create action.
     *
     * @param object|string $entity
     *
     * @return mixed
     */
    public function authorize($entity)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAcceptJsonApi()
    {
        return in_array(static::JSON_API_CONTENT_TYPE, $this->getAcceptableContentTypes());
    }

    /**
     * @return int|array
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * Get jsonapi fieldsets.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->get('fields');
    }

    /**
     * @return mixed|null
     */
    public function getFilter()
    {
        if ($query = $this->get('filter')) {
            if (is_string($query) && (null !== ($json = json_decode($query, true)))) {
                return $json;
            }

            return $query;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getOrderBy()
    {
        if ($sort = $this->get('sort')) {
            $fields = explode(',', $sort);
            $orderBy = [];

            foreach ($fields as $field) {
                if (empty($field)) continue;

                $direction = 'ASC';
                if ($field[0] === '-') {
                    $field = substr($field, 1);
                    $direction = 'DESC';
                }

                $orderBy[$field] = $direction;
            }

            return $orderBy;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getStart()
    {
        if (($page = $this->get('page')) && $this->getLimit() !== null) {
            if (isset($page['number']) && is_numeric($page['number'])) {
                return ($page['number'] - 1) * $this->getLimit();
            }

            return isset($page['offset']) && is_numeric($page['offset']) ? (int) $page['offset'] : 0;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        if ($page = $this->get('page')) {
            if (isset($page['number']) && is_numeric($page['number'])) {
                return isset($page['size']) && is_numeric($page['size']) ?
                    (int) $page['size'] : $this->getDefaultLimit();
            }

            return isset($page['limit']) && is_numeric($page['limit']) ?
                (int) $page['limit'] : $this->getDefaultLimit();
        }

        return null;
    }

    /**
     * @return array|string|null
     */
    public function getInclude()
    {
        return $this->get('include');
    }

    /**
     * @return array|string|null
     */
    public function getExclude()
    {
        return $this->get('exclude');
    }

    /**
     * @return int
     */
    protected function getDefaultLimit()
    {
        return static::DEFAULT_LIMIT;
    }
}
