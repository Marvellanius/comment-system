<?php

namespace Commentsystem\DAL;

use Commentsystem\Container;
use PDO;

class ArticleRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Container::getInstance()->getDatabase();
    }

    public function find($id): ArticleDAO
    {
        $stmt = $this->connection->prepare('
            SELECT article.* 
             FROM article 
             WHERE id = :id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Set the fetchmode to populate an instance of 'Comment'
        // This enables us to use the following:
        //     $comment = $repository->find(1234);
        //     echo $comment->content;

        return $stmt->fetchObject(ArticleDAO::class);
    }

    /**
     * Returns an article with all comments
     * This method should probably be abstracted to a ArticleService class, to keep the Repository clean...
     * @param $id
     *
     * @return array
     */
    public function findWithComments($id): array
    {
        $output = [];
        // Fetch Article first
        $article = $this->find($id);

        $output['article'] = $article;

        // Get all related comment_ids that are the root for their conversation (parent_id === -1)
        $comment_ids = $this->getRootCommentIdsForArticle($id);

        // Flatten the comment_ids array so that it can be imploded by the next function
        $c_ids = [];
        foreach($comment_ids as $c_id) {
            $c_ids[] = $c_id['id'];
        }

        // Get comment trees for the root comments
        $comments = $this->getCommentTreeForComments($c_ids);

        // Nest comments and return the nested arrays.
        $output['article']->comments = $comments;

        return $output;
    }

    public function findAll(): array
    {
        $stmt = $this->connection->prepare('
            SELECT * FROM article
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, ArticleDAO::class);

        // fetchAll() will do the same as above, but we'll have an array. ie:
        //    $comments = $repository->findAll();
        //    echo $comments[0]->content;
        return $stmt->fetchAll();
    }

    public function save(ArticleDAO $article): bool
    {
        // If the ID is set, we're updating an existing record
        if (isset($article->id)) {
            return $this->update($article);
        }
        $time = time();

        $stmt = $this->connection->prepare('
            INSERT INTO article 
                (created_at, content, user_id, title,) 
            VALUES 
                (:created_at, :content, :user_id, :title)
        ');
        $stmt->bindParam(':created_at', $time);
        $stmt->bindParam(':title', $article->title);
        $stmt->bindParam(':user_id', $article->user_id);
        $stmt->bindParam(':content', $article->content);
        return $stmt->execute();
    }

    public function update(ArticleDAO $comment): bool
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
        $stmt->bindParam(':content', $comment->getContent());
        $stmt->bindParam(':user_id', $comment->getAuthor()->id);
        $stmt->bindParam(':id', $comment->id);
        return $stmt->execute();
    }


    /**
     * Uses MySQL 8.0 'WITH RECURSIVE' support to get a comment tree efficiently, given the 'Adjacency List' table structure.
     * The Comment provided will be the root comment for the returned tree.
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
                SELECT id, content, user_id, parent_id, CAST(id as CHAR(500)) AS path
                FROM comment
                WHERE id = :id
                UNION ALL
                SELECT c.id, c.content, c.user_id, c.parent_id, CONCAT(t.path, \',\', c.id)
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

    public function getCommentTreeForComments(array $ids): array
    {
        $ids_in = implode(',', $ids);
        $stmt = $this->connection->prepare('
            WITH RECURSIVE tree AS
            (
                SELECT id, content, user_id, parent_id, CAST(id as CHAR(500)) AS path, 1 AS depth
                FROM comment
                WHERE id IN ('.$ids_in.')
                UNION ALL
                SELECT c.id, c.content, c.user_id, c.parent_id, CONCAT(t.path, \',\', c.id), t.depth+1
                FROM tree t, comment c
                WHERE c.parent_id = t.id
            )
            SELECT * FROM tree ORDER BY path
        ');

        $stmt->setFetchMode(PDO::FETCH_CLASS, CommentDAO::class);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRootCommentIdsForArticle(string $id): array
    {
        $stmt = $this->connection->prepare('
            SELECT 
                c.id
            FROM comment c
            INNER JOIN article_comments ac 
                ON c.id = ac.comment_id
                AND ac.article_id = :id
                AND c.parent_id = -1
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}