<?php

namespace Commentsystem\DAL;

use Commentsystem\Container;
use PDO;

class CommentRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Container::getInstance()->getDatabase();
    }

    public function find($id): CommentDAO
    {
        $stmt = $this->connection->prepare('
            SELECT comment.* 
             FROM comment 
             WHERE id = :id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Set the fetchmode to populate an instance of 'Comment'
        return $stmt->fetchObject(CommentDAO::class);
    }

    public function save(CommentDAO $comment): bool
    {
        // If the ID is set, we're updating an existing record
        if (isset($comment->id)) {
            return $this->update($comment);
        }
        $time = time();

        $stmt = $this->connection->prepare('
            INSERT INTO comment 
                (created_at, content, user_id, parent_id) 
            VALUES 
                (:created_at, :content, :user_id, :parent_id)
        ');
        $stmt->bindParam(':created_at', $time);
        $stmt->bindParam(':parent_id', $comment->parent_id);
        $stmt->bindParam(':user_id', $comment->user_id);
        $stmt->bindParam(':content', $comment->content);
        return $stmt->execute();
    }

    public function update(CommentDAO $comment): bool
    {
        if (!isset($comment->id)) {
            // We can't update a record unless it exists...
            throw new \LogicException(
                'Cannot update comment that does not yet exist in the database.'
            );
        }
        $stmt = $this->connection->prepare('
            UPDATE comment
            SET content = :content
            WHERE id = :id AND user_id = :user_id
        ');
        $stmt->bindParam(':content', $comment->content);
        $stmt->bindParam(':user_id', $comment->user_id);
        $stmt->bindParam(':id', $comment->id);
        return $stmt->execute();
    }


    /**
     * Uses MySQL 8.0 'WITH RECURSIVE' support to get a comment tree efficiently, given the adjacency list table structure.
     * The Comment with the provided $id will be the root comment for the returned tree.
     * The returned tree will be sorted with children directly after parents
     *
     * @param string $id
     *
     * @return CommentDAO[]
     */
    public function getCommentTreeWithRootCommentId(string $id): array
    {
        $stmt = $this->connection->prepare('
            WITH RECURSIVE tree AS
            (
                SELECT id, content, user_id, parent_id, CAST(id as CHAR(500)) AS path, 1 AS depth
                FROM comment
                WHERE id = :id
                UNION ALL
                SELECT c.id, c.content, c.user_id, c.parent_id, CONCAT(t.path, \',\', c.id), t.depth+1
                FROM tree t, comment c
                WHERE c.parent_id = t.id
            )
            SELECT * FROM tree ORDER BY path
        ');
        $stmt->bindParam(':id', $id);
        $stmt->setFetchMode(PDO::FETCH_CLASS, CommentDAO::class);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Uses MySQL 8.0 'WITH RECURSIVE' support to get a comment tree efficiently, given the adjacency list table structure.
     * The returned tree will be sorted with children directly after parents
     *
     * @param array $ids
     *
     * @return CommentDAO[]
     */
    public function getCommentTreeForComments(array $ids): array
    {
        $ids_in = implode(',', $ids);
        $stmt = $this->connection->prepare('
            WITH RECURSIVE tree AS
            (
                SELECT id, created_at, content, user_id, parent_id, CAST(id as CHAR(500)) AS path, 1 AS depth
                FROM comment
                WHERE id IN ('.$ids_in.')
                UNION ALL
                SELECT c.id, c.created_at, c.content, c.user_id, c.parent_id, CONCAT(t.path, \',\', c.id), t.depth+1
                FROM tree t, comment c
                WHERE c.parent_id = t.id
            )
            SELECT * FROM tree ORDER BY path
        ');

        $stmt->setFetchMode(PDO::FETCH_CLASS, CommentDAO::class);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRatingsForComment($id)
    {
        $stmt = $this->connection->prepare('
            SELECT r.value AS rating, COUNT(cr.rating_id) AS rating_count
            FROM comment_ratings cr
            INNER JOIN rating r
                ON cr.rating_id = r.id
            WHERE cr.comment_id = :id
            GROUP BY r.id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAggregatedRatingsForComment($id): array
    {
        $stmt = $this->connection->prepare('
            SELECT ROUND(AVG(r.value)) AS avg_rating, COUNT(cr.rating_id) AS rating_count, SUM(r.value) AS total_rating
            FROM rating r
            INNER JOIN comment_ratings cr
                ON r.id = cr.rating_id
                WHERE cr.comment_id = :id
            GROUP BY cr.comment_id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}