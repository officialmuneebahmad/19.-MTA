-- Default Seed Settings for Multan Tech Accessories
INSERT INTO `settings` (`key`, `value`, `description`, `group_name`) VALUES
('site_name', 'Multan Tech Accessories', 'Store Name', 'general'),
('site_url', 'https://multantech.store', 'Canonical Site URL', 'general'),
('phone_display', '0307-0655275', 'Display Phone / WhatsApp', 'contact'),
('phone_raw', '+923070655275', 'International Dialing Format', 'contact'),
('whatsapp_number', '923070655275', 'WhatsApp International Digits', 'contact'),
('store_address', 'Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan', 'Physical Store Address', 'contact'),
('founder_credit', 'Founded & Developed by Muneeb Ahmad', 'Founder & Developer Credit', 'general'),
('announcement_strip', 'FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY CHARGES ONLY FOR MULTANIS', 'Top Announcement Marquee Strip', 'general'),
('delivery_free_threshold', '5000', 'Free delivery minimum cart total (PKR)', 'delivery'),
('delivery_multan_charge', '100', 'Multan local delivery fee (PKR)', 'delivery'),
('delivery_standard_charge', '250', 'Pakistan standard delivery fee (PKR)', 'delivery'),
('markup_percent', '10', 'Default markup percentage', 'pricing'),
('markup_fixed', '0', 'Default fixed markup (PKR)', 'pricing'),
('markup_round_to', '10', 'Round selling price to nearest multiple', 'pricing'),
('supplier_api_url', 'http://127.0.0.1:8080/products', 'Supplier Products API Endpoint', 'supplier'),
('supplier_auth_header_format', 'raw', 'Supplier Auth format: raw or bearer', 'supplier'),
('supplier_token', 'TODO-OWNER', 'Supplier API Secret Key / Token', 'supplier'),
('admin_path', 'mta-control-9872', 'Obfuscated Admin Dashboard Path', 'security'),
('adsense_enabled', '0', '1 to enable AdSense AdSlots', 'adsense'),
('adsense_publisher_id', '', 'AdSense Publisher ID (e.g. ca-pub-XXXX)', 'adsense'),
('ga4_measurement_id', '', 'Google Analytics 4 Measurement ID', 'analytics'),
('gsc_verification_tag', '', 'Google Search Console Verification Tag', 'analytics'),
('bing_verification_tag', '', 'Bing Webmaster Verification Tag', 'analytics'),
('social_instagram', 'https://instagram.com/TODO-OWNER', 'Instagram profile URL', 'social'),
('social_facebook', 'https://facebook.com/TODO-OWNER', 'Facebook page URL', 'social'),
('social_tiktok', 'https://tiktok.com/@TODO-OWNER', 'TikTok profile URL', 'social'),
('ai_provider', 'gemini', 'AI Provider for Rewrites: gemini, openrouter, or anthropic', 'ai'),
('ai_api_key', '', 'API Key for AI Content Engine', 'ai'),
('ai_daily_cap', '100', 'Maximum products to rewrite per day', 'ai'),
('author_name', 'Muneeb Ahmad', 'Default Post Author Name', 'editorial'),
('author_slug', 'muneeb-ahmad', 'Default Post Author Slug', 'editorial');

-- Insert Initial Author
INSERT INTO `authors` (`name`, `slug`, `bio`) VALUES
('Muneeb Ahmad', 'muneeb-ahmad', 'Founder and developer of Multan Tech Accessories. Passionate about authentic computer and mobile hardware, tech accessories, and bringing quality digital tools to Pakistani tech enthusiasts and professionals.');
