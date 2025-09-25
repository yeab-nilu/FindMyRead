-- Seed data for FindMyRead
USE book_recommendation_db;

-- Insert sample genres
INSERT INTO genres (name, description, color) VALUES
('Fiction', 'Imaginative literature including novels, short stories, and novellas', '#8b5cf6'),
('Non-Fiction', 'Informative and factual literature', '#06b6d4'),
('Mystery', 'Stories involving crime, detective work, and suspense', '#ef4444'),
('Romance', 'Stories focusing on love relationships', '#ec4899'),
('Science Fiction', 'Speculative fiction dealing with futuristic concepts', '#3b82f6'),
('Fantasy', 'Fiction involving magical or supernatural elements', '#f59e0b'),
('Thriller', 'Stories designed to create excitement and suspense', '#dc2626'),
('Historical Fiction', 'Fiction set in the past with historical elements', '#7c3aed'),
('Biography', 'Non-fiction accounts of people\'s lives', '#059669'),
('Self-Help', 'Books aimed at personal development and improvement', '#10b981'),
('Business', 'Books about business practices and entrepreneurship', '#6366f1'),
('Technology', 'Books about computers, programming, and digital innovation', '#8b5cf6'),
('Psychology', 'Books about human behavior and mental processes', '#f97316'),
('Philosophy', 'Books exploring fundamental questions about existence', '#6b7280'),
('Health & Fitness', 'Books about physical and mental well-being', '#22c55e');

-- Insert sample books
INSERT INTO books (isbn, title, author, publisher, publication_year, genre_id, description, cover_image_url, average_rating, total_reviews, page_count, language) VALUES
('978-1250301697', 'The Silent Patient', 'Alex Michaelides', 'Celadon Books', 2019, 3, 'A psychological thriller about a woman who refuses to speak after allegedly murdering her husband. The story follows a psychotherapist who becomes obsessed with uncovering the truth.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300&h=400&fit=crop', 4.2, 1250, 336, 'English'),
('978-1250301698', 'Verity', 'Colleen Hoover', 'Grand Central Publishing', 2018, 4, 'A dark romance thriller that will keep you guessing until the very end. A struggling writer takes a job to complete the remaining books of a successful author.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=400&fit=crop', 4.1, 980, 331, 'English'),
('978-1501132740', 'It Ends With Us', 'Colleen Hoover', 'Atria Books', 2016, 4, 'An emotional story about love, loss, and the strength to start over. A young woman finds herself in a complicated relationship that tests her strength.', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop', 4.3, 2100, 384, 'English'),
('978-0735211292', 'Atomic Habits', 'James Clear', 'Avery', 2018, 10, 'An easy and proven way to build good habits and break bad ones. A comprehensive guide to creating lasting change through small, incremental improvements.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=400&fit=crop', 4.6, 1850, 320, 'English'),
('978-1982137274', 'The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', 'Atria Books', 2017, 4, 'A captivating story about a reclusive Hollywood icon who finally decides to tell her life story to an unknown journalist.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300&h=400&fit=crop', 4.4, 1650, 400, 'English'),
('978-0525559477', 'The Midnight Library', 'Matt Haig', 'Viking', 2020, 1, 'A novel about the infinite possibilities of life and the choices we make. A woman gets the chance to live different versions of her life.', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop', 4.1, 1400, 304, 'English'),
('978-0593099322', 'Project Hail Mary', 'Andy Weir', 'Ballantine Books', 2021, 5, 'A science fiction adventure about humanity\'s last hope for survival. A lone astronaut must save Earth from an extinction-level event.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=400&fit=crop', 4.5, 1200, 496, 'English'),
('978-1250301699', 'The Alchemist', 'Paulo Coelho', 'HarperOne', 1988, 1, 'A philosophical novel about a young shepherd\'s journey to find his personal legend. A timeless tale of following your dreams.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300&h=400&fit=crop', 4.2, 2800, 163, 'English'),
('978-0062457714', 'The Subtle Art of Not Giving a F*ck', 'Mark Manson', 'HarperOne', 2016, 10, 'A counterintuitive approach to living a good life. A refreshing take on personal development and happiness.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=400&fit=crop', 4.0, 1950, 224, 'English'),
('978-0593099330', 'The Psychology of Money', 'Morgan Housel', 'Harriman House', 2020, 11, 'Timeless lessons on wealth, greed, and happiness. A collection of short stories exploring the strange ways people think about money.', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop', 4.4, 850, 256, 'English'),
('978-0143127741', 'The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 1925, 1, 'A classic American novel about the mysterious Jay Gatsby and his obsession with the beautiful Daisy Buchanan.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300&h=400&fit=crop', 4.3, 3200, 180, 'English'),
('978-0061120084', 'To Kill a Mockingbird', 'Harper Lee', 'J.B. Lippincott & Co.', 1960, 1, 'A gripping tale of racial injustice and childhood innocence in the American South.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=400&fit=crop', 4.5, 4500, 281, 'English'),
('978-0451524935', '1984', 'George Orwell', 'Signet Classic', 1949, 5, 'A dystopian social science fiction novel about totalitarian control and surveillance.', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop', 4.2, 2800, 328, 'English'),
('978-0141439518', 'Pride and Prejudice', 'Jane Austen', 'Penguin Classics', 1813, 4, 'A romantic novel about Elizabeth Bennet and Mr. Darcy, exploring themes of love, class, and social expectations.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300&h=400&fit=crop', 4.4, 3800, 432, 'English'),
('978-0544003415', 'The Hobbit', 'J.R.R. Tolkien', 'Houghton Mifflin Harcourt', 1937, 6, 'A fantasy novel about a hobbit who goes on an unexpected journey to help dwarves reclaim their homeland.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=400&fit=crop', 4.6, 4200, 310, 'English'),
('978-0547928227', 'The Lord of the Rings', 'J.R.R. Tolkien', 'Houghton Mifflin Harcourt', 1954, 6, 'An epic high-fantasy novel about the quest to destroy the One Ring and defeat the Dark Lord Sauron.', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop', 4.7, 5600, 1216, 'English'),
('978-0439708180', 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Scholastic', 1997, 6, 'The first book in the Harry Potter series, following a young wizard\'s journey at Hogwarts School of Witchcraft and Wizardry.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300&h=400&fit=crop', 4.8, 6800, 309, 'English'),
('978-0316769174', 'The Catcher in the Rye', 'J.D. Salinger', 'Little, Brown and Company', 1951, 1, 'A coming-of-age story about teenager Holden Caulfield and his experiences in New York City.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=400&fit=crop', 4.1, 2100, 277, 'English'),
('978-0142000670', 'Of Mice and Men', 'John Steinbeck', 'Penguin Books', 1937, 1, 'A tragic story about two displaced migrant ranch workers during the Great Depression.', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop', 4.2, 1800, 107, 'English'),
('978-0061122415', 'The Chronicles of Narnia', 'C.S. Lewis', 'HarperCollins', 1950, 6, 'A series of fantasy novels about children who discover the magical world of Narnia.', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=300&h=400&fit=crop', 4.5, 3400, 767, 'English');

-- Insert sample users
INSERT INTO users (username, email, password_hash, first_name, last_name, reading_preferences, bio) VALUES
('alice_reader', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice', 'Johnson', JSON_OBJECT('favorite_genres', JSON_ARRAY('Fiction','Mystery','Romance'), 'reading_goal', 24, 'preferred_language', 'English', 'favorite_authors', JSON_ARRAY('Colleen Hoover','Alex Michaelides')), 'Avid reader who loves psychological thrillers and romance novels. Always looking for my next great read!'),
('bob_bookworm', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Smith', JSON_OBJECT('favorite_genres', JSON_ARRAY('Non-Fiction','Business','Self-Help'), 'reading_goal', 12, 'preferred_language', 'English', 'favorite_authors', JSON_ARRAY('James Clear','Morgan Housel')), 'Business professional who enjoys self-improvement and productivity books.'),
('charlie_literature', 'charlie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Charlie', 'Brown', JSON_OBJECT('favorite_genres', JSON_ARRAY('Fiction','Fantasy','Science Fiction'), 'reading_goal', 36, 'preferred_language', 'English', 'favorite_authors', JSON_ARRAY('J.R.R. Tolkien','J.K. Rowling')), 'Fantasy and sci-fi enthusiast. Love exploring new worlds through books!'),
('diana_analyst', 'diana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diana', 'Wilson', JSON_OBJECT('favorite_genres', JSON_ARRAY('Psychology','Philosophy','Biography'), 'reading_goal', 18, 'preferred_language', 'English', 'favorite_authors', JSON_ARRAY('Carl Jung','Viktor Frankl')), 'Psychology student with a passion for understanding human behavior and consciousness.');

-- Insert reading analytics for users
INSERT INTO reading_analytics (user_id, books_read_count, favorite_genres, average_reading_time, monthly_reading_goal, current_month_read, yearly_reading_goal, current_year_read, reading_streak, last_reading_date) VALUES
(1, 15, JSON_ARRAY('Fiction','Mystery','Romance'), 45, 24, 3, 50, 15, 7, CURDATE()),
(2, 8, JSON_ARRAY('Business','Self-Help','Non-Fiction'), 30, 12, 2, 25, 8, 3, CURDATE() - INTERVAL 2 DAY),
(3, 22, JSON_ARRAY('Fantasy','Science Fiction','Fiction'), 60, 36, 5, 75, 22, 12, CURDATE()),
(4, 12, JSON_ARRAY('Psychology','Philosophy','Biography'), 40, 18, 2, 40, 12, 5, CURDATE() - INTERVAL 1 DAY);

-- Insert sample reviews
INSERT INTO reviews (user_id, book_id, rating, review_text, is_verified_purchase, helpful_votes) VALUES
(1, 1, 5, 'Absolutely mind-blowing! The twist at the end completely caught me off guard. Alex Michaelides is a master of psychological suspense.', TRUE, 23),
(1, 2, 4, 'Dark and gripping. Colleen Hoover knows how to write compelling characters. The ending was unexpected but satisfying.', TRUE, 18),
(1, 3, 5, 'This book broke my heart in the best way possible. Such a powerful story about love, loss, and strength.', TRUE, 31),
(2, 4, 5, 'Life-changing book! The habit framework is so practical and easy to implement. Already seeing results after 2 months.', TRUE, 45),
(2, 10, 4, 'Great insights on money psychology. Made me rethink my relationship with wealth and happiness.', TRUE, 12),
(3, 7, 5, 'Andy Weir does it again! The science is fascinating and the story is incredibly engaging. Couldn\'t put it down!', TRUE, 28),
(3, 15, 5, 'Tolkien\'s masterpiece. The world-building is incredible and the characters are unforgettable.', TRUE, 67),
(3, 16, 5, 'The greatest fantasy epic ever written. Every time I read it, I discover something new.', TRUE, 89),
(4, 1, 4, 'Interesting psychological thriller. The character development is excellent.', TRUE, 15),
(4, 4, 3, 'Good concepts but felt a bit repetitive at times. Still worth reading for the practical advice.', TRUE, 8);

-- Insert reading lists
INSERT INTO user_reading_lists (user_id, book_id, list_type) VALUES
(1, 1, 'favorites'),
(1, 2, 'favorites'),
(1, 3, 'read'),
(1, 4, 'want_to_read'),
(1, 5, 'currently_reading'),
(2, 4, 'favorites'),
(2, 10, 'read'),
(2, 9, 'want_to_read'),
(3, 15, 'favorites'),
(3, 16, 'favorites'),
(3, 17, 'read'),
(3, 7, 'currently_reading'),
(4, 1, 'read'),
(4, 4, 'read'),
(4, 11, 'want_to_read');

-- Insert book recommendations
INSERT INTO book_recommendations (user_id, recommended_book_id, recommendation_reason, algorithm_used, confidence_score, is_viewed) VALUES
(1, 6, 'Because you liked psychological thrillers and mystery novels', 'content_based', 0.87, FALSE),
(1, 8, 'Popular among readers who enjoyed romance and emotional stories', 'collaborative', 0.76, FALSE),
(2, 9, 'Based on your interest in self-help and personal development', 'content_based', 0.92, FALSE),
(2, 11, 'Business professionals like you also enjoyed this classic', 'collaborative', 0.68, FALSE),
(3, 12, 'Since you love fantasy, you might enjoy this classic American novel', 'content_based', 0.71, FALSE),
(3, 13, 'Fans of science fiction often appreciate this dystopian classic', 'content_based', 0.85, FALSE),
(4, 14, 'Based on your interest in psychology and human behavior', 'content_based', 0.78, FALSE),
(4, 15, 'Readers with similar preferences enjoyed this fantasy series', 'collaborative', 0.82, FALSE);

-- Insert user interactions
INSERT INTO user_interactions (user_id, book_id, interaction_type, metadata) VALUES
(1, 1, 'view', JSON_OBJECT('duration', 45, 'source', 'search')),
(1, 2, 'add_to_list', JSON_OBJECT('list_type', 'favorites')),
(1, 3, 'rate', JSON_OBJECT('rating', 5)),
(2, 4, 'view', JSON_OBJECT('duration', 30, 'source', 'recommendation')),
(2, 10, 'review', JSON_OBJECT('review_length', 150)),
(3, 7, 'view', JSON_OBJECT('duration', 60, 'source', 'browse')),
(3, 15, 'share', JSON_OBJECT('platform', 'social_media')),
(4, 1, 'search', JSON_OBJECT('query', 'psychological thriller'));

-- Insert reading challenges
INSERT INTO reading_challenges (name, description, challenge_type, target_value, start_date, end_date, is_active) VALUES
('2024 Reading Challenge', 'Read 50 books in 2024', 'books_count', 50, '2024-01-01', '2024-12-31', TRUE),
('Summer Reading Spree', 'Read 10 books during summer', 'books_count', 10, '2024-06-01', '2024-08-31', TRUE),
('Genre Explorer', 'Read books from 10 different genres', 'genres_count', 10, '2024-01-01', '2024-12-31', TRUE),
('Page Turner', 'Read 10,000 pages this year', 'pages_count', 10000, '2024-01-01', '2024-12-31', TRUE);

-- Insert user challenge participation
INSERT INTO user_challenge_participation (user_id, challenge_id, current_progress, completed_at) VALUES
(1, 1, 15, NULL),
(2, 1, 8, NULL),
(3, 1, 22, NULL),
(4, 1, 12, NULL),
(1, 2, 3, NULL),
(3, 2, 5, NULL),
(1, 3, 3, NULL),
(2, 3, 2, NULL),
(3, 3, 4, NULL),
(4, 3, 3, NULL);

-- Insert book clubs
INSERT INTO book_clubs (name, description, created_by, is_public, max_members, current_members) VALUES
('Thriller Lovers', 'A club for fans of psychological thrillers and mystery novels', 1, TRUE, 30, 12),
('Business Book Club', 'Monthly discussions on business and self-improvement books', 2, TRUE, 25, 8),
('Fantasy & Sci-Fi Readers', 'Exploring the worlds of fantasy and science fiction', 3, TRUE, 40, 18),
('Psychology Enthusiasts', 'Deep dives into psychology and philosophy books', 4, TRUE, 20, 6);

-- Insert book club members
INSERT INTO book_club_members (club_id, user_id, role) VALUES
(1, 1, 'admin'),
(1, 2, 'member'),
(1, 3, 'member'),
(2, 2, 'admin'),
(2, 1, 'member'),
(2, 4, 'member'),
(3, 3, 'admin'),
(3, 1, 'member'),
(3, 4, 'member'),
(4, 4, 'admin'),
(4, 1, 'member'),
(4, 2, 'member');
