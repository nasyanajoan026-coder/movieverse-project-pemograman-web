-- ================================================
-- MOVIEVERSE - Database Schema
-- ================================================

CREATE DATABASE IF NOT EXISTS movieverse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE movieverse;

-- ================================================
-- TABLES
-- ================================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('member','admin') DEFAULT 'member',
    bio TEXT DEFAULT NULL,
    avatar_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    year YEAR NOT NULL,
    director VARCHAR(100) DEFAULT NULL,
    duration INT DEFAULT NULL COMMENT 'in minutes',
    synopsis TEXT DEFAULT NULL,
    poster_url VARCHAR(500) DEFAULT NULL,
    trailer_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE movie_genres (
    movie_id INT NOT NULL,
    genre_id INT NOT NULL,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating DECIMAL(3,1) NOT NULL,
    review_text TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    CONSTRAINT chk_rating CHECK (rating >= 1 AND rating <= 10)
) ENGINE=InnoDB;

CREATE TABLE favorites (
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- SEED DATA
-- ================================================

-- Admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@movieverse.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Regular users (password: password123)
INSERT INTO users (username, email, password, role, bio) VALUES
('cinephile99', 'cine@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Passionate film lover. I watch everything from arthouse to blockbusters.'),
('filmgeek', 'geek@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Sci-fi and thriller enthusiast. Horror fan on weekends.'),
('movienerd', 'nerd@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Watched 500+ films and counting.'),
('reelcritic', 'critic@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Film critic and writer. Specializing in drama and world cinema.');

-- Genres
INSERT INTO genres (name, slug) VALUES
('Action', 'action'),
('Comedy', 'comedy'),
('Drama', 'drama'),
('Horror', 'horror'),
('Sci-Fi', 'sci-fi'),
('Thriller', 'thriller'),
('Romance', 'romance'),
('Animation', 'animation'),
('Documentary', 'documentary'),
('Fantasy', 'fantasy'),
('Crime', 'crime'),
('Adventure', 'adventure');

-- Movies (using TMDB poster paths as placeholders — replace with real URLs)
INSERT INTO movies (title, year, director, duration, synopsis, poster_url, trailer_url) VALUES
('Inception', 2010, 'Christopher Nolan', 148,
 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
 'https://image.tmdb.org/t/p/w500/edv5CZvWj09upOsy2Y6IwDhK8bt.jpg',
 'https://www.youtube.com/watch?v=YoHD9XEInc0'),

('Parasite', 2019, 'Bong Joon-ho', 132,
 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.',
 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg',
 'https://www.youtube.com/watch?v=5xH0HfJHsaY'),

('Interstellar', 2014, 'Christopher Nolan', 169,
 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity''s survival.',
 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
 'https://www.youtube.com/watch?v=zSWdZVtXT7E'),

('The Dark Knight', 2008, 'Christopher Nolan', 152,
 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
 'https://www.youtube.com/watch?v=EXeTwQWrcwY'),

('Dune', 2021, 'Denis Villeneuve', 155,
 'A noble family becomes embroiled in a war for control over the galaxy''s most valuable asset while its heir becomes troubled by visions of a dark future.',
 'https://image.tmdb.org/t/p/w500/d5NXSklpcvkCgnJQ3cYMJCubWMI.jpg',
 'https://www.youtube.com/watch?v=8g18jFHCLXk'),

('Everything Everywhere All at Once', 2022, 'The Daniels', 139,
 'An aging Chinese immigrant is swept up in an insane adventure, where she alone can save the world by exploring other universes connecting with the lives she could have led.',
 'https://image.tmdb.org/t/p/w500/w3LxiVYdWWRvEVdn5RYq6jIqkb1.jpg',
 'https://www.youtube.com/watch?v=wxN1T1uxQ2g'),

('Oppenheimer', 2023, 'Christopher Nolan', 180,
 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb during World War II.',
 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
 'https://www.youtube.com/watch?v=uYPbbksJxIg'),

('Whiplash', 2014, 'Damien Chazelle', 107,
 'A promising young drummer enrolls at a cut-throat music conservatory where his dreams of greatness are mentored by an instructor who will stop at nothing to realize a student''s potential.',
 'https://image.tmdb.org/t/p/w500/7fn624j5lj3xTme2SgiLCeuedmO.jpg',
 'https://www.youtube.com/watch?v=7d_jQycdQGo'),

('Spirited Away', 2001, 'Hayao Miyazaki', 125,
 'During her family''s move to the suburbs, a sullen 10-year-old girl wanders into a world ruled by gods, witches, and spirits, and where humans are changed into beasts.',
 'https://image.tmdb.org/t/p/w500/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg',
 'https://www.youtube.com/watch?v=ByXuk9QqQkk'),

('Get Out', 2017, 'Jordan Peele', 104,
 'A young African-American visits his white girlfriend''s parents for the weekend, where his simmering uneasiness about their reception of him eventually reaches a boiling point.',
 'https://image.tmdb.org/t/p/w500/tFXcEccSQMf3lfhfXKSU9iRBpa3.jpg',
 'https://www.youtube.com/watch?v=DzfpyUB60YY'),

('The Grand Budapest Hotel', 2014, 'Wes Anderson', 99,
 'A writer encounters the owner of an aging European hotel between the wars and learns of his early years serving as a lobby boy in the hotel''s glorious years under an exceptional concierge.',
 'https://image.tmdb.org/t/p/w500/eWdyYQreja6JGCzqHWXpWHDrrPo.jpg',
 'https://www.youtube.com/watch?v=1Fg5iWmQjwk'),

('Mad Max: Fury Road', 2015, 'George Miller', 120,
 'In a post-apocalyptic wasteland, a woman rebels against a tyrannical ruler in search for her homeland with the aid of a group of female prisoners, a psychotic worshiper, and a drifter named Max.',
 'https://image.tmdb.org/t/p/w500/hA2ple9q4qnwxp3hKVNhroipsir.jpg',
 'https://www.youtube.com/watch?v=hEJnMQG9ev8');

-- Assign genres to movies
-- Inception (5=Sci-Fi, 6=Thriller, 12=Adventure)
INSERT INTO movie_genres VALUES (1,5),(1,6),(1,12);
-- Parasite (3=Drama, 11=Crime, 6=Thriller)
INSERT INTO movie_genres VALUES (2,3),(2,11),(2,6);
-- Interstellar (5=Sci-Fi, 3=Drama, 12=Adventure)
INSERT INTO movie_genres VALUES (3,5),(3,3),(3,12);
-- The Dark Knight (1=Action, 11=Crime, 3=Drama)
INSERT INTO movie_genres VALUES (4,1),(4,11),(4,3);
-- Dune (5=Sci-Fi, 12=Adventure, 10=Fantasy)
INSERT INTO movie_genres VALUES (5,5),(5,12),(5,10);
-- EEAAO (1=Action, 2=Comedy, 5=Sci-Fi)
INSERT INTO movie_genres VALUES (6,1),(6,2),(6,5);
-- Oppenheimer (3=Drama, 6=Thriller)
INSERT INTO movie_genres VALUES (7,3),(7,6);
-- Whiplash (3=Drama)
INSERT INTO movie_genres VALUES (8,3);
-- Spirited Away (8=Animation, 10=Fantasy, 12=Adventure)
INSERT INTO movie_genres VALUES (9,8),(9,10),(9,12);
-- Get Out (4=Horror, 6=Thriller)
INSERT INTO movie_genres VALUES (10,4),(10,6);
-- The Grand Budapest Hotel (2=Comedy, 3=Drama)
INSERT INTO movie_genres VALUES (11,2),(11,3);
-- Mad Max (1=Action, 5=Sci-Fi, 12=Adventure)
INSERT INTO movie_genres VALUES (12,1),(12,5),(12,12);

-- Sample Reviews
INSERT INTO reviews (user_id, movie_id, rating, review_text, created_at) VALUES
-- Inception reviews
(2, 1, 9.5, 'A mind-bending masterpiece. Nolan at his best. The concept of dreams within dreams is executed flawlessly. The ending is still debated to this day!', '2024-01-10 10:30:00'),
(3, 1, 8.5, 'Visually stunning and intellectually stimulating. The rotating hallway fight scene is one of cinema''s greatest achievements. Watch it multiple times.', '2024-01-15 14:20:00'),
(4, 1, 9.0, 'Inception is a film that rewards repeat viewings. The score by Hans Zimmer is absolutely iconic. TOP is still falling.', '2024-02-01 09:15:00'),

-- Parasite reviews
(2, 2, 10.0, 'A perfect film. Bong Joon-ho crafted something truly extraordinary. The class commentary is sharp as a knife, wrapped in a thriller that had me gasping.', '2024-01-20 11:00:00'),
(3, 2, 9.5, 'Deserved every Oscar it won and more. The way the story escalates from a darkly funny social satire to a visceral thriller is unmatched. Absolute cinema.', '2024-02-05 16:30:00'),
(5, 2, 9.0, 'Still thinking about this film weeks later. The architecture of the story mirrors the social architecture being critiqued. Brilliant on every level.', '2024-02-10 08:45:00'),

-- Interstellar reviews
(2, 3, 9.0, 'Emotionally devastating and visually spectacular. The docking scene had me in tears. Hans Zimmer''s organ score is otherworldly. The love scene monologue is genuinely moving.', '2024-01-25 12:00:00'),
(4, 3, 8.0, 'Ambitious and beautiful. Some of the science gets hand-wavy near the end but the emotional core is undeniable. Cooper''s goodbye to Murphy destroyed me.', '2024-02-15 17:00:00'),

-- The Dark Knight reviews
(3, 4, 10.0, 'The Joker redefined what a comic book villain can be. Heath Ledger''s performance is transcendent. This is not just the best superhero film — it''s one of the best crime films ever made.', '2024-01-12 13:00:00'),
(5, 4, 9.5, 'Every single scene crackles with tension. The interrogation scene alone is worth the entire runtime. Why so serious? Because this film takes its premise seriously.', '2024-02-20 10:30:00'),

-- Dune reviews
(2, 5, 8.5, 'Villeneuve is a visual poet. This adaptation finally does Herbert''s novel justice. The scale is immense, the sound design is transcendent. Part 2 cannot come fast enough.', '2024-03-01 09:00:00'),
(4, 5, 9.0, 'Dune is the sci-fi epic we deserved. The world-building is meticulous without being overwhelming. Timothée Chalamet carries the film with quiet intensity.', '2024-03-05 14:30:00'),

-- Oppenheimer reviews
(3, 7, 9.5, 'Three hours that feel like three seconds. Nolan''s most mature and devastating film. Cillian Murphy is extraordinary. The Trinity test sequence is the most terrifying thing I''ve seen.', '2024-03-10 11:00:00'),
(5, 7, 8.5, 'A film that makes you feel the weight of history. The non-linear storytelling is masterful. Robert Downey Jr. delivers a career-best performance.', '2024-03-15 16:00:00'),

-- Whiplash reviews
(2, 8, 9.5, 'J.K. Simmons should get 10 Oscars for this role. This film is about obsession, abuse, and the terrible price of greatness. The final performance is cinema magic.', '2024-03-20 09:30:00'),
(4, 8, 10.0, 'Chazelle made one of the most viscerally intense films in decades on a shoestring budget. The editing is extraordinary. RUSHING OR DRAGGING?!', '2024-03-25 12:00:00'),

-- Spirited Away reviews
(3, 9, 10.0, 'Miyazaki''s masterwork. A film that works on every level — as a child''s fairy tale, as a meditation on identity and work, as a critique of consumerism. Timeless and perfect.', '2024-04-01 10:00:00'),
(5, 9, 9.5, 'Visually unparalleled. Studio Ghibli films are handcrafted art and this is their crown jewel. Will make you feel like a child again.', '2024-04-05 14:00:00'),

-- Get Out reviews
(2, 10, 9.0, 'Jordan Peele is a genius. Get Out works as a horror film, a social commentary, and a thriller all at once. The \"sunken place\" is one of the most terrifying concepts in modern horror.', '2024-04-10 11:00:00'),
(3, 10, 8.5, 'Smart, sharp, and genuinely scary. This film uses genre to say something important. Daniel Kaluuya''s eyes in this film deserve their own Oscar.', '2024-04-15 15:00:00');

-- Favorites
INSERT INTO favorites (user_id, movie_id) VALUES
(2, 1), (2, 2), (2, 3), (2, 9),
(3, 2), (3, 4), (3, 7), (3, 8),
(4, 1), (4, 3), (4, 5), (4, 8),
(5, 2), (5, 4), (5, 9), (5, 10);

-- ================================================
-- USEFUL VIEWS
-- ================================================

CREATE OR REPLACE VIEW movie_stats AS
SELECT 
    m.id,
    m.title,
    m.year,
    m.poster_url,
    COUNT(DISTINCT r.id) AS review_count,
    COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating,
    COUNT(DISTINCT f.user_id) AS favorite_count,
    GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS genres,
    m.created_at
FROM movies m
LEFT JOIN reviews r ON m.id = r.movie_id
LEFT JOIN favorites f ON m.id = f.movie_id
LEFT JOIN movie_genres mg ON m.id = mg.movie_id
LEFT JOIN genres g ON mg.genre_id = g.id
GROUP BY m.id;
