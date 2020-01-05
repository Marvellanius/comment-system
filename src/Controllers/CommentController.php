<?php

namespace Commentsystem\Controllers;

use Commentsystem\Request;
use Commentsystem\DAL\CommentRepository;
use Commentsystem\Response;

class CommentController
{
    private $repository;

    public function __construct()
    {
        $this->repository = new CommentRepository();
    }

    public function get(Request $request, Response $response): Response
    {
        $comment = $this->repository->find($request->get('id'));
        $body = [
            'results' => $comment
        ];
        $response->setContent(json_encode($body));

        return $response;
    }

    public function listCommentTreeForComment(Request $request, Response $response): Response
    {
        $comments = $this->repository->getCommentTreeWithRootCommentId($request->get('id'));

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