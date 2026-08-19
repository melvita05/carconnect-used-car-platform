-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 22, 2026 at 09:16 PM
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
-- Database: `online_car_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(2, 'Admin', 'admin@carconnect.com', '$2y$10$4Ov4Y.nHhKvGFJDtAsldweh2p9tQNgcEJPoloQqtraNIu0SabYms2', '2026-07-22 15:39:14');

-- --------------------------------------------------------

--
-- Table structure for table `buyers`
--

CREATE TABLE `buyers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buyers`
--

INSERT INTO `buyers` (`id`, `name`, `email`, `password`, `phone`, `address`, `status`, `created_at`) VALUES
(1, 'Demo Buyer', 'buyer@gmail.com', 'buyer123', '9876543210', 'India', 'active', '2026-07-22 15:20:51'),
(3, 'Leena', 'leena@gmail.com', '$2y$10$u/cgVeDDlLdIMYi689A9de8ng.H0O4KdD3e/Bb54ILz/370cUdtmi', '9887674567', NULL, 'active', '2026-07-22 18:11:32'),
(4, 'Leena', 'leena1@gmail.com', '$2y$10$E4UYzcRMAk7.OKV5fumLXukvcWnPV40f1L1yNXzN.dyn3iFUTB.iG', '9887674567', NULL, 'active', '2026-07-22 18:13:49'),
(5, 'Reena', 'reena@gmail.com', '$2y$10$9NW9asMfam4Sq9LSwZmfCeWvItIIAN3fviMhl9HeQXUf/XwucrBWq', '9764563478', NULL, 'active', '2026-07-22 18:16:21'),
(6, 'Santhosh', 'santhosh@gmail.com', '$2y$10$st5NEAb/JPmzZjCSc6WVU.k9QEJtlzbTmyqD6uyfh0ESpcUBH7dmK', '7856342345', NULL, 'active', '2026-07-22 18:49:38'),
(7, 'Treeza', 'treeza@gmail.com', '$2y$10$4otp.GgNCPRlPWa9ihnuCe82jAXUeBfn.9CMa6gHginxZKct6GhAe', '9876543235', NULL, 'active', '2026-07-22 18:59:40');

-- --------------------------------------------------------

--
-- Table structure for table `car_categories`
--

CREATE TABLE `car_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_categories`
--

INSERT INTO `car_categories` (`id`, `name`, `created_at`) VALUES
(1, 'SUV', '2026-07-22 15:20:51'),
(2, 'Sedan', '2026-07-22 15:20:51'),
(3, 'Hatchback', '2026-07-22 15:20:51'),
(4, 'Electric', '2026-07-22 15:20:51'),
(5, 'Luxury', '2026-07-22 15:20:51'),
(6, 'Sports', '2026-07-22 15:20:51'),
(7, 'MUV', '2026-07-22 16:17:10');

-- --------------------------------------------------------

--
-- Table structure for table `car_listings`
--

CREATE TABLE `car_listings` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `make` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `mileage` int(11) DEFAULT 0,
  `fuel_type` varchar(50) DEFAULT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','sold') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `color` varchar(30) DEFAULT NULL,
  `owner_type` varchar(30) DEFAULT NULL,
  `body_type` varchar(30) DEFAULT NULL,
  `seating_capacity` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_listings`
--

INSERT INTO `car_listings` (`id`, `seller_id`, `category_id`, `make`, `model`, `year`, `price`, `mileage`, `fuel_type`, `transmission`, `description`, `image_path`, `status`, `created_at`, `color`, `owner_type`, `body_type`, `seating_capacity`, `location`) VALUES
(5, 6, 1, 'Hyundai', 'Creta SX', 2021, 1250000.00, 38500, 'Diesel', 'Manual', 'Well-maintained 2021 Hyundai Creta SX Diesel. Single owner, company serviced, no accident history, excellent engine condition, smooth clutch and gearbox, chilled AC, touchscreen infotainment with Android Auto & Apple CarPlay, reverse camera, ABS, dual airbags, alloy wheels, all documents available and insurance active. Ready for immediate sale.', '/carconnect/uploads/car_6a60ea42e2636.jpg', 'pending', '2026-07-22 16:05:22', 'White', 'First Owner', 'SUV', 5, 'Manglore'),
(6, 6, 2, 'Honda', 'City VX', 2020, 920000.00, 46000, 'Petrol', 'Manual', 'Excellent condition Honda City VX with complete service history, powerful petrol engine, smooth drive, touchscreen infotainment, rear camera, automatic climate control, alloy wheels, and all original documents available. No accidents or flood damage.', '/carconnect/uploads/car_6a60eaa7780a5.jpg', 'approved', '2026-07-22 16:07:03', 'Silver', 'First Owner', 'Sedan', 5, 'Udupi'),
(7, 6, 1, 'Tata', 'Nexon XZ+', 2022, 1080000.00, 24000, 'Petrol', 'Manual', 'Excellent condition, company serviced, reverse camera, touchscreen, alloy wheels, no accident history.', '/carconnect/uploads/car_6a60eb1aed9cf.jpg', 'approved', '2026-07-22 16:08:58', 'Blue', 'First Owner', 'SUV', 5, 'Manglore'),
(8, 6, 3, 'Maruti', 'Baleno Alpha', 2021, 745000.00, 33000, 'Petrol', 'Manual', 'Single owner, showroom maintained, Android Auto, airbags, ABS, excellent mileage.', '/carconnect/uploads/car_6a60eb6ba710a.jpg', 'approved', '2026-07-22 16:10:19', 'White', 'First Owner', 'Hatchback', 5, 'Udupi'),
(9, 6, 1, 'Mahindra', 'XUV700AX5', 2023, 1980000.00, 18000, 'Diesel', 'Automatic', 'Luxury SUV with panoramic sunroof, ADAS, 360° camera, company service history.', '/carconnect/uploads/car_6a60ebe24f9f7.jpg', 'sold', '2026-07-22 16:12:18', 'Silver', 'First Owner', 'SUV', 7, 'Mysore'),
(10, 7, 1, 'Kia', 'Seltos HTX', 2022, 1450000.00, 26000, 'Diesel', 'Manual', 'Premium SUV with ventilated seats, touchscreen, Bose speakers, rear camera, excellent condition.', '/carconnect/uploads/car_6a60ec70debb2.jpg', 'approved', '2026-07-22 16:14:40', 'Red', 'First Owner', 'SUV', 5, 'Bangalore'),
(11, 7, 2, 'Honda', 'City ZX', 2020, 975000.00, 42000, 'Petrol', 'Automatic', 'Smooth automatic transmission, leather seats, cruise control, push-button start, all records available.', '/carconnect/uploads/car_6a60eccc61db0.jpg', 'pending', '2026-07-22 16:16:12', 'Grey', 'Second Owner', 'Sedan', 5, 'Manglore'),
(12, 7, 7, 'Toyota', 'Innova Crysta GX', 2021, 1890000.00, 51000, 'Diesel', 'Manual', 'Family vehicle with excellent comfort, complete service history, no accident record, powerful diesel engine, ready for long drives.', '/carconnect/uploads/car_6a60edc5efb7c.jpg', 'approved', '2026-07-22 16:20:21', 'White', 'First Owner', 'MUV', 7, 'Manglore'),
(13, 7, 1, 'Toyota', 'Fortuner 4x2', 2022, 3450000.00, 28000, 'Diesel', 'Automatic', 'Excellent condition vehicle with complete service history. Single owner, no accidents, powerful engine, smooth gearbox, touchscreen infotainment, reverse camera, ABS, dual airbags, alloy wheels, chilled AC, and all documents available. Ready for immediate sale.', '/carconnect/uploads/car_6a60eed6ad64e.jpg', 'approved', '2026-07-22 16:24:54', 'White', 'First Owner', 'SUV', 7, 'Mangalore'),
(14, 7, 3, 'Hyundai', 'i20 Sportz', 2021, 720000.00, 31000, 'Petrol', 'Manual', 'Well-maintained hatchback with low mileage and excellent fuel efficiency. Company serviced, clean interior, good tyres, working AC, music system, power steering, power windows, and all papers up to date.', '/carconnect/uploads/car_6a60efa1e726e.jpg', 'approved', '2026-07-22 16:28:17', 'Grey', 'Third Owner', 'Hatchback', 5, 'Udupi'),
(15, 8, 1, 'Kia', 'Sonet HTK+', 2022, 1180000.00, 22000, 'Petrol', 'Manual', 'Excellent condition vehicle with complete service history. Fourth owner, no accidents, powerful engine, smooth gearbox, touchscreen infotainment, reverse camera, ABS, dual airbags, alloy wheels, chilled AC, and all documents available. Ready for immediate sale.', '/carconnect/uploads/car_6a60f0dbacd77.jpg', 'approved', '2026-07-22 16:33:31', 'Red', 'Fourth Owner', 'SUV', 5, 'Bangalore'),
(16, 8, 2, 'Honda', 'Amaze VX', 2020, 740000.00, 45000, 'Petrol', 'Automatic', 'Premium sedan in excellent condition with automatic climate control, rear parking camera, cruise control, touchscreen infotainment, alloy wheels, and complete service records. No accident history.', '/carconnect/uploads/car_6a60f206088e9.jpg', 'pending', '2026-07-22 16:38:30', 'Silver', 'Third Owner', 'Sedan', 5, 'Mysore'),
(17, 8, 1, 'Tata', 'Harrier XZ+', 2021, 1720000.00, 39000, 'Diesel', 'Manual', 'Excellent condition vehicle with complete service history. Single owner, no accidents, powerful engine, smooth gearbox, touchscreen infotainment, reverse camera, ABS, dual airbags, alloy wheels, chilled AC, and all documents available. Ready for immediate sale.', '/carconnect/uploads/car_6a60f2f4bdb2b.jpg', 'sold', '2026-07-22 16:42:28', 'Black', 'First Owner', 'SUV', 5, 'Manglore'),
(18, 9, 1, 'Mahindra', 'Scorprio NZ8', 2023, 2190000.00, 17000, 'Diesel', 'Automatic', 'Excellent condition vehicle with complete service history. Fourth owner, no accidents, powerful engine, smooth gearbox, touchscreen infotainment, reverse camera, ABS, dual airbags, alloy wheels, chilled AC, and all documents available. Ready for immediate sale.', '/carconnect/uploads/car_6a60f425d2c3d.jpg', 'approved', '2026-07-22 16:47:33', 'Green', 'Fourth Owner', 'SUV', 7, 'Hubli'),
(19, 9, 3, 'Maruti', 'Swift VXi', 2019, 580000.00, 52000, 'Petrol', 'Manual', 'Well-maintained hatchback with low mileage and excellent fuel efficiency. Company serviced, clean interior, good tyres, working AC, music system, power steering, power windows, and all papers up to date.', '/carconnect/uploads/car_6a60f4dd3e077.jpg', 'approved', '2026-07-22 16:50:37', 'White', 'Second Owner', 'Hatchback', 5, 'Udupi'),
(20, 11, 3, 'Renault', 'Kwid Climber', 2020, 420000.00, 36000, 'Petrol', 'Manual', 'Well-maintained hatchback with low mileage and excellent fuel efficiency. Company serviced, clean interior, good tyres, working AC, music system, power steering, power windows, and all papers up to date.', '/carconnect/uploads/car_6a60f67ae3328.jpg', 'sold', '2026-07-22 16:57:30', 'Silver', 'Third Owner', 'Hatchback', 5, 'Mangalore'),
(21, 11, 2, 'Volkswagen', 'Virtus Highline', 2023, 1380000.00, 18500, 'Petrol', 'Manual', 'Premium sedan in excellent condition with automatic climate control, rear parking camera, cruise control, touchscreen infotainment, alloy wheels, and complete service records. No accident history.', '/carconnect/uploads/car_6a60f7684c0a9.jpg', 'sold', '2026-07-22 17:01:28', 'Blue', 'Fourth Owner', 'Sedan', 5, 'Bangalore');

-- --------------------------------------------------------

--
-- Table structure for table `car_views`
--

CREATE TABLE `car_views` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `car_id`, `sender_id`, `receiver_id`, `message`, `created_at`) VALUES
(1, 21, 5, 11, 'Hii sir', '2026-07-22 18:37:45'),
(2, 21, 5, 11, 'is this car still available?', '2026-07-22 18:38:37'),
(3, 21, 11, 5, 'hello', '2026-07-22 18:46:02'),
(4, 21, 11, 5, 'yes,its available', '2026-07-22 18:46:19'),
(5, 17, 6, 8, 'hii sir', '2026-07-22 18:50:38'),
(6, 17, 6, 8, 'is this car still available?', '2026-07-22 18:50:52'),
(7, 10, 6, 7, 'Hii, is this car available??', '2026-07-22 18:54:19'),
(8, 9, 7, 6, 'hello madam', '2026-07-22 19:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `order_status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `seller_id`, `car_id`, `total_price`, `order_status`, `created_at`) VALUES
(3, 5, 11, 21, 1380000.00, 'completed', '2026-07-22 18:39:07'),
(4, 5, 11, 20, 420000.00, 'completed', '2026-07-22 18:42:42'),
(5, 6, 8, 17, 1720000.00, 'completed', '2026-07-22 18:52:30'),
(6, 6, 7, 10, 1450000.00, 'pending', '2026-07-22 18:54:44'),
(7, 7, 6, 9, 1980000.00, 'completed', '2026-07-22 19:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `buyer_id`, `amount`, `payment_method`, `payment_status`, `created_at`) VALUES
(3, 3, 5, 1380000.00, 'UPI', 'paid', '2026-07-22 18:39:08'),
(4, 4, 5, 420000.00, 'Credit Card', 'paid', '2026-07-22 18:42:42'),
(5, 5, 6, 1720000.00, 'Debit Card', 'paid', '2026-07-22 18:52:30'),
(6, 6, 6, 1450000.00, 'Net Banking', 'paid', '2026-07-22 18:54:44'),
(7, 7, 7, 1980000.00, 'UPI', 'paid', '2026-07-22 19:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `issue` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `car_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(3, 21, 5, 5, 'Excellent car! The condition was exactly as described. Smooth engine, clean interior, and the seller was very helpful. Highly recommended.', '2026-07-22 18:36:53'),
(4, 20, 5, 5, 'Fantastic experience! The car matched the photos, and the engine performance is excellent.', '2026-07-22 18:45:19'),
(5, 17, 6, 5, 'One of the best used cars I\'ve purchased. Highly recommend this seller to anyone looking for a reliable vehicle.', '2026-07-22 18:51:53'),
(6, 10, 6, 5, 'Smooth buying experience. The seller was professional, and the car exceeded my expectations.', '2026-07-22 18:53:25'),
(7, 9, 7, 5, 'Great family car with good mileage. Delivery was quick, and all documents were genuine.', '2026-07-22 19:00:42');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `name`, `email`, `password`, `phone`, `address`, `status`, `created_at`) VALUES
(1, 'Demo Seller', 'seller@gmail.com', 'seller123', '9876543210', 'India', 'active', '2026-07-22 15:20:51'),
(6, 'Joylin', 'joylin@gmail.com', '$2y$10$wQLKYPtL0JQBBk2EjPeXxuGZe9FzgvGoCP6PO/mbevERMr2uo2e66', '9887674567', NULL, 'active', '2026-07-22 16:01:50'),
(7, 'Milton', 'Milton@gmail.com', '$2y$10$0VL6MihbZlO9JUVsPRI5nOp.1YustFJa8b7f1Xea0xNVxoVQjkLgO', '7996803142', NULL, 'active', '2026-07-22 16:13:07'),
(8, 'Primal Melvita', 'primal@gmail.com', '$2y$10$VoFmPSs5iBQDH0L9nBCHzOakty2DRZGVfrOZ3JocTILnmjLxluSz.', '7892762829', NULL, 'active', '2026-07-22 16:29:26'),
(9, 'Akshay', 'akshay@gmail.com', '$2y$10$qWsBKWPRV.el9pWYio1FxuN7NdG8PMMscb18bRnosp1c0C.krA5pi', '8277027563', NULL, 'active', '2026-07-22 16:43:13'),
(11, 'Karthik', 'karthik@gmail.com', '$2y$10$QqwZqX1hYy.I2w1wS8MQhepNvhhvUjU4uRLSp.KoZt6G2p2Mnqagy', '8978656344', NULL, 'active', '2026-07-22 16:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `buyer_id`, `car_id`, `created_at`) VALUES
(2, 5, 21, '2026-07-22 18:35:38'),
(3, 6, 14, '2026-07-22 18:50:12'),
(4, 6, 10, '2026-07-22 18:50:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `buyers`
--
ALTER TABLE `buyers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `car_categories`
--
ALTER TABLE `car_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `car_listings`
--
ALTER TABLE `car_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_car_status` (`status`);

--
-- Indexes for table `car_views`
--
ALTER TABLE `car_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_chat` (`car_id`,`sender_id`,`receiver_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `idx_orders_buyer` (`buyer_id`),
  ADD KEY `idx_orders_seller` (`seller_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`car_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`buyer_id`,`car_id`),
  ADD KEY `car_id` (`car_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `buyers`
--
ALTER TABLE `buyers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `car_categories`
--
ALTER TABLE `car_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `car_listings`
--
ALTER TABLE `car_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `car_views`
--
ALTER TABLE `car_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car_listings`
--
ALTER TABLE `car_listings`
  ADD CONSTRAINT `car_listings_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `car_listings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `car_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `car_views`
--
ALTER TABLE `car_views`
  ADD CONSTRAINT `car_views_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `car_listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `car_views_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `contact_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `car_listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`car_id`) REFERENCES `car_listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `car_listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `car_listings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
