CREATE DATABASE IF NOT EXISTS campus_lost_found
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE campus_lost_found;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_statuses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  category_id INT NOT NULL,
  status_id INT NOT NULL,
  item_type ENUM('lost', 'found') NOT NULL,
  item_name VARCHAR(160) NOT NULL,
  color VARCHAR(80) DEFAULT NULL,
  shape VARCHAR(80) DEFAULT NULL,
  item_size VARCHAR(80) DEFAULT NULL,
  estimated_weight VARCHAR(80) DEFAULT NULL,
  location VARCHAR(180) NOT NULL,
  date_reported DATE NOT NULL,
  description TEXT NOT NULL,
  image_path VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(id),
  CONSTRAINT fk_items_status FOREIGN KEY (status_id) REFERENCES post_statuses(id),
  INDEX idx_items_type (item_type),
  INDEX idx_items_date (date_reported),
  INDEX idx_items_location (location),
  FULLTEXT INDEX idx_items_text (item_name, description, location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  sender_id INT NOT NULL,
  poster_id INT NOT NULL,
  sender_email VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_messages_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_poster FOREIGN KEY (poster_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO categories (name) VALUES
('Books'),
('Bags'),
('Wallets'),
('Money'),
('Student Cards'),
('Keys'),
('Electronics'),
('Documents'),
('Clothing'),
('Personal Accessories'),
('Other');

INSERT IGNORE INTO post_statuses (name) VALUES
('Pending'),
('Approved'),
('Rejected');

INSERT INTO users (name, email, password_hash, role)
SELECT 'System Administrator', 'admin@ukm.edu.my', '$2y$10$uwm01R6VVZyD43eDmATpF.wBZowZplE3MiwyrwUMRUANzJdp.KLdC', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@ukm.edu.my');

INSERT INTO users (name, email, password_hash, role)
SELECT 'Demo Student', 'student@siswa.ukm.edu.my', '$2y$10$09MN/kf5vM068oRi9ajLaeM9cPf2ojb3e86mFmpAcFUK6bml6IEIG', 'user'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'student@siswa.ukm.edu.my');
