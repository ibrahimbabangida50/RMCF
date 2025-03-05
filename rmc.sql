-- Create the Users table
CREATE TABLE Users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    phone VARCHAR(15) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    registration_type VARCHAR(50) NOT NULL,
    contribution_class VARCHAR(50) NOT NULL,
    monthly_amount INT NOT NULL,
    district VARCHAR(100) NOT NULL
);

-- Create the Contributions table
CREATE TABLE Contributions (
    id INT IDENTITY(1,1) PRIMARY KEY,
    phone VARCHAR(15) NOT NULL,
    amount INT NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (phone) REFERENCES Users(phone)
);

-- Create the Sadaqh table
CREATE TABLE Sadaqh (
    id INT IDENTITY(1,1) PRIMARY KEY,
    donor_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    district VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    donation_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create the Zakat table
CREATE TABLE Zakat (
    id INT IDENTITY(1,1) PRIMARY KEY,
    donor_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    district VARCHAR(100),
    Zakat_type VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    donation_date DATETIME DEFAULT CURRENT_TIMESTAMP
);
