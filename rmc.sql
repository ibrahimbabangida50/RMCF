-- Updated Users table (unchanged)
CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(15) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    registration_type VARCHAR(50) NOT NULL,
    contribution_class VARCHAR(50) NOT NULL,
    monthly_amount INT NOT NULL,
    district VARCHAR(100) NOT NULL
);

-- Enhanced Contributions table
CREATE TABLE IF NOT EXISTS Contributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(15) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reference_id VARCHAR(50),
    status ENUM('pending','completed','failed') DEFAULT 'pending',
    momo_response TEXT,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (phone) REFERENCES Users(phone)
);

-- Enhanced Sadaqh table
CREATE TABLE IF NOT EXISTS Sadaqh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    district VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    reference_id VARCHAR(50),
    status ENUM('pending','completed','failed') DEFAULT 'pending',
    momo_response TEXT,
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Enhanced Zakat table
CREATE TABLE IF NOT EXISTS Zakat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    district VARCHAR(100),
    Zakat_type VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    reference_id VARCHAR(50),
    status ENUM('pending','completed','failed') DEFAULT 'pending',
    momo_response TEXT,
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transaction Logs table (new)
CREATE TABLE IF NOT EXISTS TransactionLogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('contribution','sadaqah','zakat_fitr','zakat_mal') NOT NULL,
    status ENUM('initiated','pending','completed','failed') DEFAULT 'initiated',
    reference_id VARCHAR(50),
    raw_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL
);

-- PendingPayments table (updated)
CREATE TABLE IF NOT EXISTS PendingPayments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reference_id VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(50),
    status ENUM('initiated','pending','completed','failed') DEFAULT 'initiated',
    payment_type ENUM('contribution','sadaqah','zakat') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);
-- Table for Masjid information
CREATE TABLE IF NOT EXISTS masjid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    masjid_number VARCHAR(20) NOT NULL UNIQUE,
    masjid_name VARCHAR(100) NOT NULL,
    province VARCHAR(50),
    district VARCHAR(50),
    sector VARCHAR(50),
    cell VARCHAR(50),
    imam VARCHAR(100),
    telephone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Sadaqh for Masjid transactions
CREATE TABLE IF NOT EXISTS Sadaqhformasjid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    donor_district VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    masjid_number VARCHAR(20) NOT NULL,
    masjid_name VARCHAR(100) NOT NULL,
    masjid_district VARCHAR(50),
    masjid_sector VARCHAR(50),
    donation_date DATETIME NOT NULL,
    status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (masjid_number) REFERENCES masjid(masjid_number)
);
