-- ============================================================
-- Multan Tech Accessories (https://multantech.store)
-- Complete Production Database Schema
-- MySQL 8.0+ / MariaDB (utf8mb4 / InnoDB)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id`        INT UNSIGNED NULL DEFAULT NULL,
  `slug`             VARCHAR(150) NOT NULL,
  `name`             VARCHAR(150) NOT NULL,
  `intro_html`       TEXT NULL,
  `meta_title`       VARCHAR(255) NULL,
  `meta_description` VARCHAR(400) NULL,
  `target_keyword`   VARCHAR(255) NULL,
  `sort_order`       INT NOT NULL DEFAULT 0,
  `pinned`           TINYINT(1) NOT NULL DEFAULT 0,
  `is_active`        TINYINT(1) NOT NULL DEFAULT 1,
  `product_count`    INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categories_slug` (`slug`),
  KEY `idx_categories_parent` (`parent_id`),
  KEY `idx_categories_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. brands
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `brands` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_brand_id` VARCHAR(50) NULL,
  `name`              VARCHAR(150) NOT NULL,
  `slug`              VARCHAR(150) NOT NULL,
  `logo_url`          VARCHAR(500) NULL,
  `intro_html`        TEXT NULL,
  `meta_title`        VARCHAR(255) NULL,
  `meta_description`  VARCHAR(400) NULL,
  `target_keyword`    VARCHAR(255) NULL,
  `is_active`         TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_brands_slug` (`slug`),
  KEY `idx_brands_supplier_id` (`supplier_brand_id`),
  KEY `idx_brands_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. products
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id`                        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source`                    ENUM('supplier','manual','import') NOT NULL DEFAULT 'supplier',
  `source_name`               VARCHAR(100) NULL DEFAULT NULL,
  `supplier_id`               VARCHAR(50) NULL DEFAULT NULL,
  `sku`                       VARCHAR(100) NULL DEFAULT NULL,
  `slug`                      VARCHAR(350) NOT NULL,
  `title`                     VARCHAR(500) NOT NULL,
  `category_id`               INT UNSIGNED NULL DEFAULT NULL,
  `subcategory_title`         VARCHAR(255) NULL DEFAULT NULL,
  `brand_id`                  INT UNSIGNED NULL DEFAULT NULL,
  `supplier_price`            DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `sell_price`                DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `price_override`            DECIMAL(12,2) NULL DEFAULT NULL,
  `status`                    TINYINT NOT NULL DEFAULT 1 COMMENT '1=InStock, 2=OutOfStock, 3=ComingSoon',
  `stock_status`              VARCHAR(20) NOT NULL DEFAULT 'in_stock',
  `primary_image_url`         VARCHAR(1000) NULL DEFAULT NULL,
  `image_400`                 VARCHAR(500) NULL DEFAULT NULL,
  `image_800`                 VARCHAR(500) NULL DEFAULT NULL,
  `image_1200`                VARCHAR(500) NULL DEFAULT NULL,
  `supplier_description_html` LONGTEXT NULL DEFAULT NULL,
  `description_html`          LONGTEXT NULL DEFAULT NULL,
  `short_description`         TEXT NULL DEFAULT NULL,
  `key_features`              JSON NULL DEFAULT NULL,
  `specs`                     JSON NULL DEFAULT NULL,
  `faq_json`                  JSON NULL DEFAULT NULL,
  `meta_title`                VARCHAR(255) NULL DEFAULT NULL,
  `meta_description`          VARCHAR(400) NULL DEFAULT NULL,
  `target_keyword`            VARCHAR(255) NULL DEFAULT NULL,
  `content_state`             ENUM('supplier_raw','ai_draft','approved') NOT NULL DEFAULT 'supplier_raw',
  `is_active`                 TINYINT(1) NOT NULL DEFAULT 1,
  `is_published`              TINYINT(1) NOT NULL DEFAULT 1,
  `pinned`                    TINYINT(1) NOT NULL DEFAULT 0,
  `popularity_score`          INT UNSIGNED NOT NULL DEFAULT 0,
  `supplier_link`             VARCHAR(1000) NULL DEFAULT NULL COMMENT 'Admin-only supplier link, NEVER public',
  `content_hash`              CHAR(32) NULL DEFAULT NULL,
  `last_seen_at`              DATETIME NULL DEFAULT NULL,
  `created_at`                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_products_slug` (`slug`),
  UNIQUE KEY `uk_products_supplier_id` (`supplier_id`),
  KEY `idx_products_cat_active_pop` (`category_id`, `is_active`, `popularity_score`),
  KEY `idx_products_brand` (`brand_id`),
  KEY `idx_products_status` (`status`),
  KEY `idx_products_content_state` (`content_state`),
  KEY `idx_products_source` (`source`),
  KEY `idx_products_sell_price` (`sell_price`),
  KEY `idx_products_created` (`created_at`),
  FULLTEXT KEY `ft_products_search` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. supplier_category_map
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `supplier_category_map` (
  `id`                         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_category_id`       VARCHAR(50) NOT NULL,
  `supplier_category_title`    VARCHAR(255) NOT NULL,
  `supplier_subcategory_id`    VARCHAR(50) NULL DEFAULT NULL,
  `supplier_subcategory_title` VARCHAR(255) NULL DEFAULT NULL,
  `site_category_id`           INT UNSIGNED NULL DEFAULT NULL,
  `auto_rule`                  VARCHAR(255) NULL DEFAULT NULL,
  `created_at`                 DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                 DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_scm_supplier_cat` (`supplier_category_id`, `supplier_subcategory_id`),
  KEY `idx_scm_site_cat` (`site_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. product_images
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `url`        VARCHAR(1000) NOT NULL,
  `local_path` VARCHAR(500) NULL,
  `alt_text`   VARCHAR(300) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pi_product` (`product_id`),
  KEY `idx_pi_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. orders
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number`     VARCHAR(50) NOT NULL,
  `customer_name`    VARCHAR(150) NOT NULL,
  `customer_phone`   VARCHAR(30) NOT NULL,
  `customer_email`   VARCHAR(150) NULL DEFAULT NULL,
  `city`             VARCHAR(100) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `customer_notes`   TEXT NULL,
  `subtotal`         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `delivery_charge`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount`         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total`            DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `payment_method`   VARCHAR(50) NOT NULL DEFAULT 'cod',
  `status`           ENUM('pending','confirmed','dispatched','delivered','cancelled','returned') NOT NULL DEFAULT 'pending',
  `courier_name`     VARCHAR(100) NULL DEFAULT NULL,
  `tracking_number`  VARCHAR(100) NULL DEFAULT NULL,
  `tracking_link`    VARCHAR(500) NULL DEFAULT NULL,
  `is_second_order`  TINYINT(1) NOT NULL DEFAULT 0,
  `ip_address`       VARCHAR(45) NULL,
  `user_agent`       VARCHAR(500) NULL,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_number` (`order_number`),
  KEY `idx_orders_phone` (`customer_phone`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. order_items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`      INT UNSIGNED NOT NULL,
  `product_id`    INT UNSIGNED NULL DEFAULT NULL,
  `product_title` VARCHAR(500) NOT NULL,
  `product_sku`   VARCHAR(100) NULL,
  `unit_price`    DECIMAL(12,2) NOT NULL,
  `quantity`      INT UNSIGNED NOT NULL DEFAULT 1,
  `total_price`   DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_oi_order` (`order_id`),
  KEY `idx_oi_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. banners
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `banners` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(255) NOT NULL,
  `subtitle`      VARCHAR(255) NULL,
  `desktop_image` VARCHAR(500) NOT NULL,
  `mobile_image`  VARCHAR(500) NULL,
  `link_url`      VARCHAR(500) NULL,
  `button_text`   VARCHAR(100) NULL,
  `alt_text`      VARCHAR(255) NULL,
  `sort_order`    INT NOT NULL DEFAULT 0,
  `start_date`    DATETIME NULL DEFAULT NULL,
  `end_date`      DATETIME NULL DEFAULT NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_banners_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. pages
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`             VARCHAR(150) NOT NULL,
  `title`            VARCHAR(255) NOT NULL,
  `content_html`     LONGTEXT NOT NULL,
  `meta_title`       VARCHAR(255) NULL,
  `meta_description` VARCHAR(400) NULL,
  `target_keyword`   VARCHAR(255) NULL,
  `is_active`        TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pages_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. authors
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `authors` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(150) NOT NULL,
  `slug`             VARCHAR(150) NOT NULL,
  `bio`              TEXT NULL,
  `avatar`           VARCHAR(500) NULL,
  `meta_title`       VARCHAR(255) NULL,
  `meta_description` VARCHAR(400) NULL,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_authors_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. posts (Guides)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `posts` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`             VARCHAR(200) NOT NULL,
  `title`            VARCHAR(255) NOT NULL,
  `author_id`        INT UNSIGNED NULL DEFAULT NULL,
  `featured_image`   VARCHAR(500) NULL,
  `excerpt`          TEXT NULL,
  `content_html`     LONGTEXT NOT NULL,
  `status`           ENUM('draft_needs_owner_review','published','archived') NOT NULL DEFAULT 'draft_needs_owner_review',
  `meta_title`       VARCHAR(255) NULL,
  `meta_description` VARCHAR(400) NULL,
  `target_keyword`   VARCHAR(255) NULL,
  `published_at`     DATETIME NULL DEFAULT NULL,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_posts_slug` (`slug`),
  KEY `idx_posts_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 12. redirects
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `redirects` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source_url`  VARCHAR(500) NOT NULL,
  `target_url`  VARCHAR(500) NOT NULL,
  `status_code` SMALLINT NOT NULL DEFAULT 301,
  `hits`        INT UNSIGNED NOT NULL DEFAULT 0,
  `last_hit_at` DATETIME NULL DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_redirects_source` (`source_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 13. log_404
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `log_404` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url`          VARCHAR(500) NOT NULL,
  `referrer`     VARCHAR(500) NULL,
  `ip_address`   VARCHAR(45) NULL,
  `user_agent`   VARCHAR(500) NULL,
  `hits`         INT UNSIGNED NOT NULL DEFAULT 1,
  `last_seen_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_to`  VARCHAR(500) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_log404_url` (`url`(255)),
  KEY `idx_log404_hits` (`hits`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 14. settings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `key`         VARCHAR(100) NOT NULL,
  `value`       LONGTEXT NULL,
  `description` VARCHAR(255) NULL,
  `group_name`  VARCHAR(50) NOT NULL DEFAULT 'general',
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 15. daily_hits
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `daily_hits` (
  `date`            DATE NOT NULL,
  `product_id`      INT UNSIGNED NOT NULL,
  `views`           INT UNSIGNED NOT NULL DEFAULT 0,
  `add_to_cart`     INT UNSIGNED NOT NULL DEFAULT 0,
  `whatsapp_clicks` INT UNSIGNED NOT NULL DEFAULT 0,
  `orders`          INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`, `product_id`),
  KEY `idx_dh_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 16. sync_runs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sync_runs` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `started_at`        DATETIME NOT NULL,
  `finished_at`       DATETIME NULL DEFAULT NULL,
  `status`            ENUM('running','success','failed','aborted') NOT NULL DEFAULT 'running',
  `feed_count`        INT UNSIGNED NOT NULL DEFAULT 0,
  `imported_count`    INT UNSIGNED NOT NULL DEFAULT 0,
  `updated_count`     INT UNSIGNED NOT NULL DEFAULT 0,
  `unchanged_count`   INT UNSIGNED NOT NULL DEFAULT 0,
  `skipped_invalid`   INT UNSIGNED NOT NULL DEFAULT 0,
  `deactivated_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `error_message`     TEXT NULL,
  `log_details`       LONGTEXT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sr_started` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 17. users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                 VARCHAR(100) NOT NULL,
  `email`                VARCHAR(180) NOT NULL,
  `password_hash`        VARCHAR(255) NOT NULL,
  `role`                 ENUM('owner','staff') NOT NULL DEFAULT 'owner',
  `totp_secret`          VARCHAR(64) NULL DEFAULT NULL,
  `must_change_password` TINYINT(1) NOT NULL DEFAULT 0,
  `failed_logins`        TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until`         DATETIME NULL DEFAULT NULL,
  `last_login_at`        DATETIME NULL DEFAULT NULL,
  `last_login_ip`        VARCHAR(45) NULL,
  `created_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 18. audit_log
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NULL,
  `action`      VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NULL,
  `entity_id`   VARCHAR(50) NULL,
  `details`     JSON NULL,
  `ip_address`  VARCHAR(45) NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_al_user` (`user_id`),
  KEY `idx_al_action` (`action`),
  KEY `idx_al_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 19. ai_queue
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_queue` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`    INT UNSIGNED NOT NULL,
  `status`        ENUM('pending','processing','draft_ready','failed','approved') NOT NULL DEFAULT 'pending',
  `attempts`      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `raw_input`     JSON NULL,
  `ai_output`     JSON NULL,
  `error_message` TEXT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ai_queue_product` (`product_id`),
  KEY `idx_ai_queue_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
