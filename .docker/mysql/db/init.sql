CREATE DATABASE IF NOT EXISTS comment_system;

USE comment_system;

--
-- Table structure for table `comment`
-- Parent ID references the `comment` table itself. A `parent_id` of -1 means it's a root comment.
-- (ommitted NULL values where possible
-- A closure table might have been a more elegant way of solving nested tree structures,
-- but for this example I ommitted it
-- `created_at` will be saved as a UNIX timestamp, this gives -imo- the easiest to retrieve datetime format
-- Also timezone independent, which is a plus if you ask me!
--

CREATE TABLE IF NOT EXISTS `comment` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `created_at` int(11) NOT NULL,
    `content` text COLLATE utf8_unicode_ci NOT NULL,
    `user_id` int(11) NOT NULL,
    `parent_id` int(11) DEFAULT -1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `article`
-- `created_at` is a UNIX timestamp
--

CREATE TABLE IF NOT EXISTS `article` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `created_at` int(11) NOT NULL,
    `title` text COLLATE utf8_unicode_ci NOT NULL,
    `content` text COLLATE utf8_unicode_ci NOT NULL,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
     `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `rating`
-- Table to strictly define allowed ratings (enum-ish)
--

CREATE TABLE IF NOT EXISTS `rating` (
    `id` int(11) NOT NULL,
    `value` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `article_comments`
-- Pivot table to store related article and comment_ids
-- Possibly could have chosen to just save the root comments,
-- but for this project (and to lessen the amount of queries necessary), all comments will be store here
--

CREATE TABLE IF NOT EXISTS `article_comments` (
    `article_id` int(11) NOT NULL,
    `comment_id` int(11) NOT NULL,
    PRIMARY KEY (`article_id`, `comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Table structure for table `comment_ratings`
-- Pivot table to store all comment ratings
-- Won't allow multiple ratings of the same comment by the same user.
--

CREATE TABLE IF NOT EXISTS `comment_ratings` (
    `comment_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `rating_id` int(11) NOT NULL,
    PRIMARY KEY (`comment_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Set Foreign key constraints for tables
-- 2 different ways of naming foreign key constraints here:
-- First is using real-world language: the referenced `user_id` on a comment actually points to the author
-- so I named the FK as such.
-- Secondly, the verbose method of fk_<table>_<column>__<referenced_table>_<referenced_column>
-- Normally would keep to the project's conventions.
--

ALTER TABLE `comment`
ADD CONSTRAINT fk_comment_author
FOREIGN KEY (`user_id`) REFERENCES `user`(`id`);

ALTER TABLE `article`
ADD CONSTRAINT fk_article_author
FOREIGN KEY (`user_id`) REFERENCES `user`(`id`);

ALTER TABLE `article_comments`
ADD CONSTRAINT fk_article_comments_article_id__article_id
FOREIGN KEY (`article_id`) REFERENCES `article`(`id`);

ALTER TABLE `article_comments`
ADD CONSTRAINT fk_article_comments_comment_id__comment_id
FOREIGN KEY (`comment_id`) REFERENCES `comment`(`id`);

ALTER TABLE `comment_ratings`
ADD CONSTRAINT fk_comment_ratings__comment_id__comment_id
FOREIGN KEY (`comment_id`) REFERENCES `comment`(`id`);

ALTER TABLE `comment_ratings`
ADD CONSTRAINT fk_comment_ratings__user_id__user_id
FOREIGN KEY (`user_id`) REFERENCES `user`(`id`);

ALTER TABLE `comment_ratings`
ADD CONSTRAINT fk_comment_ratings__rating_id__rating_id
FOREIGN KEY (`rating_id`) REFERENCES `rating`(`id`);
