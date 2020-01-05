<?php


namespace Commentsystem\Models;


class Comment
{
    /**
     * has string CONTENT
     * has UserDAO AUTHOR
     * has DateTime CREATED_AT
     * has 0..many int RATING
     * has Article|Comment PARENT
     * ALLOWED_RATINGS: [-1, 0, +1, +2, +3]
     */

    public int $id; // Assigned after first write to DB
    public string $content;
    public User $author;
    public int $created_at;
    public array $ratings;
    public int $depth;

    public function __construct(User $author, string $content, int $depth, int $created_at)
    {
        $this->created_at = $created_at;
        $this->content = $content;
        $this->author = $author;
        $this->depth = $depth;
    }
}