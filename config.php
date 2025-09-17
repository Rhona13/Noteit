-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 10:03 AM
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
-- Database: `noteit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `color` varchar(7) DEFAULT '#FFC107',
  `is_favorite` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `content`, `color`, `is_favorite`, `is_archived`, `created_at`, `updated_at`) VALUES
(1, 1, 'Welcome Note', 'Welcome to NoteIt! This is your first note. You can edit, delete, or mark it as favorite.', '#FFC107', 0, 0, '2025-09-17 03:22:15', '2025-09-17 03:22:15'),
(2, 1, 'Getting Started', 'Here are some tips for using NoteIt:\n- Click \"Add Notes\" to create new notes\n- Use the search bar to find specific notes\n- Mark important notes as favorites\n- Archive old notes to keep your workspace clean', '#4CAF50', 0, 0, '2025-09-17 03:22:15', '2025-09-17 03:22:15'),
(3, 1, 'Features', 'NoteIt features:\n- Create and edit notes\n- Mark favorites\n- Archive notes\n- Search functionality\n- Responsive design', '#2196F3', 0, 0, '2025-09-17 03:22:15', '2025-09-17 03:22:15'),
(4, 1, 'Welcome Note', 'Welcome to NoteIt! This is your first note. You can edit, delete, or mark it as favorite.', '#FFC107', 0, 0, '2025-09-17 03:24:16', '2025-09-17 03:24:16'),
(5, 1, 'Getting Started', 'Here are some tips for using NoteIt:\n- Click \"Add Notes\" to create new notes\n- Use the search bar to find specific notes\n- Mark important notes as favorites\n- Archive old notes to keep your workspace clean', '#4CAF50', 0, 0, '2025-09-17 03:24:16', '2025-09-17 03:24:16'),
(6, 1, 'Features', 'NoteIt features:\n- Create and edit notes\n- Mark favorites\n- Archive notes\n- Search functionality\n- Responsive design', '#2196F3', 0, 0, '2025-09-17 03:24:16', '2025-09-17 03:24:16'),
(7, 3, 'test', 'test', '#4CAF50', 0, 0, '2025-09-17 03:25:11', '2025-09-17 03:25:11'),
(8, 3, 'asdasd', 'asdad', '#2196F3', 1, 1, '2025-09-17 03:25:33', '2025-09-17 03:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@noteit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-09-17 03:22:15', '2025-09-17 03:22:15'),
(3, 'jaynu', 'jaynujangad03@gmail.com', '$2y$10$cnJtXZInfcKMU4OhW/GPKup9GGB53nYnmVJKo/z.Hi7jyeEAi9Ojm', '2025-09-17 03:24:58', '2025-09-17 03:24:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
