# doctrine-rest

[![Build Status](https://travis-ci.org/R3VoLuT1OneR/doctrine-rest.svg?branch=master)](https://travis-ci.org/R3VoLuT1OneR/doctrine-rest)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/R3VoLuT1OneR/doctrine-rest?branch=master)

Framework agnostic, library provides basic tools for implementation of [JSON API](http://jsonapi.org/format/) 

Using [`symfony/http-foundation`](https://symfony.com/doc/current/components/http_foundation.html) for requests/responses and [`league/fractal`](https://fractal.thephpleague.com/) for Rest response build.

## Install

Add composer package to your project

    composer require pz/doctrine-rest


## Usage

Package provides different actions for data manipulation and formatting.

Create entity and fractal [trasformer](https://fractal.thephpleague.com/transformers/) for the entity.

    // Entity class to work with
    $entityClass = 'User';
    $entityTransformer = new EntityTransformer();

If you want to use JSON API please implement `JsonApiResource` on your doctrine entity.

Change entity repository to `RestRepository` or create new one.

    // Provide configured entity manager
    $entityManager = getEntityManager()
    
    // Repository that action will work with
    $restRepository = new RestRepository($entityManager, $entityManager->getClassMetadata($entityClass));

### Collection (REST Index) action

Route request `http://localhost/api/{resourceKey}`. If you want to get JSON API response just add `Accept` header `application/vnd.api+json`

    // Get http request from framework or init yourself
    $httpRequest = Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $restRequest = new RestRequest($httpRequest);

    $action = new CollectionAction(
        $restRepository,
        $entityTransformer
    );

    /** @var RestResponse|Symfony\Component\HttpFoundation\Response */
    $response = $action->dispatch($restRequest);
 
 Regular response
 
    {
        'data': [
            { ...transformer generated entity },
            { ...transformer generated entity },
            { ...transformer generated entity },
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
        ]
    }


