<?php


namespace Commentsystem\Services;


use Commentsystem\DAL\ArticleRepository;
use Commentsystem\Factories\ArticleFactory;
use Commentsystem\Models\Article;

class ArticleService
{
    private $repository;
    private $factory;

    public function __construct()
    {
        $this->repository = new ArticleRepository();
        $this->factory = new ArticleFactory();
    }

    /**
     * Returns an article with all comments
     *
     * @param $id
     *
     * @return array
     */
    public function findWithComments($id): Article
    {
        // Fetch Article first
        $article = $this->repository->find($id);

        // Get all related comment_ids that are the root for their conversation (parent_id === -1)
        $comment_ids = $this->repository->getRootCommentIdsForArticle($id);

        // Flatten the comment_ids array so that it can be imploded by the next function
        $c_ids = [];
        foreach($comment_ids as $c_id) {
            $c_ids[] = $c_id['id'];
        }

        // Get comment trees for the root comments
        $comment_service = new CommentService();
        $comments = $comment_service->getCommentTreeForComments($c_ids);

        $user_service = new UserService();
        $user = $user_service->findById($article->user_id);

        $output_article = $this->factory->createFromDAO($article);
        $output_article->setComments($comments);
        $output_article->setAuthor($user);

        return $output_article;
    }
}