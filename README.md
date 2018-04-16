# doctrine-rest

[![Build Status](https://travis-ci.org/R3VoLuT1OneR/doctrine-rest.svg?branch=master)](https://travis-ci.org/R3VoLuT1OneR/doctrine-rest)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest?branch=master)

Framework agnostic, library provides basic tools for implementation of [JSON API](http://jsonapi.org/format/) over Doctrine library

Using by default [`symfony/http-foundation`](https://symfony.com/doc/current/components/http_foundation.html) for requests/responses and [`league/fractal`](https://fractal.thephpleague.com/) for Rest response build.

## Install

Add composer package to your project

    composer require pz/doctrine-rest


## Usage

Package provides different actions for data manipulation and formatting.

Create entity and fractal [trasformer](https://fractal.thephpleague.com/transformers/) for the entity.


    // Entity class to work with
    $entityClass = 'User';
    $entityTransformer = new EntityTransformer();

If you want to use JSON API please implement `JsonApiResource` on your doctrine entity and add next header to request:

    Accept: application/vnd.api+json

Change entity repository to `RestRepository` or create new one.

    // Provide configured entity manager
    $entityManager = getEntityManager()
    
    // Repository that action will work with
    $restRepository = new RestRepository($entityManager, $entityManager->getClassMetadata($entityClass));

Prepare `RestRequest` entity or implement `RestRequestContract` on your custom `RestRequest` implementation.

    // Get http request from framework or init yourself
    $httpRequest = Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $restRequest = new RestRequest($httpRequest);

### Collection (Index) action

Route request `GET http://localhost/api/{resourceKey}`

    $action = new CollectionAction($restRepository, $entityTransformer);

    /** @var RestResponse|Symfony\Component\HttpFoundation\Response */
    $response = $action->dispatch($restRequest);
 
Regular response
 
    {
        'data': [
            { ...transformer data },
            { ...transformer data },
            { ...transformer data },
        ],
        'meta': [
            'pagination': { ... paginator data },
        ]
    }

Json api response

    {
        'data': [
            {
                'id': {entityId},
                'type': {etntityResourceKey},
                'attributes': { ...transformer data },
                'relationships': { ..transformer includes },
                'links': {
                    'self': 'http://localhost/api/resourceKey/{entityId}
                }
            },
            ... Other entities
        ],
        'meta': [
            'pagination': { ... paginator data },
        ]
    }

### Item (Get) action

Route request `GET http://localhost/api/{resourceKey}/{id}`.


    $action = new ItemAction($restRepository, $entityTransformer);

    /** @var RestResponse|Symfony\Component\HttpFoundation\Response */
    $response = $action->dispatch($restRequest);
 
Regular response
 
    {
        'data': [
            'id': {id},
             { ...transformer data }
        ],
    }

Json api response

    {
        'data': {
            'id': {entityId},
            'type': {etntityResourceKey},
            'attributes': { ...transformer data },
            'relationships': { ..transformer includes },
            'links': {
                'self': 'http://localhost/api/resourceKey/{entityId}
            }
        },
    }

### Create action

Route request `POST http://localhost/api/{resourceKey}`.


    $action = new CreateAction($restRepository, $entityTransformer);

    /** @var RestResponse|Symfony\Component\HttpFoundation\Response */
    $response = $action->dispatch($restRequest);
 
Regular response
 
    {
        'data': [
            'id': {id},
             { ...transformer data }
        ],
    }

Json api response

    {
        'data': {
            'id': {entityId},
            'type': {etntityResourceKey},
            'attributes': { ...transformer data },
            'relationships': { ..transformer includes },
            'links': {
                'self': 'http://localhost/api/resourceKey/{entityId}
            }
        },
    }

### Update action

Route request `PATCH http://localhost/api/{resourceKey}/{id}`.


    $action = new UpdateAction($restRepository, $entityTransformer);

    /** @var RestResponse|Symfony\Component\HttpFoundation\Response */
    $response = $action->dispatch($restRequest);
 
Regular response
 
    {
        'data': [
            'id': {id},
             { ...transformer data }
        ],
    }

Json api response

    {
        'data': {
            'id': {entityId},
            'type': {etntityResourceKey},
            'attributes': { ...transformer data },
            'relationships': { ..transformer includes },
            'links': {
                'self': 'http://localhost/api/resourceKey/{entityId}
            }
        },
    }

### Delete action

Route request `DELETE http://localhost/api/{resourceKey}/{id}`.


    $action = new DeleteAction($restRepository, $entityTransformer);

    /** @var RestResponse|Symfony\Component\HttpFoundation\Response */
    $response = $action->dispatch($restRequest);

Response

    HTTP STATUS 204 NO CONTENT

# Development

## Generate doctrine migration diff
We using doctrine migrations for unit tests database schema.

```
php ./vendor/bin/doctrine-migrations migrations:diff
```
