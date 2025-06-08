-- Create authors table
CREATE TABLE IF NOT EXISTS `authors` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`email` varchar(255) UNIQUE NOT NULL,
	`bio` text,
	`avatar` varchar(255),
	`website` varchar(255),
	`social_media` json,
	`status` enum('active', 'inactive') DEFAULT 'active',
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_email` (`email`),
	KEY `idx_status` (`status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- Insert sample authors
INSERT INTO
	`authors` (
		`name`,
		`email`,
		`bio`,
		`website`,
		`social_media`,
		`status`,
		`created_at`,
		`updated_at`
	)
VALUES
	(
		'Admin User',
		'admin@example.com',
		'System administrator and content creator. Passionate about web development and technology.',
		'https://example.com',
		JSON_OBJECT('twitter', '@admin', 'github', 'admin'),
		'active',
		NOW(),
		NOW()
	),
	(
		'John Doe',
		'john@example.com',
		'Senior developer with expertise in PHP, JavaScript, and database design.',
		'https://johndoe.dev',
		JSON_OBJECT('twitter', '@johndoe', 'linkedin', 'johndoe'),
		'active',
		NOW(),
		NOW()
	),
	(
		'Jane Smith',
		'jane@example.com',
		'Technical writer and UI/UX designer. Loves creating user-friendly interfaces.',
		NULL,
		JSON_OBJECT('twitter', '@janesmith', 'dribbble', 'janesmith'),
		'active',
		NOW(),
		NOW()
	);
