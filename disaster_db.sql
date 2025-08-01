CREATE DATABASE disaster_db; 
USE disaster_db;

-- ✅ Users Table (for both admins and normal users)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Disasters Table
CREATE TABLE disasters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    type VARCHAR(50),
    location VARCHAR(100),
    date DATE,
    severity ENUM('Low', 'Medium', 'High'),
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- Rescue Teams Table
CREATE TABLE rescue_teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100),
    members_count INT,
    contact VARCHAR(50),
    managed_by INT,
    FOREIGN KEY (managed_by) REFERENCES users(user_id)
);

-- Victims Table
CREATE TABLE victims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    age INT,
    status ENUM('Safe', 'Injured', 'Missing'),
    disaster_id INT,
    assigned_team_id INT,
    FOREIGN KEY (disaster_id) REFERENCES disasters(id),
    FOREIGN KEY (assigned_team_id) REFERENCES rescue_teams(id)
);

-- Resources Table
CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_type VARCHAR(50),
    quantity INT,
    location VARCHAR(100),
    added_by INT,
    FOREIGN KEY (added_by) REFERENCES users(user_id)
);
