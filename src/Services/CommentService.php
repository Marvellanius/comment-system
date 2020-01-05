<?php


namespace Commentsystem\Services;


use Commentsystem\DAL\CommentRepository;
use Commentsystem\Factories\CommentFactory;
use Commentsystem\Models\Comment;

class CommentService
{
    private $repository;
    private $factory;

    public function __construct()
    {
        $this->repository = new CommentRepository();
        $this->factory = new CommentFactory();
    }

    public function find(string $id): Comment
    {
        return $this->factory->createFromDAO($this->repository->find($id));
    }

    public function listCommentTreeForComment(string $id): array
    {
        return $this->repository->getCommentTreeWithRootCommentId($id);
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