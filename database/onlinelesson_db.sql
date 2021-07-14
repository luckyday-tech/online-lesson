-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 21-07-14 12:57
-- 서버 버전: 10.4.14-MariaDB
-- PHP 버전: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `onlinelesson_db`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `online_users`
--

CREATE TABLE `online_users` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `peer_id` varchar(128) NOT NULL,
  `is_teacher` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_title` varchar(128) NOT NULL,
  `host_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 테이블의 덤프 데이터 `rooms`
--

INSERT INTO `rooms` (`id`, `room_title`, `host_id`, `created_at`, `updated_at`) VALUES
(1, 'test_lesson', 1, '2021-07-13 03:48:27', '2021-07-13 07:38:58');

-- --------------------------------------------------------

--
-- 테이블 구조 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 테이블의 덤프 데이터 `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `type`, `created_at`, `updated_at`) VALUES
(1, 'teacher01', 'teacher01@gmail.com', 1, '2021-07-13 03:46:43', '2021-07-13 03:46:43'),
(2, 'student01', 'student01@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(3, 'student02', 'student02@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(4, 'student03', 'student03@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(5, 'student04', 'student04@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(6, 'student05', 'student05@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(7, 'student06', 'student06@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(8, 'student07', 'student07@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(9, 'student08', 'student08@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(10, 'student09', 'student09@gmail.com', 0, '2021-07-13 03:48:05', '2021-07-13 03:48:05'),
(11, 'student10', 'student10@gmail.com', 0, '2021-07-13 03:48:06', '2021-07-13 03:48:06');

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `online_users`
--
ALTER TABLE `online_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 테이블의 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
