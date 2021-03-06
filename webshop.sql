-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2019 at 09:58 PM
-- Server version: 10.1.39-MariaDB
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_basket`
--

CREATE TABLE `tbl_basket` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `cookie` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_basket`
--

INSERT INTO `tbl_basket` (`id`, `product_id`, `amount`, `cookie`, `order_id`, `user_id`) VALUES
(21, 1, 9, 0, 16, 3),
(22, 2, 1, 0, 17, 3),
(23, 4, 2, 0, 17, 3),
(24, 6, 24, 0, 17, 3),
(25, 7, 10, 0, 21, 3),
(26, 1, 10, 0, 24, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cookie`
--

CREATE TABLE `tbl_cookie` (
  `id` int(11) NOT NULL,
  `id_users` int(11) DEFAULT NULL,
  `cookie` varchar(20) NOT NULL,
  `logged_in` tinyint(1) NOT NULL,
  `expire_date` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `payment` varchar(255) NOT NULL,
  `delivery` varchar(19) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_orders`
--

INSERT INTO `tbl_orders` (`id`, `user_id`, `address`, `payment`, `delivery`) VALUES
(16, 3, 'hoang home', 'Mastercard', 'DHL'),
(17, 3, 'phi home', 'Paypal', 'DHL'),
(18, 3, 'FH Kiel', 'Paypal', 'Dpd'),
(19, 3, 'fh kiel', 'Mastercard', 'Dpd'),
(20, 3, 'gh kiel', 'Visa', 'DHL'),
(21, 3, 'h', 'Visa', 'DHL'),
(22, 3, 'h', 'Paypal', 'Dpd'),
(23, 3, 'fg gs', 'Visa', 'DHL'),
(24, 3, 'hoang', 'Visa', 'DHL');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `id` int(11) NOT NULL,
  `product_name` varchar(20) NOT NULL,
  `product_desc` text NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`id`, `product_name`, `product_desc`, `product_price`, `product_stock`) VALUES
(1, 'Usb 128 gb', 'Powerful light output, up to 300MB/s with 3.1 USB interface ', 33, 65),
(2, 'Iphone7', 'Apple handy', 799, 17),
(3, 'HP Envy 13', 'HP ultrabook', 775, 19),
(4, 'Google pixel 3a', 'New android phone', 499, 18),
(5, 'Google pixel 3X', 'New android phone', 499, 20),
(6, 'Huawei matebook D', 'New window laptop', 599, 176),
(7, 'Google pixel 5', 'New android phone', 999, 0),
(8, 'Google pixel 6', 'New android phone', 999, 20),
(9, 'Google pixel 7', 'New android phone', 999, 30),
(10, 'Google pixel 8', 'New android phone', 999, 40);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `name`, `email`, `password`) VALUES
(1, 'hoang', 'nguyenvuhoang1994@gmail.com', '$2y$10$gnxaLpif7pxDW1Rr9HeFxO2X51X/2Cd2E91bmX832PFwPex53Ivl.'),
(3, 'hoangkiel', 'nguyenvuhoang1994@gmail.com', '$2y$10$LETtVKypq.AugUPUrXqvdeHALxHeArIpidTZGw8qUMt.MWEszFP/6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_basket`
--
ALTER TABLE `tbl_basket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD UNIQUE KEY `UNIQUE` (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_basket`
--
ALTER TABLE `tbl_basket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
