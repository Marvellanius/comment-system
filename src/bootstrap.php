<?php
require_once __DIR__.'/../vendor/autoload.php';

use Commentsystem\Request;
use Commentsystem\Response;
use Commentsystem\Container;

$container = Container::getInstance();

header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
header("Access-Control-Allow-Headers: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: DNT,UserDAO-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range");
header("Access-Control-Expose-Headers: Content-Length,Content-Range");

$request = new Request();
$response = new Response();

$routes = [
    "GET" => [
        'articles/[\d0-9]+' => [
            'controller' => 'ArticleController',
            'action' => 'getWithComments',
            'param_index' => 2,
        ],
        'comments/[\d0-9]+/tree' => [
            'controller' => 'CommentController',
            'action' => 'listCommentTreeForComment',
            'param_index' => 2,
        ],
        'comments/[\d0-9]+' => [
            'controller' => 'CommentController',
            'action' => 'get',
            'param_index' => 2,
        ],
    ],
    "POST" => [
        "comments/" => [
            'controller' => 'CommentController',
            'action' => 'create',
        ],
        "comments/[\d0-9]+" => [
            'controller' => 'CommentController',
            'action' => 'update',
        ]
    ],
];
$request_method = $request->method;

// If options request, return and do nothing
if ($request_method === "OPTIONS") {
    return;
}

$uri = ltrim($request->getUri(), '/');
foreach (array_keys($routes[$request_method]) as $regex) {
    if (preg_match("~^" . $regex . "$~", $uri)) {
        $route = $routes[$request_method][$regex];
        dispatch($route, $request, $response);
        break;
    } else {
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->withHeaders([
            'Content-Type: application/json',
        ]);
        $response->setContent(json_encode('Route not implemented'));
    }
}

$response->send();

function dispatch(array $route, Request $request, Response $response)
{
    $request->addParams("id", $request->getUrlElements()[$route['param_index']]);
    $controllerPath = 'Commentsystem\Controllers\\' . $route['controller'];
    $controller = new $controllerPath;
    $function = $route['action'];
    $controller->$function($request, $response);
}
