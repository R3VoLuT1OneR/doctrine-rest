<?php namespace Doctrine\Rest\Traits;

use Doctrine\Rest\Exceptions\RestException;
use Symfony\Component\Validator\Validation;

trait CanValidate
{
    /**
     * @param object $entity
     *
     * @return object
     * @throws RestException
     */
    protected function validateEntity($entity)
    {
        $errors = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator()
            ->validate($entity);

        if ($errors->count() > 0) {
            throw RestException::createFromConstraintViolationList($errors);
        }

        return $entity;
    }
}
