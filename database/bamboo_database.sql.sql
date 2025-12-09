-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Dec 09, 2025 at 05:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bamboo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'Bhavana Bhati', '$2y$10$RSjoZWsslPBK1CMx9W4EteMM6AG8oiVHpmRX8dfeOj7cUxgid2Eua'),
(2, 'Pranali gaikwad', '$2y$10$PohPy3AMwDeqBzn1YdbE8e.TqGnPhzQnRHS0R4nFI0MwtO/6GagzC');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(5, 1, 1, 1, '2025-09-05 14:35:07'),
(6, 1, 5, 1, '2025-09-05 14:35:34'),
(7, 1, 7, 2, '2025-09-05 14:39:41'),
(8, 1, 6, 2, '2025-09-05 14:50:10'),
(9, 1, 10, 1, '2025-09-05 15:13:57'),
(10, 1, 11, 2, '2025-09-05 15:14:18'),
(15, 7, 10, 2, '2025-09-06 10:56:21'),
(17, 8, 10, 1, '2025-09-20 10:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(15) NOT NULL,
  `customer_address` text NOT NULL,
  `customer_city` varchar(50) NOT NULL,
  `customer_pincode` varchar(10) NOT NULL,
  `payment_method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `quantity`, `total_price`, `order_status`, `created_at`, `customer_name`, `customer_phone`, `customer_address`, `customer_city`, `customer_pincode`, `payment_method`) VALUES
(2, 7, 12, 1, 250.00, 'Delivered', '2025-09-06 10:10:50', 'Manish Bhati', '7219757745', 'Theregaon', 'pune', '410033', 'COD'),
(3, 8, 2, 1, 450.00, 'Pending', '2025-09-08 03:34:00', 'Pranali gaikwad', '9822209028', 'sangvi', 'pune', '410033', 'COD'),
(4, 8, 12, 1, 250.00, 'Pending', '2025-09-20 10:38:07', 'Pranali gaikwad', '9822209028', 'sangvi', '', '', 'COD'),
(5, 8, 6, 1, 7800.00, 'Pending', '2025-09-20 11:52:14', 'Pranali gaikwad', '9822209028', 'sangvi', 'pune', '410033', 'COD'),
(6, 8, 5, 1, 3000.00, 'Pending', '2025-09-29 08:31:39', 'Pranali gaikwad', '9822209028', 'sangvi', '', '', 'COD'),
(7, 1, 5, 1, 3000.00, 'Cancelled', '2025-10-02 15:34:08', 'Bhavana Bhati', '9970082256', '16 no Pawar Nagar Theregaon ', 'pune', '410033', 'COD'),
(8, 1, 7, 1, 15000.00, 'Delivered', '2025-10-02 15:40:55', 'Bhavana Bhati', '9970082256', 'Pawar Nagar Theregaon ', 'pune', '410033', 'COD'),
(9, 1, 5, 1, 3000.00, 'Delivered', '2025-10-03 15:51:02', 'Bhavana Bhati', '9970082256', 'theregaon', 'pune', '410033', 'COD'),
(10, 1, 2, 1, 450.00, 'Cancelled', '2025-10-08 09:27:16', 'Bhavana Bhati', '9970082256', 'theregaon', 'pune', '410033', 'COD'),
(11, 1, 2, 1, 450.00, 'Cancelled', '2025-10-08 11:34:44', 'Bhavana Bhati', '9970082256', 'pawar nagar lane no 1 theregaon ', 'pune', '410033', 'COD'),
(12, 1, 1, 1, 750.00, 'Cancelled', '2025-10-09 05:38:58', 'Bhavana Bhati', '9970082256', 'sangvi', 'pune', '410033', 'COD'),
(13, 1, 1, 1, 750.00, 'Pending', '2025-10-10 06:52:12', 'Bhavana Bhati', '9970082256', 'theregaon', 'pune', '410033', 'COD');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `long_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `image`, `category`, `long_description`) VALUES
(1, 'Bamboo Tray', 'Eco-friendly bamboo serving tray.', 750.00, 'images/trey.jpg', 'kitchenware', 'This handcrafted bamboo tray is made by skilled artisans from Assam, India, \r\n   using organically grown bamboo harvested from the North-East region. \r\n   The making process involves curing bamboo in natural sunlight, hand-polishing with oils, \r\n   and weaving with traditional techniques passed down for generations.  \r\n\r\n   • Material: 100% natural bamboo (sun-dried, no chemicals)  \r\n   • Origin: Crafted by artisans in Assam, India  \r\n   • Process: Cut, boiled, polished with mustard oil, and handwoven  \r\n   • Eco-friendly: Biodegradable, renewable, plastic-free  \r\n   • Use: Perfect for serving tea, snacks, and fruits  \r\n\r\n   Each tray represents India’s bamboo heritage while supporting rural artisan livelihoods.'),
(2, 'Bamboo Bowl', 'Eco-friendly bamboo serving bowl.', 450.00, 'images/bowl2.jpg', 'kitchenware', 'This elegant bamboo bowl is hand-carved in Kerala using matured bamboo from the Western Ghats. \r\n   The bamboo is naturally cured and polished with coconut oil for a food-safe finish.  \r\n\r\n   • Material: Matured bamboo, polished with coconut oil  \r\n   • Origin: Handcrafted in Kerala, India  \r\n   • Process: Boiled, sun-dried, hand-carved, polished  \r\n   • Features: Durable, lightweight, unique grain patterns  \r\n   • Use: Ideal for salads, curries, fruits, and snacks  \r\n\r\n   Traditionally, bamboo bowls have been used in South Indian households \r\n   for daily meals, and now they serve as eco-conscious modern kitchenware.'),
(3, 'Bamboo Basket', 'Perfect for storing fruits, vegetables, or household items.', 550.00, 'images/basket.jpg', 'kitchenware', 'This beautifully woven bamboo basket is made by women artisans in Tripura, India, \r\n   using thin bamboo strips that are soaked, sun-dried, and carefully handwoven.  \r\n\r\n   • Material: Sun-dried bamboo strips, natural cotton binding  \r\n   • Origin: Crafted by tribal women in Tripura, India  \r\n   • Process: Handwoven with double-layer reinforcement for strength  \r\n   • Features: Lightweight, durable, eco-friendly  \r\n   • Use: Perfect for fruits, vegetables, bread, or household storage  \r\n\r\n   Each basket supports traditional weaving communities and promotes \r\n   sustainable alternatives to synthetic plastic baskets.'),
(4, 'Bamboo Plates', 'Stylish set of plates made from real bamboo.', 1100.00, 'images/plates.jpg', 'kitchenware', 'These bamboo plates are crafted in Nagaland, India, from thick bamboo sheets. \r\n   They are boiled, pressed, and heat-treated for durability, then polished with mustard oil.  \r\n\r\n   • Material: 100% thick bamboo sheets, naturally treated  \r\n   • Origin: Handcrafted in Nagaland, North-East India  \r\n   • Process: Cut, boiled, sun-dried, pressed, and oil-polished  \r\n   • Features: Strong, reusable, and biodegradable  \r\n   • Use: Ideal for meals, parties, and festive occasions  \r\n\r\n   Inspired by traditional community feasts in Nagaland, \r\n   these plates provide a sustainable replacement for disposable plastic or paper plates.'),
(5, 'Bamboo Lounge Chair', 'Handcrafted bamboo lounge chair with cushion, perfect for relaxation.', 3000.00, 'images/chair.jpg', 'furniture', 'This eco-friendly lounge chair is made from premium bamboo sourced from Assam, India, known for its strong and flexible bamboo varieties. \r\n   The chair comes with a soft cotton cushion, making it ideal for long hours of sitting. \r\n   • Handmade by skilled artisans using traditional weaving methods \r\n   • Natural polish finish enhances durability and resists wear \r\n   • Lightweight yet sturdy, easy to move around \r\n   • Ergonomic curved design supports the back for comfort \r\n   • Dimensions: 32 x 30 x 28 inches \r\n   • 100% biodegradable and eco-friendly \r\n   Perfect for living rooms, balconies, or cozy reading corners.'),
(6, 'Bamboo Dresser', 'Elegant bamboo dresser with storage drawers and mirror.', 7800.00, 'images/dresser.jpg', 'furniture', 'This dresser is crafted from solid bamboo harvested in Kerala, India, where bamboo furniture is traditionally made. \r\n   It features a large mirror with a woven bamboo frame and spacious drawers for everyday storage. \r\n   • Includes 2 deep drawers and 1 wide drawer for cosmetics, clothes, or accessories \r\n   • Traditional bamboo weaving adds a rustic yet modern touch \r\n   • Polished surface with a smooth finish, resistant to moisture \r\n   • Comes with a matching cushioned bamboo stool \r\n   • Dimensions: 45 x 18 x 55 inches \r\n   • Designed for long-lasting durability with sustainable materials \r\n   Ideal for bedrooms, vanity spaces, or boho-style interiors.'),
(7, 'Bamboo Dining Table', 'Eco-friendly bamboo dining table set with four chairs.', 15000.00, 'images/diningtable.jpg', 'furniture', 'This handcrafted dining set is designed from strong bamboo cultivated in Nagaland, India. \r\n   The set includes one rectangular table and four sturdy bamboo chairs with woven seating. \r\n   • Table size: 60 x 36 inches (seats 4 people comfortably) \r\n   • Chairs designed with ergonomic backrest and reinforced joints \r\n   • Smooth polished finish with natural honey shade \r\n   • Resistant to stains and easy to clean with a damp cloth \r\n   • Lightweight yet durable, easy to move \r\n   • Completely handmade, supporting rural artisan communities \r\n   A perfect centerpiece for sustainable dining and eco-conscious homes.'),
(8, 'Bamboo Clothing Rack with Shelves', 'Spacious bamboo clothing rack with hanging rod and side shelves.', 4500.00, 'images/rack.jpg', 'furniture', 'This multifunctional rack is handcrafted from strong bamboo, designed to organize clothes, accessories, and travel essentials in style. \r\n   • Includes a sturdy hanging rod for shirts, coats, and dresses \r\n   • Multiple side shelves for folded clothes, shoes, or storage baskets \r\n   • Bottom compartment for bags, luggage, or extra storage \r\n   • Smooth polished surface for durability and a modern look \r\n   • Lightweight yet strong – easy to move but stable when loaded \r\n   • Dimensions: 68 x 48 x 16 inches (approx.) \r\n   • Eco-friendly and made from 100% sustainable bamboo \r\n   Perfect for bedrooms, guest rooms, or minimalistic urban homes.'),
(9, 'Bamboo Bath Accessories Set', 'Eco-friendly 5-piece bamboo bath set with soap dish, dispenser, toothbrush holder, storage jar, and tray.', 750.00, 'images/bath1.jpg', 'accessories', 'This handcrafted bamboo bath set is designed to bring elegance and sustainability into your daily routine. Made by artisans in Assam, India, it combines natural bamboo with a smooth finish and a durable white accent base. \r\n  • Includes 5 pieces: soap dish, liquid dispenser, toothbrush holder, storage jar, and tray  \r\n  • Bamboo is harvested sustainably from Indian bamboo groves and polished for durability  \r\n  • Naturally resistant to water, mold, and daily wear, making it perfect for humid bathrooms  \r\n  • The smooth surface is achieved by hand-sanding and applying a food-safe coating  \r\n  • Adds a natural, spa-like touch to modern bathrooms  \r\n  • Ingredients: 100% natural bamboo with eco-safe polish, BPA-free pump and base'),
(10, 'Bamboo Makeup Brush', '6-piece bamboo makeup brush set with soft bristles and eco-friendly handles.', 600.00, 'images/makeupbrush.jpg', 'accessories', 'This six-piece makeup brush set is made using eco-conscious bamboo handles and soft, cruelty-free synthetic bristles. Artisans in Kerala, India, hand-assemble each brush with precision, ensuring comfort and durability. \r\n  • Contains brushes for foundation, powder, blush, contour, blending, and eyeshadow  \r\n  • Handles are made from heat-treated bamboo, preventing cracks and moisture damage  \r\n  • Bristles are made from premium vegan fibers, ensuring a silky feel and flawless application  \r\n  • Comes with a natural jute pouch for safe storage and travel  \r\n  • Sustainable alternative to plastic-handled brushes  \r\n  • Ingredients: Bamboo, recyclable aluminum ferrules, and cruelty-free synthetic bristles'),
(11, 'Bamboo Hair Brush & Comb', '4-piece bamboo haircare set with brushes and combs for natural, gentle grooming.', 700.00, 'images/hairbrush.jpg', 'accessories', 'This 4-piece set includes two hairbrushes and two combs, carefully crafted from Indian bamboo for daily grooming. The bamboo pins and teeth are polished by hand to ensure smoothness and reduce friction. \r\n  • Includes: 2 bamboo brushes (large and small) + 2 bamboo combs (wide-tooth and fine-tooth)  \r\n  • Bamboo naturally releases negative ions that help reduce static and frizz in hair  \r\n  • Gentle on scalp, prevents hair breakage, and stimulates natural oils  \r\n  • Designed for all hair types – men, women, and children  \r\n  • Artisans from Nagaland craft these sets with traditional bamboo carpentry skills  \r\n  • Ingredients: 100% hand-polished bamboo, eco-safe finish'),
(12, 'Bamboo Toothbrush', 'Eco-friendly bamboo toothbrush set with 4 biodegradable brushes.', 250.00, 'images/toothbrush.jpg', 'accessories', 'This set of 4 bamboo toothbrushes is designed as an eco-friendly alternative to plastic brushes. The handles are carved from locally grown bamboo in India and coated lightly with natural wax to resist moisture. \r\n  • Each pack includes 4 biodegradable bamboo toothbrushes  \r\n  • Soft, BPA-free nylon bristles ensure gentle cleaning for teeth and gums  \r\n  • Handles are smooth, splinter-free, and compostable after use  \r\n  • Vegan, cruelty-free, and safe for the environment  \r\n  • Packaged in recyclable kraft paper boxes with eco-print ink  \r\n  • Ingredients: 100% bamboo handles, BPA-free bristles, recycled packaging'),
(13, 'Bamboo Brain Teaser Puzzle', 'Eco-friendly bamboo 3D puzzle game with interlocking pieces, perfect for mind challenges and stress relief.', 250.00, 'images/puzzle1.jpg', 'kids product ', '• Made from high-quality bamboo sourced from Assam and Tripura, India  \r\n• Handcrafted and polished by skilled artisans using traditional techniques  \r\n• Safe and smooth finish, ideal for both children and adults  \r\n• Strengthens logic, patience, and problem-solving skills  \r\n• Perfect as a desk accessory, family game, or eco-friendly gift  \r\n• 100% biodegradable, sustainable, and long-lasting  \r\n• Supports rural artisan communities and promotes greener lifestyles'),
(14, 'Bamboo Ballpoint Pen Set ', 'Smooth writing bamboo ballpoint pens with metal finish, set of 5.', 300.00, 'images/pen.jpg', 'kids product', '• Crafted from natural bamboo grown in Kerala and Nagaland  \r\n• Body shaped and polished by local artisans, combined with steel fittings  \r\n• Lightweight and comfortable for long writing sessions  \r\n• Replaces plastic pens with a biodegradable, eco-friendly option  \r\n• Ideal for students, professionals, and corporate gifting  \r\n• Supports bamboo artisans and encourages eco-conscious living  \r\n• Stylish design suitable for office use, events, and conferences'),
(15, 'Bamboo Thermal Bottle', 'Insulated bamboo thermal bottle with stainless steel inner lining, keeps drinks hot/cold for hours.', 1000.00, 'images/bottle.jpg', 'kids product', '• Made using bamboo from the North-Eastern states of India  \r\n• Outer body crafted from natural bamboo, inner made of stainless steel  \r\n• Maintains hot drinks for up to 12 hours, cold drinks up to 18 hours  \r\n• 100% BPA-free, chemical-free, and safe for daily use  \r\n• Perfect for office, travel, gym, or gifting purposes  \r\n• Durable, reusable, and reduces single-use plastic bottle waste  \r\n• Sleek natural design that adds an eco-friendly touch to lifestyle products  \r\n• Supports bamboo farmers and artisans in rural India'),
(16, 'Bamboo Dinosaur Toy Basket', 'Handcrafted dinosaur-shaped bamboo storage basket with wheels, ideal for kids toys and room decor.', 1200.00, 'images/toystore.jpg', 'kids product', '• Handmade in Assam using strong bamboo canes and rattan strips  \r\n• Dinosaur-shaped design loved by kids, doubles as decor and storage  \r\n• Sturdy yet lightweight with smooth weaving finish  \r\n• Comes with wheels and a handle for easy movement  \r\n• Safe, chemical-free, and biodegradable — unlike plastic baskets  \r\n• Large capacity, perfect for toys, books, or nursery essentials  \r\n• Durable and breathable structure keeps stored items fresh  \r\n• Supports traditional bamboo artisans and eco-friendly living practices  \r\n• Promotes Indian craftsmanship and the cultural value of bamboo');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `phone`, `address`, `password`) VALUES
(1, 'Bhavana Bhati', '9970082256', NULL, '$2y$10$wv5tgwi033Uqnauk2eqmVO/Vhm0VydWW0uTRCmXxG6/NuR2wEVI0i'),
(4, 'Manisha Choudhary', '9689530033', 'Aundh new dp road pune', '$2y$10$vr6mKc1vNTsgpYh5MiEB3ORKR65opdLi70GihiMtZ0cHbNWiN53q.'),
(5, 'Sunny', '8421747103', 'Kothrud, Pune', '$2y$10$K5ffjTnf2oSEgi63kHQkCO0r.iWr9K8oVCY06dX1kyv35S3lXijE2'),
(6, 'Preeti', '9075515619', 'Saudagar', '$2y$10$u/F71vKUBZBswJV1uMjLH.oO9ybA7.fTV1ryM8I3L1bVISEj4I2PW'),
(7, 'Manish Bhati', '7219757745', 'Theregaon', '$2y$10$NbJLA.eKy8R5gDtlLiNeC.BhfCIIz3SretGs9qaru3mdwH/X6o1jq'),
(8, 'Pranali gaikwad', '9822209028', 'sangvi', '$2y$10$c1P787mD8ZFd47m0lXG4LeZ/jg.eGW8wriNkP6j8XsgHE2V2o89ZC');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `product_id`, `added_at`) VALUES
(22, 7, 9, '2025-09-06 10:44:45'),
(23, 8, 3, '2025-09-08 03:33:17'),
(24, 8, 1, '2025-09-20 10:37:22'),
(25, 8, 11, '2025-09-20 10:37:26'),
(26, 1, 11, '2025-10-08 11:39:16'),
(27, 1, 10, '2025-10-08 11:39:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
