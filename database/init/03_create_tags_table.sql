-- Create tags table
CREATE TABLE IF NOT EXISTS `tags` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,
	`slug` varchar(100) UNIQUE NOT NULL,
	`description` text,
	`color` varchar(7) DEFAULT '#007bff',
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `uk_slug` (`slug`),
	KEY `idx_name` (`name`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Create post_tags pivot table for many-to-many relationship
CREATE TABLE IF NOT EXISTS `post_tags` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`post_id` int(11) NOT NULL,
	`tag_id` int(11) NOT NULL,
	`created_at` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `uk_post_tag` (`post_id`, `tag_id`),
	KEY `idx_post_id` (`post_id`),
	KEY `idx_tag_id` (`tag_id`),
	CONSTRAINT `fk_post_tags_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
	CONSTRAINT `fk_post_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Insert sample tags
INSERT INTO
	`tags` (
		`name`,
		`slug`,
		`description`,
		`color`,
		`created_at`,
		`updated_at`
	)
VALUES
	(
		'CodeIgniter',
		'codeigniter',
		'Posts related to CodeIgniter framework',
		'#FF5722',
		NOW(),
		NOW()
	),
	(
		'PHP',
		'php',
		'PHP programming language posts',
		'#777BB4',
		NOW(),
		NOW()
	),
	(
		'MySQL',
		'mysql',
		'Database and MySQL related content',
		'#4479A1',
		NOW(),
		NOW()
	),
	(
		'Docker',
		'docker',
		'Containerization and Docker tutorials',
		'#2496ED',
		NOW(),
		NOW()
	),
	(
		'Tutorial',
		'tutorial',
		'Step-by-step guides and tutorials',
		'#28A745',
		NOW(),
		NOW()
	),
	(
		'Getting Started',
		'getting-started',
		'Beginner-friendly content',
		'#17A2B8',
		NOW(),
		NOW()
	);

-- Add sample tag relationships
INSERT INTO
	`post_tags` (`post_id`, `tag_id`, `created_at`)
VALUES
	(1, 1, NOW()),
	-- Welcome to CodeIgniter -> CodeIgniter
	(1, 6, NOW()),
	-- Welcome to CodeIgniter -> Getting Started
	(2, 1, NOW()),
	-- Getting Started with Posts -> CodeIgniter
	(2, 5, NOW()),
	-- Getting Started with Posts -> Tutorial
	(2, 6, NOW()),
	-- Getting Started with Posts -> Getting Started
	(3, 4, NOW()),
	-- Docker Setup Complete -> Docker
	(3, 2, NOW()),
	-- Docker Setup Complete -> PHP
	(3, 3, NOW());

-- Docker Setup Complete -> MySQL
