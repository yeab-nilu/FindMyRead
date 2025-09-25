-- FindMyRead Database Schema
-- Run this in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS book_recommendation_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE book_recommendation_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    avatar_url VARCHAR(500),
    reading_preferences JSON,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Genres table
CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#6366f1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) UNIQUE,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    publisher VARCHAR(100),
    publication_year INT,
    genre_id INT,
    description TEXT,
    cover_image_url VARCHAR(500),
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    page_count INT,
    language VARCHAR(10) DEFAULT 'English',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_author (author),
    INDEX idx_genre (genre_id),
    INDEX idx_rating (average_rating),
    INDEX idx_year (publication_year)
);

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    helpful_votes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id),
    INDEX idx_book_rating (book_id, rating),
    INDEX idx_user_reviews (user_id),
    INDEX idx_recent_reviews (created_at DESC)
);

-- User reading lists table
CREATE TABLE user_reading_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    list_type ENUM('want_to_read', 'currently_reading', 'read', 'favorites') NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book_list (user_id, book_id, list_type),
    INDEX idx_user_list (user_id, list_type),
    INDEX idx_book_lists (book_id)
);

-- Book recommendations table
CREATE TABLE book_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recommended_book_id INT NOT NULL,
    recommendation_reason TEXT,
    algorithm_used ENUM('collaborative', 'content_based', 'hybrid', 'popular', 'trending') NOT NULL,
    confidence_score DECIMAL(3,2) DEFAULT 0.00,
    is_viewed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recommended_book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user_recommendations (user_id, is_viewed),
    INDEX idx_algorithm (algorithm_used),
    INDEX idx_confidence (confidence_score)
);

-- User interactions table
CREATE TABLE user_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    interaction_type ENUM('view', 'search', 'add_to_list', 'share', 'rate', 'review') NOT NULL,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user_interactions (user_id, interaction_type),
    INDEX idx_book_interactions (book_id),
    INDEX idx_interaction_type (interaction_type),
    INDEX idx_recent_interactions (created_at DESC)
);

-- Reading analytics table
CREATE TABLE reading_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    books_read_count INT DEFAULT 0,
    favorite_genres JSON,
    average_reading_time INT DEFAULT 0,
    monthly_reading_goal INT DEFAULT 5,
    current_month_read INT DEFAULT 0,
    yearly_reading_goal INT DEFAULT 50,
    current_year_read INT DEFAULT 0,
    reading_streak INT DEFAULT 0,
    last_reading_date DATE,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_books_read (books_read_count),
    INDEX idx_reading_goal (monthly_reading_goal)
);

-- Book clubs table
CREATE TABLE book_clubs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    is_public BOOLEAN DEFAULT TRUE,
    max_members INT DEFAULT 50,
    current_members INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_public_clubs (is_public),
    INDEX idx_creator (created_by)
);

-- Book club members table
CREATE TABLE book_club_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    club_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('admin', 'moderator', 'member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES book_clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_club_user (club_id, user_id),
    INDEX idx_club_members (club_id),
    INDEX idx_user_clubs (user_id)
);

-- Reading challenges table
CREATE TABLE reading_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    challenge_type ENUM('books_count', 'pages_count', 'genres_count', 'time_based') NOT NULL,
    target_value INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_active_challenges (is_active),
    INDEX idx_challenge_dates (start_date, end_date)
);

-- User challenge participation table
CREATE TABLE user_challenge_participation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    current_progress INT DEFAULT 0,
    completed_at TIMESTAMP NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES reading_challenges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_challenge (user_id, challenge_id),
    INDEX idx_user_challenges (user_id),
    INDEX idx_challenge_participants (challenge_id)
);

-- Create additional indexes for better performance
CREATE INDEX idx_books_search ON books(title, author, description(100));
CREATE INDEX idx_reviews_recent ON reviews(created_at DESC);
CREATE INDEX idx_recommendations_recent ON book_recommendations(created_at DESC);
CREATE INDEX idx_interactions_recent ON user_interactions(created_at DESC);
CREATE INDEX idx_books_popular ON books(average_rating DESC, total_reviews DESC);
CREATE INDEX idx_books_recent ON books(created_at DESC);
