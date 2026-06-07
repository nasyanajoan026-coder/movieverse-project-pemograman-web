-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 12:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `movieverse`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`user_id`, `movie_id`, `created_at`) VALUES
(2, 1, '2026-06-04 06:58:05'),
(2, 2, '2026-06-04 06:58:05'),
(2, 3, '2026-06-04 06:58:05'),
(2, 9, '2026-06-04 06:58:05'),
(3, 2, '2026-06-04 06:58:05'),
(3, 4, '2026-06-04 06:58:05'),
(3, 7, '2026-06-04 06:58:05'),
(3, 8, '2026-06-04 06:58:05'),
(4, 1, '2026-06-04 06:58:05'),
(4, 3, '2026-06-04 06:58:05'),
(4, 5, '2026-06-04 06:58:05'),
(4, 8, '2026-06-04 06:58:05'),
(5, 2, '2026-06-04 06:58:05'),
(5, 4, '2026-06-04 06:58:05'),
(5, 9, '2026-06-04 06:58:05'),
(5, 10, '2026-06-04 06:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Action', 'action', '2026-06-04 06:58:05'),
(2, 'Comedy', 'comedy', '2026-06-04 06:58:05'),
(3, 'Drama', 'drama', '2026-06-04 06:58:05'),
(4, 'Horror', 'horror', '2026-06-04 06:58:05'),
(5, 'Sci-Fi', 'sci-fi', '2026-06-04 06:58:05'),
(6, 'Thriller', 'thriller', '2026-06-04 06:58:05'),
(7, 'Romance', 'romance', '2026-06-04 06:58:05'),
(8, 'Animation', 'animation', '2026-06-04 06:58:05'),
(9, 'Documentary', 'documentary', '2026-06-04 06:58:05'),
(10, 'Fantasy', 'fantasy', '2026-06-04 06:58:05'),
(11, 'Crime', 'crime', '2026-06-04 06:58:05'),
(12, 'Adventure', 'adventure', '2026-06-04 06:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `year` year(4) NOT NULL,
  `director` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'in minutes',
  `synopsis` text DEFAULT NULL,
  `poster_url` varchar(500) DEFAULT NULL,
  `trailer_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `year`, `director`, `duration`, `synopsis`, `poster_url`, `trailer_url`, `created_at`, `updated_at`) VALUES
(1, 'Inception', '2010', 'Christopher Nolan', 148, 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', 'https://image.tmdb.org/t/p/w500/edv5CZvWj09upOsy2Y6IwDhK8bt.jpg', 'https://www.youtube.com/watch?v=YoHD9XEInc0', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(2, 'Parasite', '2019', 'Bong Joon-ho', 132, 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.', 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg', 'https://www.youtube.com/watch?v=5xH0HfJHsaY', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(3, 'Interstellar', '2014', 'Christopher Nolan', 169, 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.', 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 'https://www.youtube.com/watch?v=zSWdZVtXT7E', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(4, 'The Dark Knight', '2008', 'Christopher Nolan', 152, 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg', 'https://www.youtube.com/watch?v=EXeTwQWrcwY', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(5, 'Dune', '2021', 'Denis Villeneuve', 155, 'A noble family becomes embroiled in a war for control over the galaxy\'s most valuable asset while its heir becomes troubled by visions of a dark future.', 'https://image.tmdb.org/t/p/w500/d5NXSklpcvkCgnJQ3cYMJCubWMI.jpg', 'https://www.youtube.com/watch?v=8g18jFHCLXk', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(6, 'Everything Everywhere All at Once', '2022', 'The Daniels', 139, 'An aging Chinese immigrant is swept up in an insane adventure, where she alone can save the world by exploring other universes connecting with the lives she could have led.', 'https://image.tmdb.org/t/p/w500/w3LxiVYdWWRvEVdn5RYq6jIqkb1.jpg', 'https://www.youtube.com/watch?v=wxN1T1uxQ2g', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(7, 'Oppenheimer', '2023', 'Christopher Nolan', 180, 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb during World War II.', 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg', 'https://www.youtube.com/watch?v=uYPbbksJxIg', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(8, 'Whiplash', '2014', 'Damien Chazelle', 107, 'A promising young drummer enrolls at a cut-throat music conservatory where his dreams of greatness are mentored by an instructor who will stop at nothing to realize a student\'s potential.', 'https://image.tmdb.org/t/p/w500/7fn624j5lj3xTme2SgiLCeuedmO.jpg', 'https://www.youtube.com/watch?v=7d_jQycdQGo', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(9, 'Spirited Away', '2001', 'Hayao Miyazaki', 125, 'During her family\'s move to the suburbs, a sullen 10-year-old girl wanders into a world ruled by gods, witches, and spirits, and where humans are changed into beasts.', 'https://image.tmdb.org/t/p/w500/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg', 'https://www.youtube.com/watch?v=ByXuk9QqQkk', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(10, 'Get Out', '2017', 'Jordan Peele', 104, 'A young African-American visits his white girlfriend\'s parents for the weekend, where his simmering uneasiness about their reception of him eventually reaches a boiling point.', 'https://image.tmdb.org/t/p/w500/tFXcEccSQMf3lfhfXKSU9iRBpa3.jpg', 'https://www.youtube.com/watch?v=DzfpyUB60YY', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(11, 'The Grand Budapest Hotel', '2014', 'Wes Anderson', 99, 'A writer encounters the owner of an aging European hotel between the wars and learns of his early years serving as a lobby boy in the hotel\'s glorious years under an exceptional concierge.', 'https://image.tmdb.org/t/p/w500/eWdyYQreja6JGCzqHWXpWHDrrPo.jpg', 'https://www.youtube.com/watch?v=1Fg5iWmQjwk', '2026-06-04 06:58:05', '2026-06-04 06:58:05'),
(12, 'Mad Max: Fury Road', '2015', 'George Miller', 120, 'In a post-apocalyptic wasteland, a woman rebels against a tyrannical ruler in search for her homeland with the aid of a group of female prisoners, a psychotic worshiper, and a drifter named Max.', 'https://image.tmdb.org/t/p/w500/hA2ple9q4qnwxp3hKVNhroipsir.jpg', 'https://www.youtube.com/watch?v=hEJnMQG9ev8', '2026-06-04 06:58:05', '2026-06-04 06:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `movie_genres`
--

CREATE TABLE `movie_genres` (
  `movie_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_genres`
--

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
(1, 5),
(1, 6),
(1, 12),
(2, 3),
(2, 6),
(2, 11),
(3, 3),
(3, 5),
(3, 12),
(4, 1),
(4, 3),
(4, 11),
(5, 5),
(5, 10),
(5, 12),
(6, 1),
(6, 2),
(6, 5),
(7, 3),
(7, 6),
(8, 3),
(9, 8),
(9, 10),
(9, 12),
(10, 4),
(10, 6),
(11, 2),
(11, 3),
(12, 1),
(12, 5),
(12, 12);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `movie_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 9.5, 'A mind-bending masterpiece. Nolan at his best. The concept of dreams within dreams is executed flawlessly. The ending is still debated to this day!', '2024-01-10 02:30:00', '2026-06-04 06:58:05'),
(2, 3, 1, 8.5, 'Visually stunning and intellectually stimulating. The rotating hallway fight scene is one of cinema\'s greatest achievements. Watch it multiple times.', '2024-01-15 06:20:00', '2026-06-04 06:58:05'),
(3, 4, 1, 9.0, 'Inception is a film that rewards repeat viewings. The score by Hans Zimmer is absolutely iconic. TOP is still falling.', '2024-02-01 01:15:00', '2026-06-04 06:58:05'),
(4, 2, 2, 10.0, 'A perfect film. Bong Joon-ho crafted something truly extraordinary. The class commentary is sharp as a knife, wrapped in a thriller that had me gasping.', '2024-01-20 03:00:00', '2026-06-04 06:58:05'),
(5, 3, 2, 9.5, 'Deserved every Oscar it won and more. The way the story escalates from a darkly funny social satire to a visceral thriller is unmatched. Absolute cinema.', '2024-02-05 08:30:00', '2026-06-04 06:58:05'),
(6, 5, 2, 9.0, 'Still thinking about this film weeks later. The architecture of the story mirrors the social architecture being critiqued. Brilliant on every level.', '2024-02-10 00:45:00', '2026-06-04 06:58:05'),
(7, 2, 3, 9.0, 'Emotionally devastating and visually spectacular. The docking scene had me in tears. Hans Zimmer\'s organ score is otherworldly. The love scene monologue is genuinely moving.', '2024-01-25 04:00:00', '2026-06-04 06:58:05'),
(8, 4, 3, 8.0, 'Ambitious and beautiful. Some of the science gets hand-wavy near the end but the emotional core is undeniable. Cooper\'s goodbye to Murphy destroyed me.', '2024-02-15 09:00:00', '2026-06-04 06:58:05'),
(9, 3, 4, 10.0, 'The Joker redefined what a comic book villain can be. Heath Ledger\'s performance is transcendent. This is not just the best superhero film — it\'s one of the best crime films ever made.', '2024-01-12 05:00:00', '2026-06-04 06:58:05'),
(10, 5, 4, 9.5, 'Every single scene crackles with tension. The interrogation scene alone is worth the entire runtime. Why so serious? Because this film takes its premise seriously.', '2024-02-20 02:30:00', '2026-06-04 06:58:05'),
(11, 2, 5, 8.5, 'Villeneuve is a visual poet. This adaptation finally does Herbert\'s novel justice. The scale is immense, the sound design is transcendent. Part 2 cannot come fast enough.', '2024-03-01 01:00:00', '2026-06-04 06:58:05'),
(12, 4, 5, 9.0, 'Dune is the sci-fi epic we deserved. The world-building is meticulous without being overwhelming. Timothée Chalamet carries the film with quiet intensity.', '2024-03-05 06:30:00', '2026-06-04 06:58:05'),
(13, 3, 7, 9.5, 'Three hours that feel like three seconds. Nolan\'s most mature and devastating film. Cillian Murphy is extraordinary. The Trinity test sequence is the most terrifying thing I\'ve seen.', '2024-03-10 03:00:00', '2026-06-04 06:58:05'),
(14, 5, 7, 8.5, 'A film that makes you feel the weight of history. The non-linear storytelling is masterful. Robert Downey Jr. delivers a career-best performance.', '2024-03-15 08:00:00', '2026-06-04 06:58:05'),
(15, 2, 8, 9.5, 'J.K. Simmons should get 10 Oscars for this role. This film is about obsession, abuse, and the terrible price of greatness. The final performance is cinema magic.', '2024-03-20 01:30:00', '2026-06-04 06:58:05'),
(16, 4, 8, 10.0, 'Chazelle made one of the most viscerally intense films in decades on a shoestring budget. The editing is extraordinary. RUSHING OR DRAGGING?!', '2024-03-25 04:00:00', '2026-06-04 06:58:05'),
(17, 3, 9, 10.0, 'Miyazaki\'s masterwork. A film that works on every level — as a child\'s fairy tale, as a meditation on identity and work, as a critique of consumerism. Timeless and perfect.', '2024-04-01 02:00:00', '2026-06-04 06:58:05'),
(18, 5, 9, 9.5, 'Visually unparalleled. Studio Ghibli films are handcrafted art and this is their crown jewel. Will make you feel like a child again.', '2024-04-05 06:00:00', '2026-06-04 06:58:05'),
(19, 2, 10, 9.0, 'Jordan Peele is a genius. Get Out works as a horror film, a social commentary, and a thriller all at once. The \"sunken place\" is one of the most terrifying concepts in modern horror.', '2024-04-10 03:00:00', '2026-06-04 06:58:05'),
(20, 3, 10, 8.5, 'Smart, sharp, and genuinely scary. This film uses genre to say something important. Daniel Kaluuya\'s eyes in this film deserve their own Oscar.', '2024-04-15 07:00:00', '2026-06-04 06:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('member','admin') DEFAULT 'member',
  `bio` text DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `bio`, `avatar_url`, `created_at`) VALUES
(1, 'admin', 'admin@movieverse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '2026-06-04 06:58:05'),
(2, 'cinephile99', 'cine@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Passionate film lover. I watch everything from arthouse to blockbusters.', NULL, '2026-06-04 06:58:05'),
(3, 'filmgeek', 'geek@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Sci-fi and thriller enthusiast. Horror fan on weekends.', NULL, '2026-06-04 06:58:05'),
(4, 'movienerd', 'nerd@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Watched 500+ films and counting.', NULL, '2026-06-04 06:58:05'),
(5, 'reelcritic', 'critic@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'Film critic and writer. Specializing in drama and world cinema.', NULL, '2026-06-04 06:58:05');



--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`movie_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD CONSTRAINT `movie_genres_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
