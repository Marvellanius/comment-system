-- This file is executed at the start of a unit test
-- This prepares the database with some objects

INSERT INTO `user` (`id`, `email`, `username`) VALUES
    (1, 'test@tester.nl', 'tester1'),
    (2, 'tester2@tester.com', 'tester2'),
    (3, 'dick@tester.nl', 'dicktest');

INSERT INTO `article` (`id`, `created_at`, `title`, `content`, `user_id`) VALUES
    (1, 1578147439, 'Test Article 1', 'I didn\'t really know what to type here to be fair!', 1),
    (2, 1578147439, 'Test Article 2', 'Some lorem ipsum probably would\'ve been more apt.', 2);

INSERT INTO `comment` (`id`, `created_at`, `content`, `user_id`, `parent_id`) VALUES
    (1, 1578147439, 'Great article!', 2, -1),
    (2, 1578147439, 'Why, thank you!', 1, 1),
    (3, 1578147439, 'I concur!', 3, 2),
    (4, 1578147439, 'Great article!', 1, -1),
    (5, 1578147439, 'Why, thank you!', 2, 4),
    (6, 1578147439, 'I concur!', 3, 5),
    (7, 1578147439, 'I second that notion!', 3, -1);

INSERT INTO `rating` (`id`, `value`) VALUES
    (1, -1),
    (2, 0),
    (3, 1),
    (4, 2),
    (5, 3);

INSERT INTO `article_comments` (`article_id`, `comment_id`) VALUES
    (1, 1),
    (1, 2),
    (1, 3),
    (2, 4),
    (2, 5),
    (2, 6),
    (2, 7);

INSERT INTO `comment_ratings` (`comment_id`, `user_id`, `rating_id`) VALUES
    (1, 1, 4),
    (1, 3, 5),
    (2, 2, 4),
    (3, 1, 1),
    (3, 2, 2),
    (7, 1, 1);

