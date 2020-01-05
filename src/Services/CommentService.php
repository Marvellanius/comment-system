<?php


namespace Commentsystem\Services;


use Commentsystem\DAL\CommentRepository;
use Commentsystem\Factories\CommentFactory;

class CommentService
{
    private $repository;
    private $factory;

    public function __construct()
    {
        $this->repository = new CommentRepository();
        $this->factory = new CommentFactory();
    }

    public function getCommentTreeForComments(array $comment_ids): array
    {
        $comments = $this->repository->getCommentTreeForComments($comment_ids);
        $output_comments = [];
        foreach($comments as $comment) {
            $output_comment = $this->factory->createFromDAO($comment);
            $ratings = $this->repository->getAggregatedRatingsForComment($comment->id);
            $output_comment->ratings = $ratings;
            $output_comments[] = $output_comment;
        }

        return $output_comments;
    }
}