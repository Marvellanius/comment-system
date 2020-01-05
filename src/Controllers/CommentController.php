<?php

namespace Commentsystem\Controllers;

use Commentsystem\Request;
use Commentsystem\Response;
use Commentsystem\Services\CommentService;

class CommentController
{
    private $repository;

    public function __construct()
    {
        $this->service = new CommentService();
    }

    public function get(Request $request, Response $response): Response
    {
        $comment = $this->service->find($request->get('id'));
        $body = [
            'results' => $comment
        ];
        $response->setContent(json_encode($body));

        return $response;
    }

    public function listCommentTreeForComment(Request $request, Response $response): Response
    {
        $comments = $this->service->getCommentTreeWithRootCommentId($request->get('id'));

        $body = [
            'results' => $comments
        ];
        $response->setContent(json_encode($body));
        $response->withHeaders([
            'Content-Type: application/json',
        ]);

        return $response;
    }
}