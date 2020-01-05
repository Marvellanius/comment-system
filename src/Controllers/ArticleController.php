<?php


namespace Commentsystem\Controllers;


use Commentsystem\Request;
use Commentsystem\Response;
use Commentsystem\Services\ArticleService;

class ArticleController
{
    private $service;

    public function __construct()
    {
        $this->service = new ArticleService();
    }

    public function getWithComments(Request $request, Response $response): Response
    {
        $object = $this->service->findWithComments($request->get('id'));
        $body = [
            'results' => $object
        ];
        $response->setContent(json_encode($body));
        $response->withHeaders([
            'Content-Type: application/json',
        ]);

        return $response;
    }
}