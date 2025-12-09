-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 04:43 PM
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
-- Database: `hotel_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `checkin_date` date DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `guests` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `hotel_id`, `room_id`, `checkin_date`, `checkout_date`, `guests`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, 5, 15, '2025-12-09', '2025-12-10', 1, 1500.00, 'Confirmed', '2025-12-09 13:38:59'),
(2, 1, 5, 14, '2025-12-12', '2025-12-13', 2, 900.00, 'Cancelled', '2025-12-09 14:41:35'),
(3, 1, 25, 70, '2025-12-13', '2025-12-15', 1, 2600.00, 'Confirmed', '2025-12-09 15:08:52');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `city`, `address`, `description`, `images`, `created_at`) VALUES
(1, 'Nanded Grand', 'Nanded', 'Station Road, Nanded', 'Comfortable stay in the heart of Nanded with rooftop cafe and free wifi.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/202112161619344511-12712cc0792811ec99500a58a9feac02.jpg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(2, 'River View Nanded', 'Nanded', 'Near Godavari Riverside, Nanded', 'Scenic river views, family friendly, vegetarian breakfast available.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/202201101931427997-133079589bab11ed871e0a58a9feac02.jpg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(3, 'Heritage Inn Nanded', 'Nanded', 'Old Town, Nanded', 'Classic styling, close to temples and local markets.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/201512021510223299-d50ab4ae508b11eeb7d10a58a9feac02.jpg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(4, 'Business Stay Nanded', 'Nanded', 'IT Park Road, Nanded', 'Business-oriented hotel with meeting rooms and fast internet.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/202407051932421176-1a3302e9-b2fb-4cc4-b5a0-23f9005d22eb.jpg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(5, 'Budget Lodge Nanded', 'Nanded', 'Near Bus Stand, Nanded', 'Clean budget rooms with all essentials, best for short stays.', 'https://r1imghtlak.mmtcdn.com/cf55711088b211eaab6e0242ac110002.jfif?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(6, 'Sambhaji Suites', 'Sambhajinagar', 'MG Road, Sambhajinagar', 'Modern suites with kitchenette and free parking.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/201909101811053245-8c584cb0044c11eaa1090242ac110002.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(7, 'Central Park Hotel', 'Sambhajinagar', 'Near Central Park, Sambhajinagar', 'Good location for leisure travelers and families.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/room-imgs/200808292135275657-2189828-f5dab4bc462811ee993b0a58a9feac02.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(8, 'Aurum Residency', 'Sambhajinagar', 'Opposite Railway Station, Sambhajinagar', 'Comfortable rooms with complimentary breakfast.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/202311281508135630-f4a8a87e-08ef-4d3a-a59a-869d9256a245.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(9, 'Heritage Court Sambhajinagar', 'Sambhajinagar', 'Old City Area, Sambhajinagar', 'Traditionally inspired décor with modern amenities.', 'https://r1imghtlak.mmtcdn.com/0f6a37ead00111eb97730242ac110003.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(10, 'Economy Inn Sambhajinagar', 'Sambhajinagar', 'Market Road, Sambhajinagar', 'Affordable rooms, clean and convenient.', 'https://r1imghtlak.mmtcdn.com/c6fb8c0e-6df0-4f27-b3e5-07dd209264f0.JPG?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(11, 'Jalna Grand', 'Jalna', 'Airport Road, Jalna', 'Contemporary hotel featuring banquet halls and gym.', 'https://r1imghtlak.mmtcdn.com/92a22076-a871-4f8c-a5cc-6a818b78b947.jpeg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(12, 'Golden Tulip Jalna', 'Jalna', 'Near Bus Stand, Jalna', 'Good value rooms and friendly staff.', 'https://r1imghtlak.mmtcdn.com/9d8b1d46-df3a-40d6-bdf1-3f72fa50b00e.jpeg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(13, 'Comfort Stay Jalna', 'Jalna', 'MIDC Area, Jalna', 'Ideal for business travellers visiting industrial zone.', 'https://r1imghtlak.mmtcdn.com/3df18f5d-28ce-4f14-b17d-25558e565fa5.jpeg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(14, 'Riverside Retreat Jalna', 'Jalna', 'Riverfront Lane, Jalna', 'Calm location with garden and terrace dining.', 'https://r1imghtlak.mmtcdn.com/41f90596-2914-474e-8c62-e712814eab65.jpeg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(15, 'Pocket Hotel Jalna', 'Jalna', 'Main Market, Jalna', 'Simple rooms with necessary comforts.', 'https://r1imghtlak.mmtcdn.com/89478474-23ea-42b8-95cd-eb5b25b18e44.jpg?output-quality=75&output-format=webp&downsize=360:*', '2025-12-09 13:32:47'),
(16, 'Parbhani Palace', 'Parbhani', 'Station Road, Parbhani', 'Large hotel with spacious rooms and event facilities.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/201809261054383963-b03fb67c875311ecba100a58a9feac02.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(17, 'Shanti Garden Parbhani', 'Parbhani', 'Near Municipal Garden, Parbhani', 'Peaceful stay with garden views and in-house restaurant.', 'https://r1imghtlak.mmtcdn.com/f2215362f3c211ec85da0a58a9feac02.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(18, 'City Lodge Parbhani', 'Parbhani', 'Market Road, Parbhani', 'Centrally located lodging for budget travelers.', 'https://r1imghtlak.mmtcdn.com/564693bcb96b11ecaa920a58a9feac02.jpeg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(19, 'Executive Suites Parbhani', 'Parbhani', 'Industrial Area, Parbhani', 'Executive rooms and conference support.', 'https://r1imghtlak.mmtcdn.com/9cf6a8d6a7c411ed9c850a58a9feac02.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(20, 'Traveler Inn Parbhani', 'Parbhani', 'Near Bus Stand, Parbhani', 'No-frills comfortable rooms for short stays.', 'https://r1imghtlak.mmtcdn.com/902e23c01bdd11ea9c000242ac110003.jpg?output-quality=75&output-format=jpg&downsize=360:*', '2025-12-09 13:32:47'),
(21, 'Akola Comfort', 'Akola', 'Station Road, Akola', 'Well-located hotel with friendly services and restaurant.', 'https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/room-imgs/201412082100239413-3847-832a3d98e12411edb2280a58a9feac02.jpg?output-quality=75&downsize=243:162&output-format=jpg', '2025-12-09 13:32:47'),
(22, 'Greenleaf Hotel Akola', 'Akola', 'MG Road, Akola', 'Modern rooms, great for families and business guests.', 'https://r1imghtlak.mmtcdn.com/05cfa805-38a8-47e2-bbdd-9a7be77295f4.jpg?output-quality=75&downsize=243:162&output-format=jpg', '2025-12-09 13:32:47'),
(23, 'Regal Residency Akola', 'Akola', 'Near Court, Akola', 'Stylish rooms and rooftop dining area.', 'https://r1imghtlak.mmtcdn.com/ed9d8b70-9c42-4d3d-92e2-35b6e5e3c888.jpg?output-quality=75&downsize=243:162&output-format=jpg', '2025-12-09 13:32:47'),
(24, 'Budget Stay Akola', 'Akola', 'Market Area, Akola', 'Economy rooms, very clean and maintained.', 'https://r1imghtlak.mmtcdn.com/8da75766-ef58-403e-a843-61468a8293ae.jpg?output-quality=75&downsize=243:162&output-format=jpg', '2025-12-09 13:32:47'),
(25, 'Riverbank Suites Akola', 'Akola', 'Near Godavari Road, Akola', 'Comfortable suites with river views and breakfast.', 'https://r1imghtlak.mmtcdn.com/eb799c846bba11edb5430a58a9feac02.jpg?output-quality=75&downsize=243:162&output-format=jpg', '2025-12-09 13:32:47');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_quantity` int(11) DEFAULT 5,
  `max_guests` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `type`, `price`, `total_quantity`, `max_guests`) VALUES
(1, 1, 'Single', 1200.00, 8, 1),
(2, 1, 'Double', 2000.00, 10, 2),
(3, 1, 'Deluxe', 3500.00, 4, 3),
(4, 1, 'Suite', 6000.00, 2, 4),
(5, 2, 'Single', 1300.00, 6, 1),
(6, 2, 'Double', 2200.00, 8, 2),
(7, 2, 'Family', 4200.00, 3, 4),
(8, 3, 'Single', 1100.00, 10, 1),
(9, 3, 'Double', 1900.00, 8, 2),
(10, 3, 'Deluxe', 3000.00, 3, 3),
(11, 4, 'Single', 1400.00, 5, 1),
(12, 4, 'Double', 2400.00, 6, 2),
(13, 4, 'Suite', 5500.00, 2, 4),
(14, 5, 'Single', 900.00, 12, 1),
(15, 5, 'Double', 1500.00, 6, 2),
(16, 6, 'Single', 1500.00, 6, 1),
(17, 6, 'Double', 2600.00, 7, 2),
(18, 6, 'Deluxe', 4200.00, 3, 3),
(19, 6, 'Suite', 7000.00, 1, 4),
(20, 7, 'Single', 1200.00, 8, 1),
(21, 7, 'Double', 2100.00, 10, 2),
(22, 7, 'Family', 3800.00, 4, 4),
(23, 8, 'Single', 1000.00, 10, 1),
(24, 8, 'Double', 1800.00, 9, 2),
(25, 8, 'Deluxe', 3200.00, 3, 3),
(26, 9, 'Single', 1100.00, 8, 1),
(27, 9, 'Double', 2000.00, 7, 2),
(28, 9, 'Suite', 6500.00, 2, 4),
(29, 10, 'Single', 800.00, 14, 1),
(30, 10, 'Double', 1400.00, 6, 2),
(31, 11, 'Single', 1300.00, 7, 1),
(32, 11, 'Double', 2300.00, 8, 2),
(33, 11, 'Deluxe', 3600.00, 3, 3),
(34, 11, 'Suite', 5800.00, 2, 4),
(35, 12, 'Single', 1100.00, 9, 1),
(36, 12, 'Double', 1900.00, 8, 2),
(37, 12, 'Family', 4000.00, 3, 4),
(38, 13, 'Single', 1200.00, 10, 1),
(39, 13, 'Double', 2050.00, 7, 2),
(40, 13, 'Deluxe', 3300.00, 4, 3),
(41, 14, 'Single', 1400.00, 6, 1),
(42, 14, 'Double', 2500.00, 6, 2),
(43, 14, 'Suite', 6200.00, 2, 4),
(44, 15, 'Single', 900.00, 12, 1),
(45, 15, 'Double', 1600.00, 7, 2),
(46, 16, 'Single', 1300.00, 6, 1),
(47, 16, 'Double', 2200.00, 7, 2),
(48, 16, 'Deluxe', 3700.00, 3, 3),
(49, 17, 'Single', 1100.00, 8, 1),
(50, 17, 'Double', 1950.00, 6, 2),
(51, 17, 'Family', 3900.00, 3, 4),
(52, 18, 'Single', 900.00, 12, 1),
(53, 18, 'Double', 1500.00, 8, 2),
(54, 19, 'Single', 1400.00, 5, 1),
(55, 19, 'Double', 2600.00, 5, 2),
(56, 19, 'Suite', 6000.00, 1, 4),
(57, 20, 'Single', 850.00, 14, 1),
(58, 20, 'Double', 1450.00, 6, 2),
(59, 21, 'Single', 1250.00, 7, 1),
(60, 21, 'Double', 2100.00, 8, 2),
(61, 21, 'Deluxe', 3400.00, 3, 3),
(62, 22, 'Single', 1150.00, 8, 1),
(63, 22, 'Double', 2000.00, 7, 2),
(64, 22, 'Family', 4100.00, 3, 4),
(65, 23, 'Single', 1200.00, 9, 1),
(66, 23, 'Double', 2200.00, 6, 2),
(67, 23, 'Suite', 5600.00, 2, 4),
(68, 24, 'Single', 800.00, 15, 1),
(69, 24, 'Double', 1400.00, 8, 2),
(70, 25, 'Single', 1300.00, 6, 1),
(71, 25, 'Double', 2300.00, 6, 2),
(72, 25, 'Deluxe', 3900.00, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'abc', 'abc@gmail.com', '$2y$10$KhOJt/tBWdsAx33lIyI.XuPVUMo26aeaFXQoa1Z7e1RrMr5J0Y/Mi', 'user', '2025-12-09 13:28:43'),
(2, 'Admin', 'admin@gmail.com', '$2y$10$GIrN0OJ2TQoMT8hR.naV/OyC3V1cmuMKscHVm1vnjKyOImZWHmL.K', 'admin', '2025-12-09 14:56:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
