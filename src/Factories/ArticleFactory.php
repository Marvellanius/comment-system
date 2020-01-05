<?php


namespace Commentsystem\Factories;


use Commentsystem\DAL\ArticleDAO;
use Commentsystem\Models\Article;

class ArticleFactory
{
    public function createFromDAO(ArticleDAO $dao)
    {
        $id = $dao->id;
        $title = $dao->title;
        $content = $dao->content;
        $created_at = $dao->created_at;
        return new Article($id, $title, $content, $created_at);
    }
}