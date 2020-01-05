<?php


namespace Commentsystem\Models;


class User
{
    public string $username;
    public int $id;

    public function __construct(string $username, int $id)
    {
        $this->username = $username;
        $this->id = $id;
    }
}