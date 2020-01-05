<?php


namespace Commentsystem\Factories;


use Commentsystem\DAL\CommentDAO;
use Commentsystem\Models\Comment;
use Commentsystem\Services\UserService;

class CommentFactory
{
    public function createFromDAO(CommentDAO $dao)
    {
        $user_service = new UserService();
        $user = $user_service->findById($dao->user_id);
        $created_at = $dao->created_at;
        return new Comment($user, $dao->content, $dao->depth, $created_at);
    }
}