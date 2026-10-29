-- SQLite-compatible schema for local development and testing
CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  parent_id INTEGER NULL,
  slug TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  intro_html TEXT NULL,
  meta_title TEXT NULL,
  meta_description TEXT NULL,
  target_keyword TEXT NULL,
  sort_order INTEGER NOT NULL DEFAULT 0,
  pinned INTEGER NOT NULL DEFAULT 0,
  is_active INTEGER NOT NULL DEFAULT 1,
  product_count INTEGER NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS brands (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  supplier_brand_id TEXT NULL,
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  logo_url TEXT NULL,
  intro_html TEXT NULL,
  meta_title TEXT NULL,
  meta_description TEXT NULL,
  target_keyword TEXT NULL,
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  source TEXT NOT NULL DEFAULT 'supplier',
  source_name TEXT NULL,
  supplier_id TEXT NULL UNIQUE,
  sku TEXT NULL,
  slug TEXT NOT NULL UNIQUE,
  title TEXT NOT NULL,
  category_id INTEGER NULL,
  subcategory_title TEXT NULL,
  brand_id INTEGER NULL,
  supplier_price REAL NOT NULL DEFAULT 0.00,
  sell_price REAL NOT NULL DEFAULT 0.00,
  price_override REAL NULL,
  status INTEGER NOT NULL DEFAULT 1,
  stock_status TEXT NOT NULL DEFAULT 'in_stock',
  primary_image_url TEXT NULL,
  image_400 TEXT NULL,
  image_800 TEXT NULL,
  image_1200 TEXT NULL,
  supplier_description_html TEXT NULL,
  description_html TEXT NULL,
  short_description TEXT NULL,
  key_features TEXT NULL,
  specs TEXT NULL,
  faq_json TEXT NULL,
  meta_title TEXT NULL,
  meta_description TEXT NULL,
  target_keyword TEXT NULL,
  content_state TEXT NOT NULL DEFAULT 'supplier_raw',
  is_active INTEGER NOT NULL DEFAULT 1,
  is_published INTEGER NOT NULL DEFAULT 1,
  pinned INTEGER NOT NULL DEFAULT 0,
  popularity_score INTEGER NOT NULL DEFAULT 0,
  supplier_link TEXT NULL,
  content_hash TEXT NULL,
  last_seen_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS supplier_category_map (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  supplier_category_id TEXT NOT NULL,
  supplier_category_title TEXT NOT NULL,
  supplier_subcategory_id TEXT NULL,
  supplier_subcategory_title TEXT NULL,
  site_category_id INTEGER NULL,
  auto_rule TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_images (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  url TEXT NOT NULL,
  local_path TEXT NULL,
  alt_text TEXT NULL,
  sort_order INTEGER NOT NULL DEFAULT 0,
  is_primary INTEGER NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_number TEXT NOT NULL UNIQUE,
  customer_name TEXT NOT NULL,
  customer_phone TEXT NOT NULL,
  customer_email TEXT NULL,
  city TEXT NOT NULL,
  shipping_address TEXT NOT NULL,
  customer_notes TEXT NULL,
  subtotal REAL NOT NULL DEFAULT 0.00,
  delivery_charge REAL NOT NULL DEFAULT 0.00,
  discount REAL NOT NULL DEFAULT 0.00,
  total REAL NOT NULL DEFAULT 0.00,
  payment_method TEXT NOT NULL DEFAULT 'cod',
  status TEXT NOT NULL DEFAULT 'pending',
  courier_name TEXT NULL,
  tracking_number TEXT NULL,
  tracking_link TEXT NULL,
  is_second_order INTEGER NOT NULL DEFAULT 0,
  ip_address TEXT NULL,
  user_agent TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL,
  product_id INTEGER NULL,
  product_title TEXT NOT NULL,
  product_sku TEXT NULL,
  unit_price REAL NOT NULL,
  quantity INTEGER NOT NULL DEFAULT 1,
  total_price REAL NOT NULL
);

CREATE TABLE IF NOT EXISTS banners (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  subtitle TEXT NULL,
  desktop_image TEXT NOT NULL,
  mobile_image TEXT NULL,
  link_url TEXT NULL,
  button_text TEXT NULL,
  alt_text TEXT NULL,
  sort_order INTEGER NOT NULL DEFAULT 0,
  start_date DATETIME NULL,
  end_date DATETIME NULL,
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  title TEXT NOT NULL,
  content_html TEXT NOT NULL,
  meta_title TEXT NULL,
  meta_description TEXT NULL,
  target_keyword TEXT NULL,
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS authors (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  bio TEXT NULL,
  avatar TEXT NULL,
  meta_title TEXT NULL,
  meta_description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  title TEXT NOT NULL,
  author_id INTEGER NULL,
  featured_image TEXT NULL,
  excerpt TEXT NULL,
  content_html TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'draft_needs_owner_review',
  meta_title TEXT NULL,
  meta_description TEXT NULL,
  target_keyword TEXT NULL,
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS redirects (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  source_url TEXT NOT NULL UNIQUE,
  target_url TEXT NOT NULL,
  status_code INTEGER NOT NULL DEFAULT 301,
  hits INTEGER NOT NULL DEFAULT 0,
  last_hit_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS log_404 (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  url TEXT NOT NULL,
  referrer TEXT NULL,
  ip_address TEXT NULL,
  user_agent TEXT NULL,
  hits INTEGER NOT NULL DEFAULT 1,
  last_seen_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved_to TEXT NULL
);

CREATE TABLE IF NOT EXISTS settings (
  key TEXT PRIMARY KEY,
  value TEXT NULL,
  description TEXT NULL,
  group_name TEXT NOT NULL DEFAULT 'general',
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS daily_hits (
  date TEXT NOT NULL,
  product_id INTEGER NOT NULL,
  views INTEGER NOT NULL DEFAULT 0,
  add_to_cart INTEGER NOT NULL DEFAULT 0,
  whatsapp_clicks INTEGER NOT NULL DEFAULT 0,
  orders INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (date, product_id)
);

CREATE TABLE IF NOT EXISTS sync_runs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  started_at DATETIME NOT NULL,
  finished_at DATETIME NULL,
  status TEXT NOT NULL DEFAULT 'running',
  feed_count INTEGER NOT NULL DEFAULT 0,
  imported_count INTEGER NOT NULL DEFAULT 0,
  updated_count INTEGER NOT NULL DEFAULT 0,
  unchanged_count INTEGER NOT NULL DEFAULT 0,
  skipped_invalid INTEGER NOT NULL DEFAULT 0,
  deactivated_count INTEGER NOT NULL DEFAULT 0,
  error_message TEXT NULL,
  log_details TEXT NULL
);

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'owner',
  totp_secret TEXT NULL,
  must_change_password INTEGER NOT NULL DEFAULT 0,
  failed_logins INTEGER NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  last_login_at DATETIME NULL,
  last_login_ip TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NULL,
  action TEXT NOT NULL,
  entity_type TEXT NULL,
  entity_id TEXT NULL,
  details TEXT NULL,
  ip_address TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ai_queue (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL UNIQUE,
  status TEXT NOT NULL DEFAULT 'pending',
  attempts INTEGER NOT NULL DEFAULT 0,
  raw_input TEXT NULL,
  ai_output TEXT NULL,
  error_message TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
