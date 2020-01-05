<?php


namespace Commentsystem\Models;


class Article
{
    /**
     * has string TITLE
     * has string CONTENT
     * has UserDAO AUTHOR
     * has DateTime CREATED_AT
     * has 0..many Comment COMMENT
     */

    public int $id;
    public string $title;
    public string $content;
    public User $author;
    public int $created_at;
    public array $comments;

    public function __construct(int $id, string $title, string $content, int $created_at)
    {
        $this->title = $title;
        $this->content = $content;
        $this->id = $id;
        $this->created_at = $created_at;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @param int $created_at
     */
    public function setCreatedAt(int $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @param array $comments
     */
    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }


}