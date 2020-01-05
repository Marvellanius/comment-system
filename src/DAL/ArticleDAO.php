<?php


namespace Commentsystem\DAL;


class ArticleDAO
{
    public int $id;
    public string $created_at;
    public string $title;
    public string $content;
    public int $user_id;
}