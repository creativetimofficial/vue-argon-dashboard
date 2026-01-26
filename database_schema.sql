-- =============================================
-- ISP BILLING SYSTEM - DATABASE SCHEMA
-- =============================================

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- ============= USERS & AUTHENTICATION =============

-- Base Users Table (Polymorphic)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'isp_admin', 'technician', 'customer') NOT NULL,
    userable_type VARCHAR(255) NOT NULL, -- Polymorphic type
    userable_id BIGINT UNSIGNED NOT NULL, -- Polymorphic ID
    email_verified_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_userable (userable_type, userable_id)
);

-- Super Admins
CREATE TABLE super_admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ISPs (Companies)
CREATE TABLE isps (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    logo VARCHAR(255),
    website VARCHAR(255),
    
    -- Subscription Info
    package_id BIGINT UNSIGNED, -- Super Admin package
    subscription_status ENUM('trial', 'active', 'suspended', 'expired') DEFAULT 'trial',
    subscription_start_date DATE,
    subscription_end_date DATE,
    
    -- Business Info
    tax_id VARCHAR(50), -- NPWP
    business_license VARCHAR(100),
    
    -- Status
    is_verified BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    verified_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (package_id) REFERENCES super_admin_packages(id) ON DELETE SET NULL,
    INDEX idx_subscription_status (subscription_status),
    INDEX idx_is_active (is_active)
);

-- ISP Admins
CREATE TABLE isp_admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isp_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    position VARCHAR(100),
    is_primary BOOLEAN DEFAULT FALSE, -- Primary admin of ISP
    permissions JSON, -- Custom permissions
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    INDEX idx_isp_id (isp_id)
);

-- Technicians
CREATE TABLE technicians (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isp_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    avatar VARCHAR(255),
    
    -- Work Info
    employee_id VARCHAR(50),
    specialization VARCHAR(100), -- installation, repair, maintenance
    status ENUM('available', 'busy', 'off_duty') DEFAULT 'available',
    
    -- Location
    address TEXT,
    city VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    
    -- Performance
    total_jobs INT DEFAULT 0,
    completed_jobs INT DEFAULT 0,
    average_rating DECIMAL(3, 2) DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    INDEX idx_isp_id (isp_id),
    INDEX idx_status (status)
);

-- Customers
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isp_id BIGINT UNSIGNED NOT NULL,
    customer_code VARCHAR(50) UNIQUE NOT NULL, -- Auto-generated
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    avatar VARCHAR(255),
    
    -- Identity
    id_card_number VARCHAR(50),
    id_card_photo VARCHAR(255),
    
    -- Installation Address
    installation_address TEXT NOT NULL,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    
    -- Billing Address (if different)
    billing_address TEXT,
    billing_city VARCHAR(100),
    billing_province VARCHAR(100),
    billing_postal_code VARCHAR(10),
    
    -- Service Status
    service_status ENUM('pending', 'active', 'suspended', 'terminated') DEFAULT 'pending',
    installation_date DATE,
    activation_date DATE,
    suspension_date DATE,
    termination_date DATE,
    
    -- Mikrotik Info
    pppoe_username VARCHAR(100) UNIQUE,
    pppoe_password VARCHAR(255),
    ip_address VARCHAR(50),
    mac_address VARCHAR(50),
    
    -- Notes
    notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    INDEX idx_isp_id (isp_id),
    INDEX idx_customer_code (customer_code),
    INDEX idx_service_status (service_status)
);

-- ============= PACKAGES & SUBSCRIPTIONS =============

-- Super Admin Packages (for ISPs)
CREATE TABLE super_admin_packages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    
    -- Limits
    max_customers INT NOT NULL, -- -1 for unlimited
    max_admins INT DEFAULT 5,
    max_technicians INT DEFAULT 10,
    
    -- Features
    features JSON, -- Array of features
    
    -- Pricing
    price DECIMAL(15, 2) NOT NULL,
    billing_cycle ENUM('monthly', 'quarterly', 'semi_annually', 'annually') DEFAULT 'monthly',
    trial_days INT DEFAULT 0,
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_active (is_active)
);

-- Customer Packages (Internet Packages by ISP)
CREATE TABLE customer_packages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isp_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    
    -- Internet Specs
    download_speed INT NOT NULL, -- in Mbps
    upload_speed INT NOT NULL, -- in Mbps
    quota INT DEFAULT -1, -- in GB, -1 for unlimited
    
    -- Mikrotik Profile
    mikrotik_profile_name VARCHAR(100),
    
    -- Pricing
    price DECIMAL(15, 2) NOT NULL,
    installation_fee DECIMAL(15, 2) DEFAULT 0,
    
    -- Billing
    billing_cycle ENUM('daily', 'weekly', 'monthly', 'quarterly', 'semi_annually', 'annually') DEFAULT 'monthly',
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    INDEX idx_isp_id (isp_id),
    INDEX idx_is_active (is_active)
);

-- ISP Subscriptions (ISP to Super Admin)
CREATE TABLE isp_subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isp_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED NOT NULL,
    
    -- Subscription Period
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    
    -- Status
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    
    -- Payment
    amount DECIMAL(15, 2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES super_admin_packages(id) ON DELETE RESTRICT,
    INDEX idx_isp_id (isp_id),
    INDEX idx_status (status)
);

-- Customer Subscriptions
CREATE TABLE customer_subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED NOT NULL,
    
    -- Subscription Period
    start_date DATE NOT NULL,
    end_date DATE,
    
    -- Status
    status ENUM('active', 'suspended', 'cancelled') DEFAULT 'active',
    
    -- Auto-renewal
    auto_renew BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES customer_packages(id) ON DELETE RESTRICT,
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status)
);

-- ============= BILLING & PAYMENTS =============

-- Invoices
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Recipient (Polymorphic)
    billable_type VARCHAR(255) NOT NULL, -- ISP or Customer
    billable_id BIGINT UNSIGNED NOT NULL,
    isp_id BIGINT UNSIGNED, -- For filtering
    
    -- Invoice Details
    subscription_id BIGINT UNSIGNED,
    
    -- Amounts
    subtotal DECIMAL(15, 2) NOT NULL,
    tax DECIMAL(15, 2) DEFAULT 0,
    discount DECIMAL(15, 2) DEFAULT 0,
    total DECIMAL(15, 2) NOT NULL,
    
    -- Payment Info
    payment_status ENUM('unpaid', 'partial', 'paid', 'overdue', 'cancelled') DEFAULT 'unpaid',
    paid_amount DECIMAL(15, 2) DEFAULT 0,
    
    -- Dates
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    paid_at TIMESTAMP NULL,
    
    -- Period
    period_start DATE,
    period_end DATE,
    
    -- Notes
    notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_billable (billable_type, billable_id),
    INDEX idx_isp_id (isp_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_due_date (due_date)
);

-- Invoice Items
CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(15, 2) NOT NULL,
    total DECIMAL(15, 2) NOT NULL,
    
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice_id (invoice_id)
);

-- Payments
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_code VARCHAR(50) UNIQUE NOT NULL,
    invoice_id BIGINT UNSIGNED NOT NULL,
    isp_id BIGINT UNSIGNED, -- For filtering
    
    -- Payment Details
    amount DECIMAL(15, 2) NOT NULL,
    payment_method ENUM('gateway', 'cash', 'transfer') NOT NULL,
    
    -- Payment Gateway Info
    gateway_id BIGINT UNSIGNED NULL,
    gateway_transaction_id VARCHAR(255),
    gateway_response JSON,
    
    -- Manual Payment Info (Cash/Transfer)
    payment_proof VARCHAR(255), -- For transfer
    payment_note TEXT,
    received_by BIGINT UNSIGNED NULL, -- User ID who confirmed
    
    -- Status
    status ENUM('pending', 'success', 'failed', 'cancelled') DEFAULT 'pending',
    
    -- Dates
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmed_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (gateway_id) REFERENCES payment_gateways(id) ON DELETE SET NULL,
    INDEX idx_payment_code (payment_code),
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_isp_id (isp_id),
    INDEX idx_status (status)
);

-- Payment Gateways
CREATE TABLE payment_gateways (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- Midtrans, Xendit, etc
    provider ENUM('midtrans', 'xendit', 'custom') NOT NULL,
    
    -- Configuration
    config JSON NOT NULL, -- API keys, etc (encrypted)
    
    -- Supported Methods
    supported_methods JSON, -- ['credit_card', 'bank_transfer', 'e_wallet', ...]
    
    -- Fee Settings
    fee_type ENUM('fixed', 'percentage', 'both') DEFAULT 'percentage',
    fixed_fee DECIMAL(15, 2) DEFAULT 0,
    percentage_fee DECIMAL(5, 2) DEFAULT 0,
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_primary BOOLEAN DEFAULT FALSE,
    
    -- Scope (null for super admin, isp_id for ISP specific)
    isp_id BIGINT UNSIGNED NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    INDEX idx_is_active (is_active),
    INDEX idx_isp_id (isp_id)
);

-- ============= OPERATIONS =============

-- Installation Requests
CREATE TABLE installation_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_number VARCHAR(50) UNIQUE NOT NULL,
    isp_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    technician_id BIGINT UNSIGNED NULL,
    
    -- Request Details
    package_id BIGINT UNSIGNED NOT NULL,
    installation_address TEXT NOT NULL,
    preferred_date DATE,
    preferred_time TIME,
    
    -- Status
    status ENUM('pending', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    
    -- Assignment
    assigned_at TIMESTAMP NULL,
    assigned_by BIGINT UNSIGNED NULL, -- ISP Admin user ID
    
    -- Completion
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    completion_notes TEXT,
    completion_photos JSON, -- Array of photo URLs
    
    -- Customer Feedback
    customer_rating INT, -- 1-5
    customer_feedback TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES technicians(id) ON DELETE SET NULL,
    FOREIGN KEY (package_id) REFERENCES customer_packages(id) ON DELETE RESTRICT,
    INDEX idx_request_number (request_number),
    INDEX idx_isp_id (isp_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_technician_id (technician_id),
    INDEX idx_status (status)
);

-- Repair Tickets
CREATE TABLE repair_tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(50) UNIQUE NOT NULL,
    isp_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    technician_id BIGINT UNSIGNED NULL,
    
    -- Issue Details
    issue_type ENUM('no_connection', 'slow_speed', 'intermittent', 'equipment_damage', 'other') NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    description TEXT NOT NULL,
    photos JSON, -- Array of photo URLs
    
    -- Status
    status ENUM('open', 'assigned', 'in_progress', 'resolved', 'closed', 'cancelled') DEFAULT 'open',
    
    -- Assignment
    assigned_at TIMESTAMP NULL,
    assigned_by BIGINT UNSIGNED NULL,
    
    -- Resolution
    started_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    resolution_notes TEXT,
    resolution_photos JSON,
    
    -- Customer Feedback
    customer_rating INT,
    customer_feedback TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES technicians(id) ON DELETE SET NULL,
    INDEX idx_ticket_number (ticket_number),
    INDEX idx_isp_id (isp_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_technician_id (technician_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
);

-- Complaints
CREATE TABLE complaints (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    complaint_number VARCHAR(50) UNIQUE NOT NULL,
    isp_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    
    -- Complaint Details
    category ENUM('billing', 'service', 'technical', 'staff', 'other') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    attachments JSON, -- Array of file URLs
    
    -- Status
    status ENUM('open', 'in_review', 'resolved', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    
    -- Response
    response TEXT,
    responded_by BIGINT UNSIGNED NULL,
    responded_at TIMESTAMP NULL,
    
    -- Resolution
    resolved_at TIMESTAMP NULL,
    resolution TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_complaint_number (complaint_number),
    INDEX idx_isp_id (isp_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status)
);

-- ============= MIKROTIK =============

-- Mikrotik Routers
CREATE TABLE mikrotik_routers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isp_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    host VARCHAR(255) NOT NULL,
    port INT DEFAULT 8728,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL, -- Encrypted
    
    -- Router Info
    location VARCHAR(255),
    description TEXT,
    router_type ENUM('main', 'distribution', 'access') DEFAULT 'main',
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    last_connected_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    INDEX idx_isp_id (isp_id)
);

-- ============= CUSTOMIZATION =============

-- Web Customizations (for Super Admin)
CREATE TABLE web_customizations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Branding
    site_name VARCHAR(255) DEFAULT 'ISP Billing System',
    site_logo VARCHAR(255),
    site_favicon VARCHAR(255),
    
    -- Colors
    primary_color VARCHAR(7) DEFAULT '#5e72e4',
    secondary_color VARCHAR(7) DEFAULT '#f5365c',
    
    -- Homepage
    hero_title VARCHAR(255),
    hero_subtitle TEXT,
    hero_image VARCHAR(255),
    
    -- Features
    features JSON, -- Array of features to display
    
    -- Contact
    contact_email VARCHAR(255),
    contact_phone VARCHAR(20),
    contact_address TEXT,
    
    -- Social Media
    facebook_url VARCHAR(255),
    twitter_url VARCHAR(255),
    instagram_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    
    -- SEO
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    
    -- Footer
    footer_text TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============= NOTIFICATIONS =============

-- Notifications
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Notification Details
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    
    -- Action
    action_url VARCHAR(255),
    action_text VARCHAR(100),
    
    -- Status
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
);

-- ============= AUDIT LOGS =============

-- Activity Logs
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    isp_id BIGINT UNSIGNED,
    
    -- Activity Details
    action VARCHAR(100) NOT NULL, -- create, update, delete, login, etc
    model VARCHAR(255), -- Model name
    model_id BIGINT UNSIGNED,
    
    -- Changes
    old_values JSON,
    new_values JSON,
    
    -- Request Info
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_isp_id (isp_id),
    INDEX idx_created_at (created_at)
);

-- ============= SETTINGS =============

-- System Settings
CREATE TABLE system_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_key (`key`)
);

-- ISP Settings
CREATE TABLE isp_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    isp_id BIGINT UNSIGNED NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    value TEXT,
    type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (isp_id) REFERENCES isps(id) ON DELETE CASCADE,
    UNIQUE KEY unique_isp_key (isp_id, `key`),
    INDEX idx_isp_id (isp_id)
);

-- ============= INITIAL DATA =============

-- Insert default web customization
INSERT INTO web_customizations (
    site_name,
    hero_title,
    hero_subtitle,
    primary_color,
    secondary_color
) VALUES (
    'ISP Billing System',
    'Kelola Bisnis ISP Anda dengan Mudah',
    'Platform billing ISP terlengkap dengan fitur manajemen pelanggan, integrasi Mikrotik, dan payment gateway',
    '#5e72e4',
    '#f5365c'
);

-- Insert default super admin
INSERT INTO super_admins (name, phone) VALUES ('Super Administrator', '081234567890');

INSERT INTO users (email, password, role, userable_type, userable_id, email_verified_at, is_active)
VALUES (
    'superadmin@ispbilling.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'super_admin',
    'App\\Models\\SuperAdmin',
    1,
    NOW(),
    TRUE
);

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
