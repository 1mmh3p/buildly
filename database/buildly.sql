-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 10 Kas 2025, 22:51:11
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `buildly`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `calculations`
--

CREATE TABLE `calculations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `area` decimal(10,2) NOT NULL,
  `building_type` varchar(50) NOT NULL,
  `floors` int(11) NOT NULL,
  `basement` enum('yes','no') DEFAULT 'no',
  `garage` enum('yes','no') DEFAULT 'no',
  `province_id` int(11) DEFAULT NULL,
  `material_quality` varchar(20) NOT NULL,
  `finishing_type` varchar(50) DEFAULT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `province_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tablo döküm verisi `companies`
--

INSERT INTO `companies` (`id`, `name`, `province_id`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'الشركة السورية للبناء', 1, '+963111111111', 'info@syriabuild.com', 'دمشق - المزة', '2025-11-10 21:01:19'),
(2, 'أبواب دمشق للمقاولات', 1, '+963111111112', 'info@abwabdamascus.com', 'دمشق - برزة', '2025-11-10 21:01:19'),
(3, 'المقاولون ريف دمشق', 2, '+963112222221', 'info@ruralbuild.com', 'ريف دمشق - جرمانا', '2025-11-10 21:01:19'),
(4, 'العمران ريف دمشق', 2, '+963112222222', 'info@omranrural.com', 'ريف دمشق - الكسوة', '2025-11-10 21:01:19'),
(5, 'الشهباء للبناء', 3, '+963113333331', 'info@shahba-build.com', 'حلب - العزيزية', '2025-11-10 21:01:19'),
(6, 'أفق الشمال للمقاولات', 3, '+963113333332', 'info@northofok.com', 'حلب - السليمانية', '2025-11-10 21:01:19'),
(7, 'العمران حمص', 4, '+963114444441', 'info@omranhoms.com', 'حمص - الحمراء', '2025-11-10 21:01:19'),
(8, 'شركة الاتحاد', 4, '+963114444442', 'info@ittehad-homs.com', 'حمص - باب هود', '2025-11-10 21:01:19'),
(9, 'النهضة للبناء', 5, '+963115555551', 'info@nahda-hama.com', 'حماة - القصور', '2025-11-10 21:01:19'),
(10, 'أبراج حماة', 5, '+963115555552', 'info@abrajhama.com', 'حماة - طريق سلمية', '2025-11-10 21:01:19'),
(11, 'البحر للمقاولات', 6, '+963116666661', 'info@baharlattakia.com', 'اللاذقية - المشروع الثاني', '2025-11-10 21:01:19'),
(12, 'روابي الساحل', 6, '+963116666662', 'info@rawabisahil.com', 'اللاذقية - الكورنيش', '2025-11-10 21:01:19'),
(13, 'طرطوس للبناء', 7, '+963117777771', 'info@tartousbuild.com', 'طرطوس - الرادار', '2025-11-10 21:01:19'),
(14, 'الساحل الحديث', 7, '+963117777772', 'info@moderncoast.com', 'طرطوس - الكورنيش', '2025-11-10 21:01:19'),
(15, 'إعمار إدلب', 8, '+963118888881', 'info@edlebbuild.com', 'إدلب - المدينة', '2025-11-10 21:01:19'),
(16, 'إدلب الحديثة', 8, '+963118888882', 'info@modernidlib.com', 'إدلب - طريق بنش', '2025-11-10 21:01:19'),
(17, 'درعا للإنشاءات', 9, '+963119999991', 'info@daraaconst.com', 'درعا - المدينة', '2025-11-10 21:01:19'),
(18, 'البناء الحديث درعا', 9, '+963119999992', 'info@modernbuild.com', 'درعا - الصناعة', '2025-11-10 21:01:19'),
(19, 'الجبل للمقاولات', 10, '+963120000001', 'info@jabalco.com', 'السويداء - القنوات', '2025-11-10 21:01:19'),
(20, 'العمران السويداء', 10, '+963120000002', 'info@omran-sweida.com', 'السويداء - المدينة', '2025-11-10 21:01:19'),
(21, 'دير الزور للبناء', 11, '+963121111111', 'info@deirezzorbuild.com', 'دير الزور - الجورة', '2025-11-10 21:01:19'),
(22, 'الشرق للإنشاءات', 11, '+963121111112', 'info@sharqdz.com', 'دير الزور - الحميدية', '2025-11-10 21:01:19'),
(23, 'الحسكة للبناء', 12, '+963122222221', 'info@hasakahbuild.com', 'الحسكة - النشوة', '2025-11-10 21:01:19'),
(24, 'الرافدين للمقاولات', 12, '+963122222222', 'info@rafidainhasakah.com', 'الحسكة - الكورنيش', '2025-11-10 21:01:19'),
(25, 'الرقة للمقاولات', 13, '+963123333331', 'info@raqacontract.com', 'الرقة - الصناعة', '2025-11-10 21:01:19'),
(26, 'الفرات للبناء', 13, '+963123333332', 'info@furatbuild.com', 'الرقة - المشلب', '2025-11-10 21:01:19'),
(27, 'القنيطرة للإنشاءات', 14, '+963124444441', 'info@quneitrabuild.com', 'القنيطرة - المدينة', '2025-11-10 21:01:19'),
(28, 'الجولان للمقاولات', 14, '+963124444442', 'info@golanconst.com', 'القنيطرة - خان أرنبة', '2025-11-10 21:01:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tablo döküm verisi `provinces`
--

INSERT INTO `provinces` (`id`, `name`, `created_at`) VALUES
(1, 'دمشق', '2025-11-10 21:01:19'),
(2, 'ريف دمشق', '2025-11-10 21:01:19'),
(3, 'حلب', '2025-11-10 21:01:19'),
(4, 'حمص', '2025-11-10 21:01:19'),
(5, 'حماة', '2025-11-10 21:01:19'),
(6, 'اللاذقية', '2025-11-10 21:01:19'),
(7, 'طرطوس', '2025-11-10 21:01:19'),
(8, 'إدلب', '2025-11-10 21:01:19'),
(9, 'درعا', '2025-11-10 21:01:19'),
(10, 'السويداء', '2025-11-10 21:01:19'),
(11, 'دير الزور', '2025-11-10 21:01:19'),
(12, 'الحسكة', '2025-11-10 21:01:19'),
(13, 'الرقة', '2025-11-10 21:01:19'),
(14, 'القنيطرة', '2025-11-10 21:01:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `province_materials`
--

CREATE TABLE `province_materials` (
  `id` int(11) NOT NULL,
  `province_id` int(11) DEFAULT NULL,
  `material_name` varchar(100) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `last_updated` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tablo döküm verisi `province_materials`
--

INSERT INTO `province_materials` (`id`, `province_id`, `material_name`, `unit`, `price`, `last_updated`) VALUES
(1, 1, 'إسمنت أسود', 'طن', 115.00, '2025-11-11'),
(2, 1, 'إسمنت أبيض', 'طن', 125.00, '2025-11-11'),
(3, 1, 'حديد', 'طن', 680.00, '2025-11-11'),
(4, 1, 'رمل', 'م³', 15.20, '2025-11-11'),
(5, 1, 'بلوك 20 سم', 'قطعة', 0.39, '2025-11-11'),
(6, 2, 'إسمنت', 'طن', 116.00, '2025-11-11'),
(7, 2, 'حديد', 'طن', 670.00, '2025-11-11'),
(8, 2, 'رمل', 'م³', 15.50, '2025-11-11'),
(9, 2, 'بلوك 20 سم', 'قطعة', 0.40, '2025-11-11'),
(10, 3, 'إسمنت', 'طن', 120.00, '2025-11-11'),
(11, 3, 'حديد', 'طن', 685.00, '2025-11-11'),
(12, 3, 'رمل', 'م³', 14.80, '2025-11-11'),
(13, 3, 'بلوك 20 سم', 'قطعة', 0.42, '2025-11-11'),
(14, 4, 'إسمنت', 'طن', 118.00, '2025-11-11'),
(15, 4, 'حديد', 'طن', 670.00, '2025-11-11'),
(16, 4, 'رمل', 'م³', 15.00, '2025-11-11'),
(17, 4, 'بلوك 20 سم', 'قطعة', 0.41, '2025-11-11'),
(18, 5, 'إسمنت', 'طن', 117.00, '2025-11-11'),
(19, 5, 'حديد', 'طن', 665.00, '2025-11-11'),
(20, 5, 'رمل', 'م³', 15.50, '2025-11-11'),
(21, 5, 'بلوك 20 سم', 'قطعة', 0.41, '2025-11-11'),
(22, 6, 'إسمنت', 'طن', 122.00, '2025-11-11'),
(23, 6, 'حديد', 'طن', 690.00, '2025-11-11'),
(24, 6, 'رمل', 'م³', 16.20, '2025-11-11'),
(25, 6, 'بلوك 20 سم', 'قطعة', 0.44, '2025-11-11'),
(26, 7, 'إسمنت', 'طن', 120.00, '2025-11-11'),
(27, 7, 'حديد', 'طن', 700.00, '2025-11-11'),
(28, 7, 'رمل', 'م³', 16.50, '2025-11-11'),
(29, 7, 'بلوك 20 سم', 'قطعة', 0.45, '2025-11-11'),
(30, 8, 'إسمنت', 'طن', 119.50, '2025-11-11'),
(31, 8, 'حديد', 'طن', 675.00, '2025-11-11'),
(32, 8, 'رمل', 'م³', 15.80, '2025-11-11'),
(33, 8, 'بلوك 20 سم', 'قطعة', 0.43, '2025-11-11'),
(34, 9, 'إسمنت', 'طن', 115.00, '2025-11-11'),
(35, 9, 'حديد', 'طن', 655.00, '2025-11-11'),
(36, 9, 'رمل', 'م³', 16.88, '2025-11-11'),
(37, 9, 'بلوك 20 سم', 'قطعة', 0.48, '2025-11-11'),
(38, 10, 'إسمنت', 'طن', 118.50, '2025-11-11'),
(39, 10, 'حديد', 'طن', 675.00, '2025-11-11'),
(40, 10, 'رمل', 'م³', 15.80, '2025-11-11'),
(41, 10, 'بلوك 20 سم', 'قطعة', 0.43, '2025-11-11'),
(42, 11, 'إسمنت', 'طن', 119.00, '2025-11-11'),
(43, 11, 'حديد', 'طن', 690.00, '2025-11-11'),
(44, 11, 'رمل', 'م³', 18.00, '2025-11-11'),
(45, 11, 'بلوك 20 سم', 'قطعة', 0.46, '2025-11-11'),
(46, 12, 'إسمنت', 'طن', 121.00, '2025-11-11'),
(47, 12, 'حديد', 'طن', 710.00, '2025-11-11'),
(48, 12, 'رمل', 'م³', 17.50, '2025-11-11'),
(49, 12, 'بلوك 20 سم', 'قطعة', 0.47, '2025-11-11'),
(50, 13, 'إسمنت', 'طن', 118.00, '2025-11-11'),
(51, 13, 'حديد', 'طن', 695.00, '2025-11-11'),
(52, 13, 'رمل', 'م³', 16.50, '2025-11-11'),
(53, 13, 'بلوك 20 سم', 'قطعة', 0.44, '2025-11-11'),
(54, 14, 'إسمنت', 'طن', 116.00, '2025-11-11'),
(55, 14, 'حديد', 'طن', 665.00, '2025-11-11'),
(56, 14, 'رمل', 'م³', 15.80, '2025-11-11'),
(57, 14, 'بلوك 20 سم', 'قطعة', 0.40, '2025-11-11');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@buildly.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-11-10 21:01:19');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `calculations`
--
ALTER TABLE `calculations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `province_id` (`province_id`);

--
-- Tablo için indeksler `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `province_id` (`province_id`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `province_materials`
--
ALTER TABLE `province_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `province_id` (`province_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `calculations`
--
ALTER TABLE `calculations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tablo için AUTO_INCREMENT değeri `province_materials`
--
ALTER TABLE `province_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `calculations`
--
ALTER TABLE `calculations`
  ADD CONSTRAINT `calculations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `calculations_ibfk_2` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

--
-- Tablo kısıtlamaları `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

--
-- Tablo kısıtlamaları `province_materials`
--
ALTER TABLE `province_materials`
  ADD CONSTRAINT `province_materials_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
