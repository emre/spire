<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/settings.php';
require_once __DIR__.'/resources.php';
require_once __DIR__.'/utils.php';

use Spire\Settings;
use Spire\Resources;
use Spire\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Silex\Application;


$app = new Application();
$app['debug'] = TRUE;

$app->register(new Sfk\Silex\Provider\MongoDBServiceProvider(), array(
    'mongodb.server' => $MONGODB_CONNECTION_URI,
    'mongodb.options' => $MONGODB_OPTIONS,
));


# workaround to accept JSON body for creating resources.
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->json_content = $data;
    }
});

/*
 * GET ENDPOINT/users
 */

$app->get('/{collection_name}/',  function (Application $app, Request $request, $collection_name) use ($RESOURCE_CLASS) {
    $collection = new MongoCollection($app['mongodb'], $collection_name);
    $resource = new $RESOURCE_CLASS($collection, $request->query);
    $data = json_encode($resource->listItems());

    return new Utils\JsonResponse($data);
});

/*
 * GET ENDPOINT/users/:mongo_id:
 */

$app->get('/{collection_name}/{id}/', function(Application $app, Request $request, $collection_name, $id) use ($RESOURCE_CLASS) {
    $collection = new MongoCollection($app['mongodb'], $collection_name);

    $resource = new $RESOURCE_CLASS($collection, $request->query);
    try {
        $data = json_encode($resource->getItem(new MongoId($id)));
    } catch (MongoException  $exception) {
        return new Utils\JsonResponse('Invalid ID.', '200', array(), TRUE);
    }

    if($data == "null") {
        return new Utils\JsonResponse('Document Not Found.', '404', array(), TRUE);
    }
    return new Utils\JsonResponse($data);
});

$app->post('/{collection_name}/', function(Application $app, Request $request, $collection_name) use ($RESOURCE_CLASS) {
    $collection = new MongoCollection($app['mongodb'], $collection_name);
    $resource = new $RESOURCE_CLASS($collection, $request->query);
    $data = $resource->createItem($request->request->json_content);

    if(!$request->request->json_content) {
        return new Response('Body data Not Found', '200', array(), TRUE);
    }

    $headers = ["Location" => sprintf("%s/%s", $collection_name, $data["_id"]), ];
    return new Response('', '201', $headers, TRUE);

});

$app->put('/{collection_name}/{id}/', function(Application $app, Request $request, $collection_name, $id) use ($RESOURCE_CLASS) {
    $collection = new MongoCollection($app['mongodb'], $collection_name);
    $resource = new $RESOURCE_CLASS($collection, $request->query);

    # _id is immutable since it's not supposed to be updated/replaced.
    unset($request->request->json_content["_id"]);

    $resource->replaceItem(new MongoId($id), $request->request->json_content);

    return new Response('', '204', array(), TRUE);

});

$app->delete('/{collection_name}/{id}/', function(Application $app, Request $request, $collection_name, $id) use ($RESOURCE_CLASS) {
    $collection = new MongoCollection($app['mongodb'], $collection_name);
    $resource = new $RESOURCE_CLASS($collection, $request->query);

    $resource->deleteItem(new MongoId($id));

    return new Response('', '204', array(), TRUE);

});

