<?php namespace Pz\Doctrine\Rest\Traits;

use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestRepository;

trait RelatedAction
{
    /**
     * @var RestRepository
     */
    protected $related;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $mappedBy;

    /**
     * Related repository.
     *
     * @return RestRepository
     */
    public function related()
    {
        return $this->related;
    }

    /**
     * `field` on base entity that identify relation.
     *
     * @return string
     */
    public function field()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function mappedBy()
    {
        return $this->mappedBy;
    }

    /**
     * @param $item
     *
     * @return null|object
     * @throws RestException
     */
    protected function getRelatedEntity($item)
    {
        if (!isset($item['id']) || !isset($item['type'])) {
            throw RestException::createUnprocessable('Delete item without identifiers.')
                ->error(
                    'invalid-data',
                    ['pointer' => $this->field()],
                    'Delete item without `id` or `type`.'
                );
        }

        if ($this->related()->getResourceKey() !== $item['type']) {
            throw RestException::createUnprocessable('Different resource type in delete request.')
                ->error('invalid-data', ['pointer' => $this->field()], 'Type is not in sync with relation.');
        }

        return $this->related()->findById($item['id']);
    }
}
