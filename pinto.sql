-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 16, 2020 at 04:05 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id12932277_pinto`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `admin_name` varchar(50) NOT NULL,
  `admin_level` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `admin_joindate` datetime NOT NULL,
  `admin_phone` varchar(13) NOT NULL,
  `admin_email` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `admin_isactive` enum('0','1') NOT NULL DEFAULT '1',
  `admin_dateinactive` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `employee_id`, `admin_name`, `admin_level`, `admin_joindate`, `admin_phone`, `admin_email`, `comments`, `admin_isactive`, `admin_dateinactive`) VALUES
(1, 'MN000001', 'Anura DeSilva', 1, '2017-11-26 07:30:00', '(011)2698556', 'anura@gmail.com', '', '0', '2018-01-14 05:54:26'),
(2, 'EX000001', 'Jafar Basha', 1, '2017-11-26 07:30:00', '(011)2698556', 'ssss', '', '0', '2018-01-13 20:13:51'),
(3, 'ST000003', 'Arjuna Jayasinghe', 2, '2017-11-26 07:30:00', '(011)2698556', NULL, NULL, '0', '2018-01-13 20:04:54'),
(4, 'ST000001', 'Arjuna Jayasingha', 2, '2018-01-13 00:00:00', '(011)2698556', 'arjuna@gmail.com', '', '0', '2018-01-13 20:13:57'),
(5, 'EX000002', 'Jafar Basha', 1, '2018-01-13 00:00:00', '(011)2698556', '', '', '1', NULL),
(6, 'MN000000', 'Anura DeSilva', 1, '2018-01-14 00:00:00', '(011)2698556', 'anura@gmail.com', '', '0', '2018-02-20 20:02:45'),
(7, 'ST000004', 'Arjuna Jayasingha', 2, '2018-01-14 00:00:00', '(011)2698556', 'arjuna@gmail.com', '', '1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_login`
--

CREATE TABLE `admin_login` (
  `admin_username` varchar(50) NOT NULL,
  `admin_password` char(41) NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_login`
--

INSERT INTO `admin_login` (`admin_username`, `admin_password`, `admin_id`) VALUES
('Arjuna', '*71909127F77695357AC545CB79C9605F385EC0A2', 7),
('JafarB', '*7B23C2004843F793AA35986819D2B9D70EAB741E', 5);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` int(10) UNSIGNED NOT NULL,
  `cust_name` varchar(50) NOT NULL,
  `cust_address` varchar(255) NOT NULL,
  `cust_city` enum('Colombo 1','Colombo 2','Colombo 3','Colombo 4','Colombo 5','Colombo 6','Colombo 7','Colombo 8','Colombo 9','Colombo 10','Colombo 11','Colombo 12','Colombo 13','Colombo 14','Colombo 15') NOT NULL,
  `cust_joindate` datetime NOT NULL,
  `cust_phone` varchar(13) NOT NULL,
  `cust_email` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `cust_isactive` enum('0','1') NOT NULL DEFAULT '1',
  `cust_dateinactive` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `cust_name`, `cust_address`, `cust_city`, `cust_joindate`, `cust_phone`, `cust_email`, `comments`, `cust_isactive`, `cust_dateinactive`) VALUES
(1, 'Gehan Perera', '340/11 Kuruppu Lane', 'Colombo 8', '2018-01-20 00:00:00', '(011)2697606', 'gehan@yahoo.com', '', '1', NULL),
(2, 'Dimuthu Yapa', '233 Pinnewala Road', 'Colombo 1', '2018-01-20 00:00:00', '(011)2697333', 'yapa@yahoo.com', '', '0', '2018-01-20 13:16:18');

-- --------------------------------------------------------

--
-- Table structure for table `cust_login`
--

CREATE TABLE `cust_login` (
  `cust_username` varchar(50) NOT NULL,
  `cust_password` char(41) NOT NULL,
  `cust_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cust_login`
--

INSERT INTO `cust_login` (`cust_username`, `cust_password`, `cust_id`) VALUES
('gehanp', '*3E6FF23F8EC4EEC5AC3A5A59B68364D9F6085067', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_total` decimal(8,2) NOT NULL,
  `order_cust` varchar(50) NOT NULL,
  `order_address` varchar(255) NOT NULL,
  `order_city` enum('Colombo 1','Colombo 2','Colombo 3','Colombo 4','Colombo 5','Colombo 6','Colombo 7','Colombo 8','Colombo 9','Colombo 10','Colombo 11','Colombo 12','Colombo 13','Colombo 14','Colombo 15') NOT NULL,
  `order_phone` varchar(13) NOT NULL,
  `order_time` datetime NOT NULL,
  `order_instructions` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `order_status` enum('Pending','Confirmed','Cancelled','Delivered') NOT NULL DEFAULT 'Pending',
  `status_updatetime` datetime DEFAULT NULL,
  `cust_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_total`, `order_cust`, `order_address`, `order_city`, `order_phone`, `order_time`, `order_instructions`, `comments`, `order_status`, `status_updatetime`, `cust_id`) VALUES
(1, 2050.00, 'Dimuthu Tharanga', '46 Marine Drive', 'Colombo 5', '(011)2697606', '2018-01-27 13:22:12', 'Near construction site', '', 'Delivered', '2018-02-07 21:29:18', NULL),
(2, 3450.00, 'Charindu', '7 Batik Lane', 'Colombo 3', '(071)4563721', '2018-01-28 06:37:05', 'Near temple', '', 'Delivered', '2018-02-07 21:30:27', NULL),
(3, 2300.00, 'Jayantha Atlas', '200 Galle Road', 'Colombo 5', '(011)2344603', '2018-01-28 06:45:06', '', '', 'Delivered', '2018-02-07 21:30:47', NULL),
(4, 2950.00, 'Ayesh Cooray', '5 Baudhaloka Mawatha', 'Colombo 7', '(011)2252379', '2018-01-30 16:23:47', 'Near the DS school', 'Customer will be home only after 2 hours!', 'Pending', '2018-02-03 12:05:06', NULL),
(6, 5850.00, 'Dinesh Yapa', '300 Galle Road', 'Colombo 5', '(077)7331818', '2018-02-04 09:20:49', 'Near gas station', NULL, 'Pending', '0000-00-00 00:00:00', NULL),
(7, 2550.00, 'Nishika Silva', '40 Kotta Road', 'Colombo 8', '(011)2254449', '2018-02-04 10:51:15', 'Near Railway crossing', '', 'Delivered', '2018-02-07 21:31:30', NULL),
(9, 2950.00, 'Diane Sparrow', '30 Wickramasinge Road', 'Colombo 7', '(077)7332020', '2018-02-09 11:18:25', 'House located opposite Sadeepa Book Shop', NULL, 'Pending', '0000-00-00 00:00:00', NULL),
(10, 3450.00, 'Gehan Perera', '25 Galle Road', 'Colombo 5', '(071)5364412', '2018-02-09 11:34:04', 'Near ESOFT School', NULL, 'Pending', '0000-00-00 00:00:00', 1),
(11, 4050.00, 'Dinesh Chandimal', '71 Dr N.M. Perera Mw', 'Colombo 8', '(011)2238379', '2018-02-09 11:51:07', '', NULL, 'Pending', '0000-00-00 00:00:00', NULL),
(12, 6450.00, 'Dananjaya', 'galled road', 'Colombo 6', '(077)7332020', '2018-02-14 04:43:55', '', '', 'Delivered', '2018-02-14 04:44:37', NULL),
(13, 2950.00, 'Gihan Silva', '35 Kuruppu Road', 'Colombo 8', '(011)2254449', '2018-02-14 04:47:05', '', '', 'Delivered', '2018-02-14 04:47:40', NULL),
(14, 10950.00, 'Felix Mendis', '56 Dharmapala Mawatha', 'Colombo 7', '(077)8552020', '2018-02-23 05:13:02', 'Opposite the Library', NULL, 'Pending', '0000-00-00 00:00:00', NULL),
(15, 1750.00, 'Umesh Raginda', '23 Sri Vimalasiri Mawatha', 'Colombo 4', '(077)8552020', '2018-02-26 22:09:54', '', NULL, 'Pending', '0000-00-00 00:00:00', NULL),
(16, 10650.00, 'Dananjaya Silva', '700 Galle Road', 'Colombo 6', '(011)2254660', '2018-02-27 00:05:22', 'Near Shopping Mall', '', 'Pending', '2018-02-27 00:12:26', NULL),
(17, 3850.00, 'Kulasekara', '37 Galle Road', 'Colombo 4', '(011)2254449', '2018-02-28 21:32:16', '', '', 'Delivered', '2018-02-28 21:52:36', NULL),
(18, 4850.00, 'Punter', '20 Plymouth Blvd', 'Colombo 1', '(345)6789078', '2020-03-15 17:57:07', '', NULL, 'Pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_product`
--

CREATE TABLE `order_product` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  `quantity` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `cust_instructions` varchar(255) DEFAULT NULL,
  `vendor_response` enum('Accept','Reject') DEFAULT NULL,
  `vendor_comments` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `pinto_comments` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_product`
--

INSERT INTO `order_product` (`order_id`, `product_id`, `unit_price`, `quantity`, `cust_instructions`, `vendor_response`, `vendor_comments`, `comments`, `pinto_comments`) VALUES
(1, 15, 1000.00, 1, '', 'Reject', 'Not Available!', NULL, NULL),
(1, 16, 800.00, 1, 'Well Done!', 'Accept', '', NULL, NULL),
(2, 18, 700.00, 2, '', 'Accept', '', NULL, NULL),
(2, 19, 1800.00, 1, 'Pan Crust!', NULL, NULL, NULL, NULL),
(3, 14, 400.00, 2, '', NULL, NULL, NULL, NULL),
(3, 11, 450.00, 1, '', '', '', NULL, NULL),
(3, 16, 800.00, 1, 'Medium Rare!', NULL, 'Slight Delay', NULL, NULL),
(4, 11, 450.00, 2, '', NULL, NULL, NULL, NULL),
(9, 11, 450.00, 2, 'Not spicy!', NULL, NULL, NULL, NULL),
(4, 19, 1800.00, 1, '', NULL, NULL, NULL, NULL),
(6, 17, 1000.00, 3, '', 'Accept', '', NULL, NULL),
(6, 18, 700.00, 1, 'light on the veges', 'Reject', 'unavailable', NULL, NULL),
(6, 21, 1900.00, 1, 'No anchovies!', NULL, NULL, NULL, NULL),
(7, 11, 450.00, 2, 'Spicy', NULL, NULL, NULL, NULL),
(7, 18, 700.00, 2, '', 'Accept', '', NULL, NULL),
(9, 19, 1800.00, 1, '', 'Accept', '', NULL, NULL),
(10, 16, 800.00, 2, '', NULL, NULL, NULL, NULL),
(10, 22, 1600.00, 1, '', 'Reject', 'Sorry - Not Available Today!', NULL, NULL),
(11, 21, 1900.00, 2, 'Extra Veggies please!', 'Accept', '', NULL, NULL),
(14, 15, 1000.00, 3, 'Light on the Vegies', NULL, NULL, NULL, NULL),
(12, 17, 1000.00, 1, '', NULL, NULL, NULL, NULL),
(12, 16, 800.00, 4, '', NULL, NULL, NULL, NULL),
(12, 15, 1000.00, 2, '', NULL, NULL, NULL, NULL),
(13, 15, 1000.00, 1, '', NULL, NULL, NULL, NULL),
(13, 17, 1000.00, 1, '', NULL, NULL, NULL, NULL),
(13, 18, 700.00, 1, '', NULL, NULL, NULL, NULL),
(14, 18, 700.00, 1, '', NULL, NULL, NULL, NULL),
(14, 19, 1800.00, 3, '', NULL, NULL, NULL, NULL),
(14, 22, 1600.00, 1, '', NULL, NULL, NULL, NULL),
(15, 10, 550.00, 2, 'Not too spicy!', 'Accept', '', NULL, NULL),
(15, 14, 400.00, 1, 'Extra Hot!', 'Reject', 'Item is unavailable today!', NULL, NULL),
(16, 15, 1000.00, 3, '', NULL, NULL, NULL, NULL),
(16, 17, 1000.00, 5, '', NULL, NULL, NULL, NULL),
(16, 11, 450.00, 2, '', NULL, NULL, NULL, NULL),
(16, 13, 750.00, 2, 'Extra hot sauce please!', NULL, NULL, NULL, NULL),
(17, 19, 1800.00, 1, '', 'Accept', '1 hour please', NULL, NULL),
(17, 14, 400.00, 2, 'Spicy!', NULL, NULL, NULL, NULL),
(17, 15, 1000.00, 1, 'No Beef', 'Accept', 'Sure. No Probs! Pork it is!', NULL, 'Is it possible to add pork instead of Beef?'),
(18, 15, 1000.00, 2, '', NULL, NULL, NULL, NULL),
(18, 17, 1000.00, 1, '', NULL, NULL, NULL, NULL),
(18, 20, 1600.00, 1, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_desc` text DEFAULT NULL,
  `product_price` decimal(8,2) NOT NULL,
  `product_dateadded` datetime NOT NULL,
  `comments` text DEFAULT NULL,
  `product_isactive` enum('0','1') NOT NULL DEFAULT '1',
  `product_dateinactive` datetime DEFAULT NULL,
  `availability` enum('0','1') NOT NULL DEFAULT '1',
  `vendor_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_desc`, `product_price`, `product_dateadded`, `comments`, `product_isactive`, `product_dateinactive`, `availability`, `vendor_id`) VALUES
(1, 'Pepperoni Pizza', 'Our favorite topping - pepperoni!', 1500.00, '2017-12-01 00:00:00', NULL, '0', NULL, '1', 2),
(2, 'Smoked Salmon & Avocado Pizza', 'Smoked salmon, ripe avocado, baby spinach, onion, garlic, lemon pepper, pizza sauce and mozzarella garnished with garlic aioli, baby capers, fresh shallots and lemon wedges.', 1900.00, '2017-11-26 14:00:00', NULL, '0', NULL, '1', 2),
(3, 'Piri Piri Chicken', 'Piri piri sauce, oven roasted chicken, Spanish onion, crushed garlic, red capsicum, cherry tomatoes, mozzarella and African spice garnished with aioli shallots, cashew nuts and lemon wedges.', 1600.00, '2017-11-26 14:00:00', NULL, '0', NULL, '1', 2),
(4, 'Vegetarian', 'Fresh button mushrooms, Spanish onions, roasted capsicums, kalamata olives, pineapple, signature pizza sauce, medley of herbs, crushed garlic and mozzarella garnished with fresh parsley and parmesan.', 1500.00, '2017-11-26 14:00:00', NULL, '0', NULL, '1', 2),
(5, 'Ham & Pineapple', 'Sliced pineapples, ham, mozzarella and pizza sauce.', 1750.00, '2017-11-26 14:00:00', NULL, '0', NULL, '1', 2),
(6, 'Chinese Chicken Rolls', 'Great Roll', 80.00, '2017-12-01 00:00:00', NULL, '0', NULL, '1', 2),
(7, 'Chinese Fish Rolls', 'Great!', 60.00, '2017-12-01 00:00:00', NULL, '0', NULL, '1', 2),
(8, 'Chinese Mutton Rolls', '', 100.00, '2018-01-10 00:00:00', NULL, '0', '2018-01-27 12:07:45', '1', 2),
(9, 'Chinese Chicken Rolls', 'best2 chicken!', 90.00, '2017-12-01 00:00:00', NULL, '0', '2018-01-27 12:07:31', '1', 2),
(10, 'Nasi Goreng', 'Our most popular dish!', 550.00, '2018-01-27 11:33:43', NULL, '1', NULL, '1', 8),
(11, 'Fried Rice with Chicken', '', 450.00, '2018-01-27 11:36:27', NULL, '1', NULL, '1', 8),
(12, 'Fried Rice with Shrimp', 'Seafood made from the freshest ingredients!', 450.00, '2018-01-27 11:38:33', NULL, '1', NULL, '1', 8),
(13, 'Singapore Fried Meehoon', 'Delicious noodles with chicken & prawn', 750.00, '2018-01-27 11:41:53', NULL, '1', NULL, '1', 8),
(14, 'Dragon\'s Devilled Pork', 'Hot & Spicy!', 400.00, '2018-01-27 11:43:25', NULL, '1', NULL, '1', 8),
(15, 'Signature Club Sandwich', 'The king among sandwiches!', 1000.00, '2018-01-27 11:58:28', NULL, '1', NULL, '1', 1),
(16, 'Bistro Seafood Burger', 'Recommended for seafood & burger lovers alike!', 800.00, '2018-01-27 12:01:22', NULL, '1', '2018-02-20 19:06:16', '1', 1),
(17, 'Caesar Salad with Chicken', 'Made from the healthiest of ingredients!', 1000.00, '2018-01-27 12:02:33', NULL, '1', '2018-02-20 18:59:46', '1', 1),
(18, 'Grilled Chicken Sub', 'Guaranteed to satisfy your taste buds!', 700.00, '2018-01-27 12:05:12', NULL, '1', '2018-02-20 19:04:11', '1', 1),
(19, 'Tuscan Pesto Chicken & Bacon', 'Oven roasted chicken, pesto pizza sauce, bacon, button mushrooms, red onion, crushed garlic, Roma tomatoes and mozzarella garnished with pesto aioli, parmesan and fresh herbs.', 1800.00, '2018-01-27 12:12:34', NULL, '1', NULL, '1', 2),
(20, 'Piri Piri Chicken', 'Piri piri sauce, oven roasted chicken, Spanish onion, crushed garlic, red capsicum, cherry tomatoes, mozzarella and African spice garnished with aioli shallots, cashew nuts and lemon wedges.', 1600.00, '2018-01-27 12:14:54', NULL, '1', NULL, '1', 2),
(21, 'Smoked Salmon & Avocado', 'Smoked salmon, ripe avocado, baby spinach, onion, garlic, lemon pepper, pizza sauce and mozzarella garnished with garlic aioli, baby capers, fresh shallots and lemon wedges.', 1900.00, '2018-01-27 12:17:25', NULL, '1', NULL, '1', 2),
(22, 'Pepperoni Pizza', 'Our famous smoked pepperoni, Spanish onion and mozzarella garnished with fresh parsley. ', 1600.00, '2018-01-27 12:18:57', NULL, '1', NULL, '1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `vendor_id` int(10) UNSIGNED NOT NULL,
  `vendor_name` varchar(50) NOT NULL,
  `vendor_address` varchar(255) NOT NULL,
  `vendor_city` enum('Colombo 1','Colombo 2','Colombo 3','Colombo 4','Colombo 5','Colombo 6','Colombo 7','Colombo 8','Colombo 9','Colombo 10','Colombo 11','Colombo 12','Colombo 13','Colombo 14','Colombo 15') NOT NULL,
  `vendor_joindate` datetime NOT NULL,
  `vendor_phone` varchar(13) NOT NULL,
  `vendor_email` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `vendor_isactive` enum('0','1') NOT NULL DEFAULT '1',
  `vendor_dateinactive` datetime DEFAULT NULL,
  `weekday_start` time DEFAULT NULL,
  `weekday_end` time DEFAULT NULL,
  `sat_start` time DEFAULT NULL,
  `sat_end` time DEFAULT NULL,
  `sun_start` time DEFAULT NULL,
  `sun_end` time DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`vendor_id`, `vendor_name`, `vendor_address`, `vendor_city`, `vendor_joindate`, `vendor_phone`, `vendor_email`, `comments`, `vendor_isactive`, `vendor_dateinactive`, `weekday_start`, `weekday_end`, `sat_start`, `sat_end`, `sun_start`, `sun_end`) VALUES
(1, 'Cafe Beverly', '475/C Sri Jayawardanepura Mawatha', 'Colombo 8', '2017-11-26 07:30:00', '(011)2698556', NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'The Kingsbury Pizza', 'Janadhipathi Mawatha', 'Colombo 1', '2017-11-26 07:30:00', '(011)2332332', '', '', '1', NULL, '09:00:00', '23:00:00', '09:00:00', '20:00:00', '09:00:00', '20:00:00'),
(6, 'Chinese Dragon Cafe', '180 Kotte Road', 'Colombo 1', '2018-01-07 00:00:00', '(011)2455555', 'werewerw', '', '0', '2018-01-14 16:59:54', '10:00:00', '22:00:00', '10:00:00', '22:00:00', '10:00:00', '22:00:00'),
(7, 'Chinese Dragon Cafe', '180 Kotte Road', 'Colombo 5', '2018-01-14 00:00:00', '(011)2332332', 'werewerw', '', '0', '2018-01-14 17:09:43', '10:00:00', '22:00:00', '10:00:00', '22:00:00', '10:00:00', '22:00:00'),
(8, 'Chinese Dragon Cafe', '180 Kotte Road', 'Colombo 5', '2018-01-14 00:00:00', '(011)2455555', 'cvbbbb', '', '1', NULL, '10:00:00', '22:00:00', '10:00:00', '22:00:00', '10:00:00', '22:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_login`
--

CREATE TABLE `vendor_login` (
  `vendor_username` varchar(50) NOT NULL,
  `vendor_password` char(41) NOT NULL,
  `vendor_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `vendor_login`
--

INSERT INTO `vendor_login` (`vendor_username`, `vendor_password`, `vendor_id`) VALUES
('Kanchana', '*5354402811D33EABB3BBB0703E609F9D5BCF941A', 1),
('Lahiru', '*2F4A16A516D18D068ED215C9E0A2CA48D1664DB8', 2),
('Dragon46', '*5F88A2D2B9C0268E523458E6AB84F17E011F7D56', 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indexes for table `admin_login`
--
ALTER TABLE `admin_login`
  ADD PRIMARY KEY (`admin_username`),
  ADD UNIQUE KEY `admin_username` (`admin_username`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`);

--
-- Indexes for table `cust_login`
--
ALTER TABLE `cust_login`
  ADD PRIMARY KEY (`cust_username`),
  ADD UNIQUE KEY `cust_username` (`cust_username`),
  ADD KEY `cust_id` (`cust_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `cust_id` (`cust_id`);

--
-- Indexes for table `order_product`
--
ALTER TABLE `order_product`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `vendor_login`
--
ALTER TABLE `vendor_login`
  ADD PRIMARY KEY (`vendor_username`),
  ADD UNIQUE KEY `vendor_username` (`vendor_username`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `vendor_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
