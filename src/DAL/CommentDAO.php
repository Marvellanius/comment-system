<?php


namespace Commentsystem\DAL;


class CommentDAO
{
    public int $id;
    public string $content;
    public int $parent_id;
    public string $created_at;
    public int $user_id;
    public string $path;
    public int $depth;
}