-- Create posts table
CREATE TABLE IF NOT EXISTS `posts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	`content` text NOT NULL,
	`author` varchar(100) DEFAULT 'Anonymous',
	`status` enum('published', 'draft', 'archived') DEFAULT 'published',
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_status` (`status`),
	KEY `idx_created_at` (`created_at`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Insert sample data
INSERT INTO
	`posts` (
		`title`,
		`content`,
		`author`,
		`status`,
		`created_at`,
		`updated_at`
	)
VALUES
	(
		'Welcome to CodeIgniter',
		'This is a sample post to test our CodeIgniter setup with Docker and MySQL.',
		'Admin',
		'published',
		NOW(),
		NOW()
	),
	(
		'Getting Started with Posts',
		'Learn how to create, edit and manage posts in this simple blog system.',
		'Admin',
		'published',
		NOW(),
		NOW()
	),
	(
		'Docker Setup Complete',
		'Your CodeIgniter application is now running with Docker, MySQL 5.7 and PHP 7.4.',
		'System',
		'published',
		NOW(),
		NOW()
	);
