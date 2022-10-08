CREATE TABLE `secret` (
  `hash` varchar(64) NOT NULL,
  `secretText` text NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `expiresAt` datetime NOT NULL,
  `remainingViews` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `secret`
  ADD PRIMARY KEY (`hash`);
COMMIT;