-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 19, 2025 at 02:53 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login_register_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_activity`
--

CREATE TABLE `tb_activity` (
  `id_activity` int NOT NULL,
  `id_user` int NOT NULL,
  `activity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail`
--

CREATE TABLE `tb_detail` (
  `id_detail` int NOT NULL,
  `id_transaction` int NOT NULL,
  `id_product` int NOT NULL,
  `amount` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_detail`
--

INSERT INTO `tb_detail` (`id_detail`, `id_transaction`, `id_product`, `amount`) VALUES
(18, 23, 31, 2),
(20, 23, 32, 2),
(21, 32, 63, 1),
(23, 24, 32, 1),
(25, 34, 31, 1);

--
-- Triggers `tb_detail`
--
DELIMITER $$
CREATE TRIGGER `trg_reduce_stock` AFTER INSERT ON `tb_detail` FOR EACH ROW BEGIN
  IF (SELECT stock FROM tb_produk WHERE id = NEW.id_product) >= NEW.amount THEN
    UPDATE tb_produk
    SET stock = stock - NEW.amount
    WHERE id = NEW.id_product;
  ELSE
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Stok tidak cukup untuk transaksi ini';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Dress'),
(2, 'Kebaya'),
(3, 'Heels');

-- --------------------------------------------------------

--
-- Table structure for table `tb_produk`
--

CREATE TABLE `tb_produk` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int NOT NULL,
  `stock` int NOT NULL,
  `photo` text NOT NULL,
  `description` text NOT NULL,
  `id_kategori` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_produk`
--

INSERT INTO `tb_produk` (`id`, `name`, `price`, `stock`, `photo`, `description`, `id_kategori`) VALUES
(31, 'blue bloom dress', 200000, 0, 'd1.jpg', 'look sweet with a floral dress that exudes feminine charm.', 1),
(32, 'pink berry dress', 200000, 0, 'd8.jpg', 'a touch of soft pink for an effortless elegant impression', 1),
(33, 'garden breeze dress', 200000, 1, 'd6.jpg', 'a modern touch that exudes natural elegance', 1),
(34, 'red vintage dress', 200000, 1, 'd12.jpg', 'a captivating retro touch in a classic red dress', 1),
(35, 'blue short-sleeved kebaya', 150000, 1, '1-new.jpg', 'elegant and graceful, suitable for teenagers', 2),
(36, 'burgundy short-sleeved kebaya', 150000, 1, '2-new.jpg', 'traditional touches with modern details. ideal for proposals', 2),
(37, 'grey short-sleeved kebaya', 150000, 1, 'k20.jpg', 'the delicate embroidery motif blends with the soft material', 2),
(38, 'heels', 80000, 5, 'h1.jpg', 'elegant', 3),
(39, 'heels', 85000, 5, 'h3.jpg', 'cute', 3),
(41, 'pink short-sleeved kebaya', 150000, 1, '4-new.jpg', 'simple yet captivating design. perfect for outdoor events\r\n', 2),
(42, 'burgundy long-sleeved kebaya', 150000, 1, 'k1.jpg', 'bring elegance to every step with this special kebaya', 2),
(43, 'purple long-sleeved kebaya', 150000, 1, 'k2.jpg', 'a fashionable kebaya that still maintains a traditional feel', 2),
(44, 'maroon long-sleeved kebaya', 150000, 1, 'k3.jpg', 'a timeless classic design, perfect for you', 2),
(45, 'black long-sleeved kebaya', 150000, 1, 'k4.jpg', 'modern kebaya with charming traditional details', 2),
(46, 'black long-sleeved kebaya', 150000, 1, 'k5.jpg', 'experience the luxury of quality fabrics with charming cuts.', 2),
(47, 'blue long-sleeved kebaya', 150000, 1, 'k6.jpg', 'beautiful kebaya to appear confident at important moments', 2),
(48, 'white long-sleeved kebaya', 150000, 1, 'k7.jpg', 'an elegant kebaya that exudes the charm of Indonesian culture', 2),
(49, 'green long-sleeved kebaya', 150000, 1, 'k9.jpg', 'the right choice to look glamorous and remain elegant', 2),
(50, 'maroon long-sleeved kebaya', 150000, 1, 'k10.jpg', 'stylish kebaya with a touch of luxury in every detail', 2),
(51, 'pink long-sleeved kebaya', 150000, 1, 'k12.jpg', 'the charm of the kebaya that makes every eye turn to you', 2),
(52, 'maroon long-sleeved kebaya', 150000, 1, 'k14.jpg', 'exclusive design that makes you look different', 2),
(53, 'pink long-sleeved kebaya', 150000, 1, 'k15.jpg', 'premium kebaya for impressive events', 2),
(54, 'green short-sleeved kebaya', 150000, 1, 'k22.jpg', 'a touch of elegant color that captivates the heart', 2),
(55, 'burgundy long-sleeved kebaya', 150000, 1, 'k18.jpg', 'a timeless kebaya that is always suitable for any occasion', 2),
(56, 'navy long-sleeved kebaya', 150000, 1, 'k32.jpg', 'special kebaya for unforgettable moments', 2),
(57, 'moss green long-sleeved kebaya', 150000, 1, 'k31.jpg', 'combine elegance and comfort in one kebaya', 2),
(58, 'burgundy short-sleeved kebaya', 150000, 1, 'k24.jpg', 'charming kebaya with beautiful embroidery details', 2),
(59, 'burgundy long-sleeved kebaya', 150000, 1, 'k25.jpg', 'the best choice to look stunning', 2),
(60, 'burgundy long-sleeved kebaya', 150000, 1, 'k28.jpg', 'classic kebaya with a touch of luxury', 2),
(61, 'burgundy long-sleeved kebaya', 150000, 1, 'k27.jpg', 'the charm of premium fabric with elegant design', 2),
(62, 'rosé elegance dress', 200000, 1, 'd3.jpg', 'the softness of the colors and motifs makes the appearance even more charming.', 1),
(63, 'petal grow dress', 250000, 1, 'd4.jpg', 'Timeless design with a soft touch', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaction` int NOT NULL,
  `id_user` int NOT NULL,
  `date` datetime NOT NULL,
  `total_price` int NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaction`, `id_user`, `date`, `total_price`, `payment_method`) VALUES
(23, 2, '2025-09-09 03:59:21', 810000, 'E-Wallet'),
(24, 1, '2025-09-09 05:17:45', 10000, 'Bank Transfer'),
(32, 2, '2025-09-12 01:40:22', 260000, 'Bank Transfer'),
(34, 2, '2025-09-17 01:34:07', 210000, 'Bank Transfer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_status` enum('pending','confirmed','reset') DEFAULT 'pending',
  `role` enum('user','admin') NOT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(255) NOT NULL,
  `reset_request` tinyint(1) DEFAULT '0',
  `new_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `phone`, `email`, `password`, `password_status`, `role`, `profile_picture`, `created_at`, `address`, `reset_request`, `new_password`) VALUES
(1, 'gifaattire', 'admin', '0882000134734', 'gifaattire@gmail.com', 'admin', 'pending', 'admin', NULL, '2025-08-22 07:49:13', '', 0, NULL),
(2, 'adinda ', 'adindaa', '0895398505257', 'a@gmail.com', 's4CMYFPR', 'reset', 'user', '1755950467_i.jpg', '2025-08-22 07:49:13', '', 0, 's4CMYFPR'),
(18, 'adinda meilia ', 'meii', '0882000134734', 'adindaameiliaa09@gmail.com', 'QpCgBcE8', 'reset', 'user', NULL, '2025-09-15 08:40:03', 'bukepin', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_activity`
--
ALTER TABLE `tb_activity`
  ADD PRIMARY KEY (`id_activity`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaction`),
  ADD KEY `id_produk` (`id_product`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produk_kategori` (`id_kategori`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaction`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_activity`
--
ALTER TABLE `tb_activity`
  MODIFY `id_activity` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_detail`
--
ALTER TABLE `tb_detail`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaction` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_activity`
--
ALTER TABLE `tb_activity`
  ADD CONSTRAINT `tb_activity_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD CONSTRAINT `tb_detail_ibfk_1` FOREIGN KEY (`id_transaction`) REFERENCES `tb_transaksi` (`id_transaction`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_detail_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `tb_produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `tb_kategori` (`id_kategori`);

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
