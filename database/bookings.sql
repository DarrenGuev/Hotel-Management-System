-- Bookings table for TravelMates Hotel Management System

CREATE TABLE `bookings` (
  `bookingID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `roomID` int(11) NOT NULL,
  `fullName` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phoneNumber` varchar(15) NOT NULL,
  `checkInDate` date NOT NULL,
  `checkOutDate` date NOT NULL,
  `numberOfGuests` int(11) NOT NULL,
  `totalPrice` decimal(10,2) NOT NULL,
  `paymentMethod` enum('cash','gcash','credit_card','debit_card') NOT NULL DEFAULT 'cash',
  `paymentStatus` enum('pending','paid','refunded') NOT NULL DEFAULT 'pending',
  `bookingStatus` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`bookingID`),
  FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  FOREIGN KEY (`roomID`) REFERENCES `rooms` (`roomID`) ON DELETE CASCADE
);
