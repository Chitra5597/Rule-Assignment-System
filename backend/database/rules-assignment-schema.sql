-- SQL script to create the database schema for the Rule Assignment System
-- Create Database
CREATE DATABASE rule_assignment_system;
USE rule_assignment_system;

-- Rules table to store all rules
CREATE TABLE rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    type VARCHAR(50) NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Groups table to store rule groups
CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Assignments table for storing rule to group relationships
CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    rule_id INT NOT NULL,
    parent_id INT,
    level INT NOT NULL,
    order_num INT NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id),
    FOREIGN KEY (rule_id) REFERENCES rules(id),
    FOREIGN KEY (parent_id) REFERENCES assignments(id)
);

-- Create some indexes for better performance
CREATE INDEX idx_group_id ON assignments(group_id);
CREATE INDEX idx_rule_id ON assignments(rule_id);
CREATE INDEX idx_parent_id ON assignments(parent_id);
CREATE INDEX idx_level ON assignments(level);