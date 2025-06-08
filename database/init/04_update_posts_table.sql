-- Update posts table to use author_id
ALTER TABLE
	`posts`
ADD
	COLUMN `author_id` int(11) DEFAULT NULL
AFTER
	`content`;

-- Update existing posts to link with authors
UPDATE
	`posts`
SET
	`author_id` = 1
WHERE
	`author` = 'Admin';

UPDATE
	`posts`
SET
	`author_id` = 1
WHERE
	`author` = 'System';

-- Add indexes
ALTER TABLE
	`posts`
ADD
	KEY `idx_author_id` (`author_id`);

ALTER TABLE
	`posts`
ADD
	FULLTEXT KEY `ft_title_content` (`title`, `content`);

ALTER TABLE
	`authors`
ADD
	FULLTEXT KEY `ft_name_bio` (`name`, `bio`);
