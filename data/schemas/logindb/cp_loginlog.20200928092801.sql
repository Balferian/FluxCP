ALTER TABLE `cp_loginlog`
ADD COLUMN `user_id` int(10) DEFAULT NULL AFTER `id`,
MODIFY `username` varchar(255) DEFAULT NULL,
MODIFY `password` varchar(255) DEFAULT NULL;