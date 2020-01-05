<?php


namespace Commentsystem\Factories;


use Commentsystem\Models\User;
use Commentsystem\DAL\UserDAO;

class UserFactory
{
    public function createFromDAO(UserDAO $dao): User
    {
        return new User($dao->username, $dao->id);
    }
}