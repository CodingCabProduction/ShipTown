/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_id`,`subject_type`),
  KEY `causer` (`causer_id`,`causer_type`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configurations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `database_version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.0.0',
  `ecommerce_connected` tinyint(1) NOT NULL DEFAULT '0',
  `disable_2fa` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_collection_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_collection_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_collection_id` bigint unsigned NOT NULL,
  `inventory_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `warehouse_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `is_processed` tinyint(1) DEFAULT NULL,
  `total_transferred_in` double NOT NULL DEFAULT '0',
  `total_transferred_out` double NOT NULL DEFAULT '0',
  `quantity_requested` decimal(20,2) DEFAULT NULL,
  `quantity_scanned` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_to_scan` decimal(20,2) GENERATED ALWAYS AS (greatest(0,(((ifnull(`quantity_requested`,0) - ifnull(`total_transferred_out`,0)) - ifnull(`total_transferred_in`,0)) - ifnull(`quantity_scanned`,0)))) STORED COMMENT 'GREATEST(0, IFNULL(quantity_requested, 0) - IFNULL(total_transferred_out, 0) - IFNULL(total_transferred_in, 0) - IFNULL(quantity_scanned, 0))',
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `unit_sold_price` decimal(10,2) DEFAULT NULL,
  `unit_discount` double GENERATED ALWAYS AS ((`unit_full_price` - `unit_sold_price`)) STORED COMMENT 'unit_full_price - unit_sold_price',
  `unit_full_price` decimal(10,2) DEFAULT NULL,
  `price_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_source_id` bigint unsigned DEFAULT NULL,
  `total_discount` double GENERATED ALWAYS AS ((`quantity_scanned` * (`unit_full_price` - `unit_sold_price`))) STORED COMMENT 'quantity_scanned * (unit_full_price - unit_sold_price)',
  `total_price` decimal(20,2) GENERATED ALWAYS AS ((`quantity_scanned` * `unit_sold_price`)) STORED COMMENT 'quantity_scanned * unit_price',
  `is_requested` tinyint(1) GENERATED ALWAYS AS ((ifnull(`quantity_requested`,0) = 0)) STORED COMMENT 'IFNULL(data_collection_records.quantity_requested, 0) = 0',
  `is_fully_scanned` tinyint(1) GENERATED ALWAYS AS ((`quantity_to_scan` <= 0)) STORED COMMENT 'quantity_to_scan <= 0',
  `is_over_scanned` tinyint(1) GENERATED ALWAYS AS ((ifnull(`quantity_scanned`,0) > ifnull(`quantity_requested`,0))) STORED COMMENT 'IFNULL(data_collection_records.quantity_scanned, 0) > IFNULL(data_collection_records.quantity_requested, 0)',
  `custom_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_collection_records_custom_uuid_unique` (`custom_uuid`),
  KEY `data_collection_records_data_collection_id_foreign` (`data_collection_id`),
  KEY `data_collection_records_product_id_foreign` (`product_id`),
  KEY `data_collection_records_is_requested_index` (`is_requested`),
  KEY `data_collection_records_is_fully_scanned_index` (`is_fully_scanned`),
  KEY `data_collection_records_is_over_scanned_index` (`is_over_scanned`),
  KEY `data_collection_records_warehouse_code_foreign` (`warehouse_code`),
  KEY `data_collection_records_total_price_index` (`total_price`),
  CONSTRAINT `data_collection_records_data_collection_id_foreign` FOREIGN KEY (`data_collection_id`) REFERENCES `data_collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `data_collection_records_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `data_collection_records_warehouse_code_foreign` FOREIGN KEY (`warehouse_code`) REFERENCES `warehouses` (`code`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_collections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `destination_collection_id` bigint unsigned DEFAULT NULL,
  `destination_warehouse_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currently_running_task` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_collections_custom_uuid_unique` (`custom_uuid`),
  KEY `data_collections_warehouse_id_foreign` (`warehouse_id`),
  KEY `data_collections_destination_collection_id_foreign` (`destination_collection_id`),
  KEY `data_collections_destination_warehouse_id_foreign` (`destination_warehouse_id`),
  CONSTRAINT `data_collections_destination_collection_id_foreign` FOREIGN KEY (`destination_collection_id`) REFERENCES `data_collections` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `data_collections_destination_warehouse_id_foreign` FOREIGN KEY (`destination_warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `data_collections_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_collections_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_collections_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_collection_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `data_collections_comments_data_collection_id_foreign` (`data_collection_id`),
  KEY `data_collections_comments_user_id_foreign` (`user_id`),
  CONSTRAINT `data_collections_comments_data_collection_id_foreign` FOREIGN KEY (`data_collection_id`) REFERENCES `data_collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `data_collections_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `heartbeats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `heartbeats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'error',
  `error_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_heal_job_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `heartbeats_code_unique` (`code`),
  KEY `heartbeats_level_index` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `warehouse_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shelve_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `recalculated_at` timestamp NULL DEFAULT NULL,
  `recount_required` tinyint(1) NOT NULL DEFAULT '0',
  `quantity_available` decimal(20,2) GENERATED ALWAYS AS ((`quantity` - `quantity_reserved`)) STORED COMMENT 'quantity - quantity_reserved',
  `quantity` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `total_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `is_in_stock` tinyint(1) GENERATED ALWAYS AS ((`quantity_available` > 0)) STORED COMMENT 'quantity_available > 0',
  `quantity_reserved` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_incoming` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_required` decimal(20,2) GENERATED ALWAYS AS ((case when (((`quantity` - `quantity_reserved`) + `quantity_incoming`) between 0 and `reorder_point`) then (`restock_level` - ((`quantity` - `quantity_reserved`) + `quantity_incoming`)) else 0 end)) STORED COMMENT 'CASE WHEN (quantity - quantity_reserved + quantity_incoming) BETWEEN 0 AND reorder_point THEN restock_level - (quantity - quantity_reserved + quantity_incoming)ELSE 0 END',
  `reorder_point` decimal(20,2) NOT NULL DEFAULT '0.00',
  `restock_level` decimal(20,2) NOT NULL DEFAULT '0.00',
  `last_sequence_number` bigint unsigned DEFAULT NULL,
  `first_movement_at` timestamp NULL DEFAULT NULL,
  `last_movement_at` datetime DEFAULT NULL,
  `first_received_at` datetime DEFAULT NULL,
  `last_received_at` datetime DEFAULT NULL,
  `first_sold_at` datetime DEFAULT NULL,
  `last_sold_at` datetime DEFAULT NULL,
  `last_counted_at` timestamp NULL DEFAULT NULL,
  `in_stock_since` timestamp NULL DEFAULT NULL,
  `first_counted_at` datetime DEFAULT NULL,
  `last_movement_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_last_sequence_number desc_index` (`last_sequence_number` DESC),
  KEY `inventory_last_movement_at desc_index` (`last_movement_at` DESC),
  KEY `inventory_first_received_at desc_index` (`first_received_at` DESC),
  KEY `inventory_last_received_at desc_index` (`last_received_at` DESC),
  KEY `inventory_first_sold_at desc_index` (`first_sold_at` DESC),
  KEY `inventory_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_product_id_index` (`product_id`),
  KEY `inventory_warehouse_code_index` (`warehouse_code`),
  KEY `inventory_shelve_location_index` (`shelve_location`),
  KEY `inventory_recalculated_at_index` (`recalculated_at`),
  KEY `inventory_recount_required_index` (`recount_required`),
  KEY `inventory_quantity_available_index` (`quantity_available`),
  KEY `inventory_quantity_index` (`quantity`),
  KEY `inventory_is_in_stock_index` (`is_in_stock`),
  KEY `inventory_quantity_reserved_index` (`quantity_reserved`),
  KEY `inventory_quantity_incoming_index` (`quantity_incoming`),
  KEY `inventory_quantity_required_index` (`quantity_required`),
  KEY `inventory_reorder_point_index` (`reorder_point`),
  KEY `inventory_restock_level_index` (`restock_level`),
  KEY `inventory_last_sold_at_index` (`last_sold_at`),
  KEY `inventory_last_counted_at_index` (`last_counted_at`),
  KEY `inventory_in_stock_since_index` (`in_stock_since`),
  KEY `inventory_first_counted_at_index` (`first_counted_at`),
  CONSTRAINT `inventory_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recount_required` tinyint(1) NOT NULL DEFAULT '1',
  `total_quantity_in_stock` decimal(8,2) NOT NULL,
  `total_quantity_reserved` decimal(8,2) NOT NULL,
  `total_quantity_available` decimal(8,2) NOT NULL,
  `total_quantity_incoming` decimal(8,2) NOT NULL,
  `total_quantity_required` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_groups_product_id_foreign` (`product_id`),
  CONSTRAINT `inventory_groups_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `occurred_at` datetime NOT NULL,
  `sequence_number` int unsigned DEFAULT NULL COMMENT 'row_number() over (partition by inventory_id order by occurred_at asc, id asc)',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_unique_reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inventory_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `quantity_delta` decimal(20,2) NOT NULL,
  `quantity_before` decimal(20,2) NOT NULL,
  `quantity_after` decimal(20,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_movements_inventory_id_sequence_number_unique` (`inventory_id`,`sequence_number`),
  UNIQUE KEY `inventory_movements_custom_unique_reference_id_unique` (`custom_unique_reference_id`),
  KEY `inventory_movements_occurred_at_id_index` (`occurred_at`,`id`),
  KEY `inventory_movements_inventory_id_occurred_at_index` (`inventory_id`,`occurred_at`),
  KEY `inventory_movements_product_id_foreign` (`product_id`),
  KEY `inventory_movements_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_movements_user_id_foreign` (`user_id`),
  KEY `inventory_movements_warehouse_code_index` (`warehouse_code`),
  KEY `inventory_movements_occurred_at_index` (`occurred_at`),
  KEY `inventory_movements_sequence_number_index` (`sequence_number`),
  KEY `inventory_movements_type_index` (`type`),
  KEY `occurred_at_sequence_number_index` (`occurred_at` DESC,`sequence_number` DESC),
  CONSTRAINT `inventory_movements_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_warehouse_code_foreign` FOREIGN KEY (`warehouse_code`) REFERENCES `warehouses` (`code`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_movements_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_movements_statistics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inventory_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `warehouse_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last7days_quantity_delta` decimal(13,2) NOT NULL DEFAULT '0.00',
  `last14days_quantity_delta` decimal(13,2) NOT NULL DEFAULT '0.00',
  `last28days_quantity_delta` decimal(13,2) NOT NULL DEFAULT '0.00',
  `last7days_min_movement_id` bigint unsigned DEFAULT NULL,
  `last7days_max_movement_id` bigint unsigned DEFAULT NULL,
  `last14days_min_movement_id` bigint unsigned DEFAULT NULL,
  `last14days_max_movement_id` bigint unsigned DEFAULT NULL,
  `last28days_min_movement_id` bigint unsigned DEFAULT NULL,
  `last28days_max_movement_id` bigint unsigned DEFAULT NULL,
  `last_sold_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_movements_statistics_type_inventory_id_unique` (`type`,`inventory_id`),
  KEY `inventory_movements_statistics_inventory_id_foreign` (`inventory_id`),
  KEY `inventory_movements_statistics_product_id_index` (`product_id`),
  KEY `inventory_movements_statistics_warehouse_code_index` (`warehouse_code`),
  KEY `inventory_movements_statistics_last_sold_at_index` (`last_sold_at`),
  CONSTRAINT `inventory_movements_statistics_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_statistics_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_statistics_warehouse_code_foreign` FOREIGN KEY (`warehouse_code`) REFERENCES `warehouses` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_movements_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_movements_temp` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_first_movement` tinyint(1) DEFAULT NULL,
  `inventory_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `warehouse_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_delta` decimal(20,2) NOT NULL,
  `quantity_before` decimal(20,2) NOT NULL,
  `quantity_after` decimal(20,2) NOT NULL,
  `description` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_unique_reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_movement_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_movements_temp_custom_unique_reference_id_unique` (`custom_unique_reference_id`),
  UNIQUE KEY `inventory_movements_temp_previous_movement_id_unique` (`previous_movement_id`),
  KEY `inventory_movements_temp_type_index` (`type`),
  KEY `inventory_movements_temp_is_first_movement_index` (`is_first_movement`),
  KEY `inventory_movements_temp_inventory_id_foreign` (`inventory_id`),
  KEY `inventory_movements_temp_product_id_foreign` (`product_id`),
  KEY `inventory_movements_temp_warehouse_code_foreign` (`warehouse_code`),
  KEY `inventory_movements_temp_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_movements_temp_user_id_foreign` (`user_id`),
  CONSTRAINT `inventory_movements_temp_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_movements_temp_previous_movement_id_foreign` FOREIGN KEY (`previous_movement_id`) REFERENCES `inventory_movements` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_movements_temp_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_movements_temp_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_movements_temp_warehouse_code_foreign` FOREIGN KEY (`warehouse_code`) REFERENCES `warehouses` (`code`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_movements_temp_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventory_id` bigint unsigned NOT NULL,
  `product_sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_reserved` decimal(20,2) NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `custom_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_reservations_custom_uuid_unique` (`custom_uuid`),
  KEY `inventory_reservations_warehouse_code_foreign` (`warehouse_code`),
  KEY `inventory_reservations_product_sku_foreign` (`product_sku`),
  KEY `inventory_reservations_inventory_id_foreign` (`inventory_id`),
  CONSTRAINT `inventory_reservations_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `inventory_reservations_product_sku_foreign` FOREIGN KEY (`product_sku`) REFERENCES `products` (`sku`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_reservations_warehouse_code_foreign` FOREIGN KEY (`warehouse_code`) REFERENCES `warehouses` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_totals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_totals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `recount_required` tinyint(1) NOT NULL DEFAULT '1',
  `quantity` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_reserved` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_available` decimal(20,2) GENERATED ALWAYS AS ((`quantity` - `quantity_reserved`)) STORED COMMENT 'quantity - quantity_reserved',
  `quantity_incoming` decimal(20,2) NOT NULL DEFAULT '0.00',
  `max_inventory_updated_at` timestamp NOT NULL DEFAULT '1999-12-31 23:00:00',
  `calculated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_totals_product_id_index` (`product_id`),
  KEY `inventory_totals_calculated_at_index` (`calculated_at`),
  CONSTRAINT `inventory_totals_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_totals_by_warehouse_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_totals_by_warehouse_tag` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_tag_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint unsigned NOT NULL,
  `tag_id` int unsigned NOT NULL,
  `recalc_required` tinyint(1) NOT NULL DEFAULT '1',
  `quantity` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_reserved` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_available` decimal(20,2) GENERATED ALWAYS AS ((`quantity` - `quantity_reserved`)) STORED COMMENT 'quantity - quantity_reserved',
  `quantity_incoming` decimal(20,2) NOT NULL DEFAULT '0.00',
  `max_inventory_updated_at` timestamp NOT NULL DEFAULT '1999-12-31 23:00:00',
  `calculated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_tag` (`product_id`,`tag_id`),
  KEY `fk_inventory_totals_by_warehouse_tag_tag_id` (`tag_id`),
  KEY `inventory_totals_by_warehouse_tag_product_id_index` (`product_id`),
  KEY `inventory_totals_by_warehouse_tag_calculated_at_index` (`calculated_at`),
  KEY `inventory_totals_by_warehouse_tag_recalc_required_index` (`recalc_required`),
  KEY `inventory_totals_by_warehouse_tag_warehouse_tag_name_index` (`warehouse_tag_name`),
  KEY `inventory_totals_by_warehouse_tag_product_sku_foreign` (`product_sku`),
  CONSTRAINT `fk_inventory_totals_by_warehouse_tag_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inventory_totals_by_warehouse_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_totals_by_warehouse_tag_product_sku_foreign` FOREIGN KEY (`product_sku`) REFERENCES `products` (`sku`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mail_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mail_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `mailable` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply_to` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` text COLLATE utf8mb4_unicode_ci,
  `html_template` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_template` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `manual_request_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manual_request_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manual_request_jobs_job_name_unique` (`job_name`),
  UNIQUE KEY `manual_request_jobs_job_class_unique` (`job_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `module_auto_status_pickings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `module_auto_status_pickings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_provider_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_service_provider_class_unique` (`service_provider_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_api2cart_connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_api2cart_connections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `inventory_source_warehouse_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inventory_source_warehouse_tag_id` bigint unsigned DEFAULT NULL,
  `pricing_source_warehouse_id` bigint unsigned DEFAULT NULL,
  `prefix` char(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bridge_api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `magento_store_id` bigint unsigned DEFAULT NULL,
  `magento_warehouse_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pricing_location_id` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_synced_modified_at` datetime NOT NULL DEFAULT '2020-01-01 00:00:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_api2cart_order_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_api2cart_order_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `shipping_method_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_method_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `when_processed` datetime DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api2cart_order_id` bigint NOT NULL,
  `raw_import` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_api2cart_order_imports_order_id_foreign` (`order_id`),
  KEY `modules_api2cart_order_imports_when_processed_index` (`when_processed`),
  KEY `modules_api2cart_order_imports_order_number_index` (`order_number`),
  KEY `connection_id_api2cart_order_id_when_processed_index` (`connection_id`,`api2cart_order_id`,`when_processed`),
  CONSTRAINT `modules_api2cart_order_imports_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `modules_api2cart_connections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `modules_api2cart_order_imports_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_api2cart_product_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_api2cart_product_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `is_in_sync` tinyint(1) DEFAULT NULL,
  `product_id` bigint unsigned NOT NULL,
  `api2cart_connection_id` bigint unsigned NOT NULL,
  `api2cart_product_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api2cart_product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_pushed_at` timestamp NULL DEFAULT NULL,
  `last_pushed_response` json DEFAULT NULL,
  `last_fetched_at` datetime DEFAULT NULL,
  `last_fetched_data` json DEFAULT NULL,
  `api2cart_quantity` decimal(10,2) DEFAULT NULL,
  `api2cart_price` decimal(10,2) DEFAULT NULL,
  `api2cart_sale_price` decimal(10,2) DEFAULT NULL,
  `api2cart_sale_price_start_date` date DEFAULT NULL,
  `api2cart_sale_price_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api2cart_connection_product_id_unique` (`api2cart_connection_id`,`api2cart_product_id`),
  KEY `modules_api2cart_product_links_product_id_foreign` (`product_id`),
  KEY `modules_api2cart_product_links_is_in_sync_index` (`is_in_sync`),
  CONSTRAINT `modules_api2cart_product_links_api2cart_connection_id_foreign` FOREIGN KEY (`api2cart_connection_id`) REFERENCES `modules_api2cart_connections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modules_api2cart_product_links_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_automations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_automations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `priority` smallint NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_automations_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_automations_actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `automation_id` bigint unsigned NOT NULL,
  `priority` smallint NOT NULL DEFAULT '0',
  `action_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_automations_actions_automation_id_foreign` (`automation_id`),
  CONSTRAINT `modules_automations_actions_automation_id_foreign` FOREIGN KEY (`automation_id`) REFERENCES `modules_automations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_automations_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_automations_conditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `automation_id` bigint unsigned NOT NULL,
  `condition_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_automations_conditions_automation_id_class_value_unique` (`automation_id`,`condition_class`,`condition_value`),
  CONSTRAINT `modules_automations_conditions_automation_id_foreign` FOREIGN KEY (`automation_id`) REFERENCES `modules_automations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_autostatus_picking_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_autostatus_picking_configurations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `max_batch_size` int NOT NULL DEFAULT '10',
  `max_order_age` int NOT NULL DEFAULT '5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_boxtop_order_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_boxtop_order_lock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_boxtop_order_lock_order_id_unique` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_boxtop_warehouse_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_boxtop_warehouse_stock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `SKUGroup` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SKUNumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SKUName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Attributes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Warehouse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `WarehouseQuantity` double(8,2) NOT NULL,
  `Allocated` double(8,2) NOT NULL,
  `Available` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_dpd_ireland_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_dpd_ireland_configuration` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `live` tinyint(1) NOT NULL DEFAULT '0',
  `token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `user` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `contact_telephone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address_line_3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address_line_4` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_dpduk_connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_dpduk_connections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `collection_address_id` bigint unsigned DEFAULT NULL,
  `geo_session` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_dpduk_connections_collection_address_id_foreign` (`collection_address_id`),
  CONSTRAINT `modules_dpduk_connections_collection_address_id_foreign` FOREIGN KEY (`collection_address_id`) REFERENCES `orders_addresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_inventory_movements_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_inventory_movements_configurations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `totals_by_warehouse_tag_max_inventory_movement_id_checked` bigint unsigned NOT NULL DEFAULT '0',
  `quantity_before_job_last_movement_id_checked` bigint unsigned NOT NULL DEFAULT '0',
  `quantity_before_basic_job_last_movement_id_checked` bigint unsigned NOT NULL DEFAULT '0',
  `quantity_before_stocktake_job_last_movement_id_checked` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_inventory_movements_statistics_last28days_sale_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_inventory_movements_statistics_last28days_sale_movements` (
  `inventory_movement_id` bigint unsigned NOT NULL,
  `inventory_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `sold_at` datetime NOT NULL,
  `quantity_sold` decimal(20,3) NOT NULL,
  `included_in_7days` tinyint(1) DEFAULT NULL,
  `included_in_14days` tinyint(1) DEFAULT NULL,
  `included_in_28days` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `inventory_movement_id_index` (`inventory_movement_id`),
  KEY `inventory_id_index` (`inventory_id`),
  KEY `warehouse_id_index` (`warehouse_id`),
  KEY `sold_at_index` (`sold_at`),
  KEY `included_in_7days` (`included_in_7days`),
  KEY `included_in_14days` (`included_in_14days`),
  KEY `included_in_28days` (`included_in_28days`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_inventory_reservations_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_inventory_reservations_configurations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_inventory_reservations_warehouse_id_index` (`warehouse_id`),
  CONSTRAINT `modules_inventory_reservations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_inventory_totals_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_inventory_totals_configurations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `totals_max_product_id_checked` bigint unsigned NOT NULL DEFAULT '0',
  `totals_by_warehouse_tag_max_inventory_id_checked` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_magento2api_connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_magento2api_connections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `magento_store_id` int DEFAULT NULL,
  `inventory_source_warehouse_tag_id` int DEFAULT NULL,
  `pricing_source_warehouse_id` int DEFAULT NULL,
  `access_token_encrypted` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_magento2api_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_magento2api_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `exists_in_magento` tinyint(1) DEFAULT NULL,
  `magento_price` decimal(20,2) DEFAULT NULL,
  `magento_sale_price` decimal(20,2) DEFAULT NULL,
  `magento_sale_price_start_date` datetime DEFAULT NULL,
  `magento_sale_price_end_date` datetime DEFAULT NULL,
  `is_inventory_in_sync` tinyint(1) DEFAULT NULL,
  `quantity` decimal(20,2) DEFAULT NULL,
  `is_in_stock` tinyint(1) DEFAULT NULL,
  `stock_items_fetched_at` timestamp NULL DEFAULT NULL,
  `stock_items_raw_import` json DEFAULT NULL,
  `base_prices_fetched_at` timestamp NULL DEFAULT NULL,
  `base_prices_raw_import` json DEFAULT NULL,
  `special_prices_fetched_at` timestamp NULL DEFAULT NULL,
  `special_prices_raw_import` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_magento2api_products_product_id_foreign` (`product_id`),
  CONSTRAINT `modules_magento2api_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_magento2api_products_inventory_comparison_view`;
/*!50001 DROP VIEW IF EXISTS `modules_magento2api_products_inventory_comparison_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `modules_magento2api_products_inventory_comparison_view` AS SELECT
 1 AS `modules_magento2api_connection_id`,
 1 AS `modules_magento2api_products_id`,
 1 AS `sku`,
 1 AS `magento_quantity`,
 1 AS `expected_quantity`,
 1 AS `stock_items_fetched_at`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `modules_magento2api_products_prices_comparison_view`;
/*!50001 DROP VIEW IF EXISTS `modules_magento2api_products_prices_comparison_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `modules_magento2api_products_prices_comparison_view` AS SELECT
 1 AS `modules_magento2api_connection_id`,
 1 AS `modules_magento2api_products_id`,
 1 AS `sku`,
 1 AS `magento_store_id`,
 1 AS `magento_price`,
 1 AS `expected_price`,
 1 AS `magento_sale_price`,
 1 AS `expected_sale_price`,
 1 AS `magento_sale_price_start_date`,
 1 AS `expected_sale_price_start_date`,
 1 AS `magento_sale_price_end_date`,
 1 AS `expected_sale_price_end_date`,
 1 AS `base_prices_fetched_at`,
 1 AS `special_prices_fetched_at`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `modules_magento2msi_connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_magento2msi_connections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `magento_source_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inventory_source_warehouse_tag_id` bigint unsigned DEFAULT NULL,
  `api_access_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_magento2msi_inventory_source_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_magento2msi_inventory_source_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `custom_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exists_in_magento` tinyint(1) DEFAULT NULL,
  `source_assigned` tinyint(1) DEFAULT NULL,
  `sync_required` tinyint(1) DEFAULT NULL,
  `inventory_source_items_fetched_at` timestamp NULL DEFAULT NULL,
  `quantity` decimal(20,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `magento_product_id` bigint unsigned DEFAULT NULL,
  `magento_product_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `connection_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `inventory_totals_by_warehouse_tag_id` bigint unsigned DEFAULT NULL,
  `inventory_source_items` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_connection_id_sku` (`connection_id`,`sku`),
  UNIQUE KEY `modules_magento2msi_inventory_source_items_custom_uuid_unique` (`custom_uuid`),
  KEY `modules_magento2msi_inventory_source_items_connection_id_index` (`connection_id`),
  KEY `modules_magento2msi_inventory_source_items_product_id_index` (`product_id`),
  KEY `inventory_totals_by_warehouse_tag_id_index` (`inventory_totals_by_warehouse_tag_id`),
  KEY `exists_in_magento_index` (`exists_in_magento`),
  KEY `source_assigned_index` (`source_assigned`),
  KEY `sync_required_index` (`sync_required`),
  KEY `magento_product_id_index` (`magento_product_id`),
  KEY `magento_product_type_index` (`magento_product_type`),
  KEY `custom_uuid_index` (`custom_uuid`),
  KEY `modules_magento2msi_inventory_source_items_sku_foreign` (`sku`),
  CONSTRAINT `inventory_totals_by_warehouse_tag_id` FOREIGN KEY (`inventory_totals_by_warehouse_tag_id`) REFERENCES `inventory_totals_by_warehouse_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `modules_magento2msi_inventory_source_items_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `modules_magento2msi_connections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `modules_magento2msi_inventory_source_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `modules_magento2msi_inventory_source_items_sku_foreign` FOREIGN KEY (`sku`) REFERENCES `products` (`sku`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_printnode_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_printnode_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_printnode_print_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_printnode_print_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `printer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire_after` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_quantity_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_quantity_discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `configuration` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_quantity_discounts_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_quantity_discounts_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quantity_discount_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_quantity_discounts_products_quantity_discount_id_foreign` (`quantity_discount_id`),
  KEY `modules_quantity_discounts_products_product_id_foreign` (`product_id`),
  CONSTRAINT `modules_quantity_discounts_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modules_quantity_discounts_products_quantity_discount_id_foreign` FOREIGN KEY (`quantity_discount_id`) REFERENCES `modules_quantity_discounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_queue_monitor_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_queue_monitor_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dispatched_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processing_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `seconds_dispatching` bigint GENERATED ALWAYS AS (timestampdiff(SECOND,`dispatched_at`,`processing_at`)) STORED COMMENT 'TIMESTAMPDIFF(SECOND, dispatched_at, processing_at)',
  `seconds_running` bigint GENERATED ALWAYS AS (timestampdiff(SECOND,`processing_at`,`processed_at`)) STORED COMMENT 'TIMESTAMPDIFF(SECOND, processing_at, processed_at)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_queue_monitor_jobs_uuid_unique` (`uuid`),
  KEY `modules_queue_monitor_jobs_job_class_index` (`job_class`),
  KEY `modules_queue_monitor_jobs_dispatched_at_index` (`dispatched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_rmsapi_connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_rmsapi_connections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'price',
  `products_last_timestamp` bigint unsigned NOT NULL DEFAULT '0',
  `shippings_last_timestamp` bigint unsigned NOT NULL DEFAULT '0',
  `sales_last_timestamp` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_rmsapi_connections_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `modules_rmsapi_connections_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_rmsapi_products_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_rmsapi_products_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint unsigned NOT NULL,
  `warehouse_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `inventory_id` bigint unsigned DEFAULT NULL,
  `product_price_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `rms_product_id` bigint unsigned DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_web_item` tinyint(1) DEFAULT NULL,
  `quantity_on_hand` decimal(20,2) DEFAULT NULL,
  `quantity_committed` decimal(20,2) DEFAULT NULL,
  `quantity_available` decimal(20,2) DEFAULT NULL,
  `quantity_on_order` decimal(20,2) DEFAULT NULL,
  `reorder_point` decimal(20,2) DEFAULT NULL,
  `restock_level` decimal(20,2) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `price_a` decimal(20,2) DEFAULT NULL,
  `cost` decimal(20,2) DEFAULT NULL,
  `sale_price` decimal(20,2) DEFAULT NULL,
  `sale_price_start_date` timestamp NULL DEFAULT NULL,
  `sale_price_end_date` timestamp NULL DEFAULT NULL,
  `department_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_description_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_description_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_description_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_import` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_rmsapi_products_imports_inventory_id_foreign` (`inventory_id`),
  KEY `modules_rmsapi_products_imports_product_id_foreign` (`product_id`),
  KEY `modules_rmsapi_products_imports_warehouse_id_foreign` (`warehouse_id`),
  KEY `modules_rmsapi_products_imports_warehouse_code_foreign` (`warehouse_code`),
  KEY `modules_rmsapi_products_imports_connection_id_index` (`connection_id`),
  KEY `modules_rmsapi_products_imports_rms_product_id_index` (`rms_product_id`),
  KEY `modules_rmsapi_products_imports_is_web_item_index` (`is_web_item`),
  KEY `modules_rmsapi_products_imports_reserved_at_index` (`reserved_at`),
  KEY `modules_rmsapi_products_imports_processed_at_index` (`processed_at`),
  KEY `modules_rmsapi_products_imports_product_price_id_foreign` (`product_price_id`),
  CONSTRAINT `modules_rmsapi_products_imports_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `modules_rmsapi_connections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modules_rmsapi_products_imports_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modules_rmsapi_products_imports_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modules_rmsapi_products_imports_product_price_id_foreign` FOREIGN KEY (`product_price_id`) REFERENCES `products_prices` (`id`),
  CONSTRAINT `modules_rmsapi_products_imports_warehouse_code_foreign` FOREIGN KEY (`warehouse_code`) REFERENCES `warehouses` (`code`) ON DELETE CASCADE,
  CONSTRAINT `modules_rmsapi_products_imports_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_rmsapi_products_quantity_comparison_view`;
/*!50001 DROP VIEW IF EXISTS `modules_rmsapi_products_quantity_comparison_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `modules_rmsapi_products_quantity_comparison_view` AS SELECT
 1 AS `record_id`,
 1 AS `product_sku`,
 1 AS `product_id`,
 1 AS `warehouse_id`,
 1 AS `warehouse_code`,
 1 AS `rms_quantity`,
 1 AS `pm_quantity`,
 1 AS `quantity_delta`,
 1 AS `modules_rmsapi_products_imports_updated_at`,
 1 AS `inventory_id`,
 1 AS `movement_id`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `modules_rmsapi_sales_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_rmsapi_sales_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inventory_movement_id` bigint unsigned DEFAULT NULL,
  `connection_id` bigint unsigned NOT NULL,
  `inventory_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `reserved_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `quantity` decimal(20,2) DEFAULT NULL,
  `transaction_time` timestamp NULL DEFAULT NULL,
  `transaction_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_entry_id` int DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_import` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_rmsapi_sales_imports_inventory_movement_id_foreign` (`inventory_movement_id`),
  KEY `modules_rmsapi_sales_imports_connection_id_foreign` (`connection_id`),
  KEY `modules_rmsapi_sales_imports_inventory_id_foreign` (`inventory_id`),
  KEY `modules_rmsapi_sales_imports_uuid_index` (`uuid`),
  KEY `modules_rmsapi_sales_imports_type_index` (`type`),
  CONSTRAINT `modules_rmsapi_sales_imports_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `modules_rmsapi_connections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modules_rmsapi_sales_imports_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
  CONSTRAINT `modules_rmsapi_sales_imports_inventory_movement_id_foreign` FOREIGN KEY (`inventory_movement_id`) REFERENCES `inventory_movements` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_rmsapi_shipping_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_rmsapi_shipping_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection_id` bigint unsigned NOT NULL,
  `raw_import` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_rmsapi_shipping_imports_connection_id_foreign` (`connection_id`),
  CONSTRAINT `modules_rmsapi_shipping_imports_connection_id_foreign` FOREIGN KEY (`connection_id`) REFERENCES `modules_rmsapi_connections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_slack_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_slack_config` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `incoming_webhook_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_stocktaking_suggestions_configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_stocktaking_suggestions_configurations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_count_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_webhooks_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_webhooks_configuration` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `topic_arn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules_webhooks_pending_webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules_webhooks_pending_webhooks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `message` json NOT NULL,
  `sns_message_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_at` timestamp NULL DEFAULT NULL,
  `available_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_webhooks_pending_webhooks_reserved_at_published_at_index` (`reserved_at`,`published_at`),
  KEY `modules_webhooks_pending_webhooks_reserved_at_index` (`reserved_at`),
  KEY `modules_webhooks_pending_webhooks_published_at_index` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `navigation_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `navigation_menu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(999) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `group` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `label_template` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_on_hold` tinyint(1) NOT NULL DEFAULT '0',
  `is_editing` tinyint(1) NOT NULL DEFAULT '0',
  `is_fully_paid` tinyint(1) GENERATED ALWAYS AS (((((`total_products` + `total_shipping`) - `total_discounts`) - `total_paid`) < 0.01)) STORED COMMENT 'total_products + total_shipping - total_discounts - total_paid < 0.01',
  `product_line_count` int DEFAULT NULL,
  `total_products` decimal(13,2) DEFAULT NULL,
  `total_shipping` decimal(13,2) NOT NULL DEFAULT '0.00',
  `total_discounts` decimal(13,2) NOT NULL DEFAULT '0.00',
  `total_order` decimal(13,2) GENERATED ALWAYS AS (((`total_products` + `total_shipping`) - `total_discounts`)) STORED COMMENT 'total_products + total_shipping - total_discounts',
  `total_paid` decimal(13,2) NOT NULL DEFAULT '0.00',
  `total_outstanding` decimal(13,2) GENERATED ALWAYS AS ((((`total_products` + `total_shipping`) - `total_discounts`) - `total_paid`)) STORED COMMENT 'total_products + total_shipping - total_discounts - total_paid',
  `shipping_address_id` bigint unsigned DEFAULT NULL,
  `billing_address_id` bigint unsigned DEFAULT NULL,
  `shipping_method_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `shipping_method_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `packer_user_id` bigint unsigned DEFAULT NULL,
  `order_placed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `picked_at` timestamp NULL DEFAULT NULL,
  `packed_at` timestamp NULL DEFAULT NULL,
  `order_closed_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custom_unique_reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  UNIQUE KEY `orders_custom_unique_reference_id_unique` (`custom_unique_reference_id`),
  KEY `orders_shipping_address_id_foreign` (`shipping_address_id`),
  KEY `orders_packer_user_id_foreign` (`packer_user_id`),
  KEY `orders_status_code_index` (`status_code`),
  KEY `orders_label_template_index` (`label_template`),
  KEY `orders_is_active_index` (`is_active`),
  KEY `orders_is_on_hold_index` (`is_on_hold`),
  KEY `orders_order_placed_at_index` (`order_placed_at`),
  KEY `orders_billing_address_id_foreign` (`billing_address_id`),
  FULLTEXT KEY `orders_order_number_status_code_fulltext` (`order_number`,`status_code`),
  CONSTRAINT `orders_billing_address_id_foreign` FOREIGN KEY (`billing_address_id`) REFERENCES `orders_addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_packer_user_id_foreign` FOREIGN KEY (`packer_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_shipping_address_id_foreign` FOREIGN KEY (`shipping_address_id`) REFERENCES `orders_addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_status_code_foreign` FOREIGN KEY (`status_code`) REFERENCES `orders_statuses` (`code`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `state_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `state_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `website` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `first_name_encrypted` longtext COLLATE utf8mb4_unicode_ci,
  `last_name_encrypted` longtext COLLATE utf8mb4_unicode_ci,
  `email_encrypted` longtext COLLATE utf8mb4_unicode_ci,
  `phone_encrypted` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_comments_order_id_foreign` (`order_id`),
  KEY `orders_comments_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_comments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `sku_ordered` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ordered` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_shipped` tinyint(1) GENERATED ALWAYS AS ((((`quantity_ordered` - `quantity_split`) - `quantity_shipped`) <= 0)) STORED COMMENT 'quantity_ordered - quantity_split - quantity_shipped <= 0',
  `price` decimal(10,3) NOT NULL DEFAULT '0.000',
  `quantity_ordered` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_split` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(20,2) GENERATED ALWAYS AS (((`quantity_ordered` - `quantity_split`) * `price`)) STORED COMMENT '(quantity_ordered - quantity_split) * price',
  `quantity_shipped` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_to_pick` decimal(10,2) GENERATED ALWAYS AS ((((`quantity_ordered` - `quantity_split`) - `quantity_picked`) - `quantity_skipped_picking`)) STORED COMMENT 'quantity_ordered - quantity_split - quantity_picked - quantity_skipped_picking',
  `quantity_to_ship` decimal(10,2) GENERATED ALWAYS AS (((`quantity_ordered` - `quantity_split`) - `quantity_shipped`)) STORED COMMENT 'quantity_ordered - quantity_split - quantity_shipped',
  `quantity_picked` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_skipped_picking` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_not_picked` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custom_unique_reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_products_custom_unique_reference_id_unique` (`custom_unique_reference_id`),
  KEY `orders_products_product_id_foreign` (`product_id`),
  KEY `orders_products_order_id_foreign` (`order_id`),
  KEY `orders_products_is_shipped_index` (`is_shipped`),
  CONSTRAINT `orders_products_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_products_shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_products_shipments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `order_product_id` bigint unsigned NOT NULL,
  `order_shipment_id` bigint unsigned DEFAULT NULL,
  `sku_shipped` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity_shipped` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_products_shipments_order_id_foreign` (`order_id`),
  CONSTRAINT `orders_products_shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_products_totals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_products_totals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `count` int NOT NULL DEFAULT '0',
  `quantity_ordered` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_split` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_picked` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_skipped_picking` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_not_picked` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_shipped` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_to_pick` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quantity_to_ship` decimal(20,2) NOT NULL DEFAULT '0.00',
  `max_updated_at` timestamp NOT NULL DEFAULT '1999-12-31 23:00:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_products_totals_order_id_unique` (`order_id`),
  KEY `orders_products_totals_count_index` (`count`),
  KEY `orders_products_totals_quantity_ordered_index` (`quantity_ordered`),
  KEY `orders_products_totals_quantity_split_index` (`quantity_split`),
  KEY `orders_products_totals_quantity_picked_index` (`quantity_picked`),
  KEY `orders_products_totals_quantity_skipped_picking_index` (`quantity_skipped_picking`),
  KEY `orders_products_totals_quantity_not_picked_index` (`quantity_not_picked`),
  KEY `orders_products_totals_quantity_shipped_index` (`quantity_shipped`),
  KEY `orders_products_totals_quantity_to_pick_index` (`quantity_to_pick`),
  KEY `orders_products_totals_quantity_to_ship_index` (`quantity_to_ship`),
  KEY `orders_products_totals_updated_at_index` (`updated_at`),
  CONSTRAINT `orders_products_totals_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_shipments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL,
  `shipping_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carrier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `service` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tracking_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `base64_pdf_labels` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_shipments_order_id_foreign` (`order_id`),
  KEY `orders_shipments_user_id_foreign` (`user_id`),
  FULLTEXT KEY `orders_shipments_shipping_number_fulltext` (`shipping_number`),
  CONSTRAINT `orders_shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_shipments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_active` tinyint(1) NOT NULL DEFAULT '1',
  `order_on_hold` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sync_ecommerce` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_statuses_name_unique` (`name`),
  UNIQUE KEY `orders_statuses_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `picks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `picks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `warehouse_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku_ordered` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ordered` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_picked` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_skipped_picking` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `picks_user_id_foreign` (`user_id`),
  KEY `picks_product_id_foreign` (`product_id`),
  CONSTRAINT `picks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `picks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `picks_orders_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `picks_orders_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pick_id` bigint unsigned NOT NULL,
  `order_product_id` bigint unsigned DEFAULT NULL,
  `quantity_picked` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_skipped_picking` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `picks_orders_products_pick_id_foreign` (`pick_id`),
  KEY `picks_orders_products_order_product_id_foreign` (`order_product_id`),
  CONSTRAINT `picks_orders_products_order_product_id_foreign` FOREIGN KEY (`order_product_id`) REFERENCES `orders_products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `picks_orders_products_pick_id_foreign` FOREIGN KEY (`pick_id`) REFERENCES `picks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sale_price_start_date` date NOT NULL DEFAULT '1899-01-01',
  `sale_price_end_date` date NOT NULL DEFAULT '1899-01-01',
  `commodity_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_reserved` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity_available` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_deleted_at_index` (`deleted_at`),
  KEY `products_quantity_index` (`quantity`),
  KEY `products_quantity_reserved_index` (`quantity_reserved`),
  KEY `products_quantity_available_index` (`quantity_available`),
  KEY `products_department_index` (`department`),
  KEY `products_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `trigger_on_products` AFTER INSERT ON `products` FOR EACH ROW BEGIN
                INSERT INTO inventory (product_id, warehouse_id, warehouse_code, created_at, updated_at)
                SELECT new.id as product_id, warehouses.id as warehouse_id, warehouses.code as warehouse_code, now(), now() FROM warehouses;

                INSERT INTO products_prices (product_id, warehouse_id, warehouse_code, created_at, updated_at)
                SELECT new.id as product_id, warehouses.id as warehouse_id, warehouses.code as warehouse_code, now(), now() FROM warehouses;

                INSERT INTO products_aliases (product_id, alias, created_at, updated_at)
                VALUES (NEW.id, NEW.sku, now(), now())
                ON DUPLICATE KEY UPDATE product_id = NEW.id;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `products_aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_aliases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_aliases_alias_unique` (`alias`),
  KEY `products_aliases_product_id_foreign` (`product_id`),
  CONSTRAINT `products_aliases_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `inventory_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `location_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `warehouse_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(20,2) NOT NULL DEFAULT '0.00',
  `price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `sale_price_start_date` date NOT NULL DEFAULT '2000-01-01',
  `sale_price_end_date` date NOT NULL DEFAULT '2000-01-01',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_prices_product_id_warehouse_id_unique` (`product_id`,`warehouse_id`),
  KEY `products_prices_warehouse_id_foreign` (`warehouse_id`),
  KEY `products_prices_product_id_index` (`product_id`),
  KEY `products_prices_warehouse_code_index` (`warehouse_code`),
  KEY `products_prices_inventory_id_foreign` (`inventory_id`),
  CONSTRAINT `products_prices_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_prices_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_prices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `queue_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `queue_monitor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `started_at_exact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `finished_at_exact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_elapsed` double(12,6) DEFAULT NULL,
  `failed` tinyint(1) NOT NULL DEFAULT '0',
  `attempt` int NOT NULL DEFAULT '0',
  `progress` int DEFAULT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci,
  `exception_message` text COLLATE utf8mb4_unicode_ci,
  `exception_class` text COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `queue_monitor_job_id_index` (`job_id`),
  KEY `queue_monitor_started_at_index` (`started_at`),
  KEY `queue_monitor_time_elapsed_index` (`time_elapsed`),
  KEY `queue_monitor_failed_index` (`failed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `scheduled_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheduled_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `target_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `send_at` datetime NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `rescheduled_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `meta` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `sessions_id_unique` (`id`),
  KEY `sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shipping_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_provider_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_services_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stocktake_suggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stocktake_suggestions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventory_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `points` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stocktake_suggestions_inventory_id_reason_unique` (`inventory_id`,`reason`),
  KEY `stocktake_suggestions_product_id_foreign` (`product_id`),
  KEY `stocktake_suggestions_warehouse_id_reason_index` (`warehouse_id`,`reason`),
  KEY `stocktake_suggestions_inventory_id_reason_index` (`inventory_id`,`reason`),
  KEY `stocktake_suggestions_reason_index` (`reason`),
  CONSTRAINT `stocktake_suggestions_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stocktake_suggestions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stocktake_suggestions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `taggables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taggables` (
  `tag_id` int unsigned NOT NULL,
  `tag_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taggable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taggable_id` bigint unsigned NOT NULL,
  UNIQUE KEY `taggables_tag_id_taggable_id_taggable_type_unique` (`tag_id`,`taggable_id`,`taggable_type`),
  KEY `taggables_taggable_type_taggable_id_index` (`taggable_type`,`taggable_id`),
  KEY `taggables_taggable_type_index` (`taggable_type`),
  CONSTRAINT `taggables_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` json NOT NULL,
  `slug` json NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_column` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_family_hash_index` (`family_hash`),
  KEY `telescope_entries_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `telescope_entries_tags_entry_uuid_tag_index` (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `raw_data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `printer_id` int DEFAULT NULL,
  `address_label_template` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ask_for_shipping_number` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `location_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_expires_at` datetime DEFAULT NULL,
  `default_dashboard_uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active_transaction_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_active_transaction_id_foreign` (`active_transaction_id`),
  KEY `users_warehouse_code_foreign` (`warehouse_code`),
  CONSTRAINT `users_active_transaction_id_foreign` FOREIGN KEY (`active_transaction_id`) REFERENCES `transactions` (`id`),
  CONSTRAINT `users_warehouse_code_foreign` FOREIGN KEY (`warehouse_code`) REFERENCES `warehouses` (`code`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `view_key_dates`;
/*!50001 DROP VIEW IF EXISTS `view_key_dates`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_key_dates` AS SELECT
 1 AS `date`,
 1 AS `this_week_start_date`,
 1 AS `this_month_start_date`,
 1 AS `this_year_start_date`,
 1 AS `now`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouses_code_unique` (`code`),
  KEY `warehouses_address_id_foreign` (`address_id`),
  CONSTRAINT `warehouses_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `orders_addresses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `widgets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP VIEW IF EXISTS `modules_magento2api_products_inventory_comparison_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `modules_magento2api_products_inventory_comparison_view` AS select `modules_magento2api_products`.`connection_id` AS `modules_magento2api_connection_id`,`modules_magento2api_products`.`id` AS `modules_magento2api_products_id`,`products`.`sku` AS `sku`,floor(max(`modules_magento2api_products`.`quantity`)) AS `magento_quantity`,if((floor(sum(`inventory`.`quantity_available`)) < 0),0,floor(sum(`inventory`.`quantity_available`))) AS `expected_quantity`,`modules_magento2api_products`.`stock_items_fetched_at` AS `stock_items_fetched_at` from (((((`modules_magento2api_products` left join `modules_magento2api_connections` on((`modules_magento2api_connections`.`id` = `modules_magento2api_products`.`connection_id`))) left join `taggables` on(((`taggables`.`tag_id` = `modules_magento2api_connections`.`inventory_source_warehouse_tag_id`) and (`taggables`.`taggable_type` = 'App\\Models\\Warehouse')))) left join `warehouses` on((`warehouses`.`id` = `taggables`.`taggable_id`))) left join `inventory` on(((`inventory`.`product_id` = `modules_magento2api_products`.`product_id`) and (`inventory`.`warehouse_id` = `warehouses`.`id`)))) left join `products` on((`products`.`id` = `modules_magento2api_products`.`product_id`))) where ((`modules_magento2api_connections`.`inventory_source_warehouse_tag_id` is not null) and (ifnull(`modules_magento2api_products`.`exists_in_magento`,1) = 1)) group by `modules_magento2api_products`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `modules_magento2api_products_prices_comparison_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `modules_magento2api_products_prices_comparison_view` AS select `modules_magento2api_products`.`connection_id` AS `modules_magento2api_connection_id`,`modules_magento2api_products`.`id` AS `modules_magento2api_products_id`,`products`.`sku` AS `sku`,`modules_magento2api_connections`.`magento_store_id` AS `magento_store_id`,`modules_magento2api_products`.`magento_price` AS `magento_price`,`products_prices`.`price` AS `expected_price`,`modules_magento2api_products`.`magento_sale_price` AS `magento_sale_price`,`products_prices`.`sale_price` AS `expected_sale_price`,`modules_magento2api_products`.`magento_sale_price_start_date` AS `magento_sale_price_start_date`,`products_prices`.`sale_price_start_date` AS `expected_sale_price_start_date`,`modules_magento2api_products`.`magento_sale_price_end_date` AS `magento_sale_price_end_date`,`products_prices`.`sale_price_end_date` AS `expected_sale_price_end_date`,`modules_magento2api_products`.`base_prices_fetched_at` AS `base_prices_fetched_at`,`modules_magento2api_products`.`special_prices_fetched_at` AS `special_prices_fetched_at` from (((`modules_magento2api_products` left join `products` on((`products`.`id` = `modules_magento2api_products`.`product_id`))) left join `modules_magento2api_connections` on((`modules_magento2api_connections`.`id` = `modules_magento2api_products`.`connection_id`))) left join `products_prices` on(((`products_prices`.`product_id` = `modules_magento2api_products`.`product_id`) and (`products_prices`.`warehouse_id` = `modules_magento2api_connections`.`pricing_source_warehouse_id`)))) where ((`modules_magento2api_connections`.`pricing_source_warehouse_id` is not null) and (ifnull(`modules_magento2api_products`.`exists_in_magento`,0) = 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `modules_rmsapi_products_quantity_comparison_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `modules_rmsapi_products_quantity_comparison_view` AS select `modules_rmsapi_products_imports`.`id` AS `record_id`,`modules_rmsapi_products_imports`.`sku` AS `product_sku`,`modules_rmsapi_products_imports`.`product_id` AS `product_id`,`modules_rmsapi_products_imports`.`warehouse_id` AS `warehouse_id`,`modules_rmsapi_products_imports`.`warehouse_code` AS `warehouse_code`,`modules_rmsapi_products_imports`.`quantity_on_hand` AS `rms_quantity`,`inventory`.`quantity` AS `pm_quantity`,(`modules_rmsapi_products_imports`.`quantity_on_hand` - `inventory`.`quantity`) AS `quantity_delta`,`modules_rmsapi_products_imports`.`updated_at` AS `modules_rmsapi_products_imports_updated_at`,`inventory`.`id` AS `inventory_id`,(select max(`inventory_movements`.`id`) from `inventory_movements` where ((`inventory_movements`.`inventory_id` = `inventory`.`id`) and (`inventory_movements`.`description` = 'stocktake') and (`inventory_movements`.`user_id` = 1) and (`inventory_movements`.`created_at` > (now() - interval 7 day)))) AS `movement_id` from (`modules_rmsapi_products_imports` join `inventory` on(((`inventory`.`product_id` = `modules_rmsapi_products_imports`.`product_id`) and (`inventory`.`warehouse_id` = `modules_rmsapi_products_imports`.`warehouse_id`)))) where `modules_rmsapi_products_imports`.`id` in (select max(`modules_rmsapi_products_imports`.`id`) from `modules_rmsapi_products_imports` group by `modules_rmsapi_products_imports`.`warehouse_id`,`modules_rmsapi_products_imports`.`product_id`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_key_dates`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_key_dates` AS select curdate() AS `date`,(curdate() + interval -(weekday(now())) day) AS `this_week_start_date`,(curdate() + interval (-(dayofmonth(now())) + 1) day) AS `this_month_start_date`,(curdate() + interval (-(dayofyear(now())) + 1) day) AS `this_year_start_date`,now() AS `now` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` VALUES (1,'2019_02_25_231036_create_scheduled_notifications_table',1);
INSERT INTO `migrations` VALUES (2,'2019_10_15_000001_create_core_tables',1);
INSERT INTO `migrations` VALUES (3,'2019_10_16_000000_core_v1',1);
INSERT INTO `migrations` VALUES (4,'2019_10_16_000001_install_application',1);
INSERT INTO `migrations` VALUES (5,'2021_09_10_130000_add_meta_to_scheduled_notifications',1);
INSERT INTO `migrations` VALUES (6,'2023_06_25_130738_create_view_key_dates_view',1);
INSERT INTO `migrations` VALUES (7,'2023_08_08_151511_create_temporary_inventory_movements_new_table',1);
INSERT INTO `migrations` VALUES (8,'2024_02_02_084715_add_department_column_to_products_table',1);
INSERT INTO `migrations` VALUES (9,'2024_02_02_084745_add_category_column_to_products_table',1);
INSERT INTO `migrations` VALUES (10,'2024_02_02_095414_add_product_price_id_to_modules_rmsapi_products_imports_table',1);
INSERT INTO `migrations` VALUES (11,'2024_02_02_101753_create_trigger_on_products_table',1);
INSERT INTO `migrations` VALUES (12,'2024_02_12_144555_install_magento2msi_service_provider',1);
INSERT INTO `migrations` VALUES (13,'2024_02_15_105353_rebuild_magento2msi_tables',1);
INSERT INTO `migrations` VALUES (14,'2024_02_16_114529_add_index_to_modules_api2cart_order_imports_table',1);
INSERT INTO `migrations` VALUES (15,'2024_02_18_223022_add_indexes_to_taggables_table',1);
INSERT INTO `migrations` VALUES (16,'2024_02_21_162152_install_manual_request_jobs',1);
INSERT INTO `migrations` VALUES (17,'2024_02_21_183331_add_recalc_required_column_to_inventory_totals_by_warehouse_tag_table',1);
INSERT INTO `migrations` VALUES (18,'2024_02_26_105149_create_data_collections_comments_table',1);
INSERT INTO `migrations` VALUES (19,'2024_02_28_111132_create_inventory_reservations_table',1);
INSERT INTO `migrations` VALUES (20,'2024_03_12_094526_uninstall_inventory_reservations_module',1);
INSERT INTO `migrations` VALUES (21,'2024_03_12_095444_install_inventory_quantity_reserved_module',1);
INSERT INTO `migrations` VALUES (22,'2024_03_12_100647_install_active_orders_inventory_reservations_module',1);
INSERT INTO `migrations` VALUES (23,'2024_03_22_072347_add_auto_heal_job_class_column_to_heartbeats_table',1);
INSERT INTO `migrations` VALUES (24,'2024_03_29_094227_add_api2cart_jobs_to_manual_request_jobs_table',1);
INSERT INTO `migrations` VALUES (25,'2024_04_01_105532_add_billing_address_id_column_to_orders_table',1);
INSERT INTO `migrations` VALUES (26,'2024_04_01_160000_add_shipping_address_label_shipping_service',1);
INSERT INTO `migrations` VALUES (27,'2024_04_09_135626_make_fields_nullable_in_modules_magento2msi_inventory_source_items_table',1);
INSERT INTO `migrations` VALUES (28,'2024_04_16_171339_remove_old_heartbeats',1);
INSERT INTO `migrations` VALUES (29,'2024_04_17_133738_add_suppier_column_to_products_table',1);
INSERT INTO `migrations` VALUES (30,'2024_04_19_130414_create_transaction_table',1);
INSERT INTO `migrations` VALUES (31,'2024_04_19_131829_add_active_transaction_id_column_to_users_table',1);
INSERT INTO `migrations` VALUES (32,'2024_04_19_215313_add_warehouse_code_to_users_table',1);
INSERT INTO `migrations` VALUES (33,'2024_04_30_162014_fill_users_warehouse_code',1);
INSERT INTO `migrations` VALUES (34,'2024_05_03_185707_add_total_cost_total_price_columns_to_inventory_table',1);
INSERT INTO `migrations` VALUES (35,'2024_05_07_104127_change_api2cart_order_id_column_on_modules_api2cart_order_imports_table',1);
INSERT INTO `migrations` VALUES (36,'2024_05_08_171804_add_recount_required_column_to_inventory_totals_table',1);
INSERT INTO `migrations` VALUES (37,'2024_05_11_183800_create_inventory_groups_table',1);
INSERT INTO `migrations` VALUES (38,'2024_05_11_184927_install_inventory_groups_module',1);
INSERT INTO `migrations` VALUES (39,'2024_05_11_204222_install_inventory_module',1);
INSERT INTO `migrations` VALUES (40,'2024_05_16_105731_add_tag_name_to_taggables_table',1);
INSERT INTO `migrations` VALUES (41,'2024_05_17_105744_add_database_version_column_to_configurations_table',1);
INSERT INTO `migrations` VALUES (42,'2024_05_17_122202_add_warehouse_tag_name_column_to_inventory_totals_by_warehouse_tag_table',1);
INSERT INTO `migrations` VALUES (43,'2024_05_17_122352_add_product_sku_column_to_inventory_totals_by_warehouse_tag_table',1);
INSERT INTO `migrations` VALUES (44,'2024_05_30_195833_add_ecommerce_connected_column_to_configurations_table',1);
INSERT INTO `migrations` VALUES (45,'2024_06_20_092334_update_product_sku_index_on_inventory_totals_by_warehouse_tag_table',1);
INSERT INTO `migrations` VALUES (46,'2024_07_23_085348_install_quantity_discounts_service_provider',1);
INSERT INTO `migrations` VALUES (47,'2024_07_23_104209_create_modules_quantity_discounts_table',1);
INSERT INTO `migrations` VALUES (48,'2024_07_23_104357_create_modules_quantity_discounts_products_table',1);
INSERT INTO `migrations` VALUES (49,'2024_07_24_213811_add_custom_uuid_column_to_data_collections_table',1);
INSERT INTO `migrations` VALUES (50,'2024_07_25_151321_add_price_columns_to_data_collections_records_table',1);
INSERT INTO `migrations` VALUES (51,'2024_07_25_191233_add_warehouse_code_column_to_data_collections_records_table',1);
INSERT INTO `migrations` VALUES (52,'2024_07_25_200630_add_inventory_id_column_to_products_prices_table',1);
INSERT INTO `migrations` VALUES (53,'2024_07_25_210327_add_total_price_column_to_data_collection_records_table',1);
INSERT INTO `migrations` VALUES (54,'2024_07_25_220907_rebuild_unit_discounts_column_on_data_collection_records_table',1);
INSERT INTO `migrations` VALUES (55,'2024_07_26_080721_add_price_source_id_column_to_data_collection_records_table',1);
