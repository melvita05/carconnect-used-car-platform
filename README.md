🚗 Online Car Connect – README
📌 Project Overview

Online Car Connect is a web-based platform that allows users to buy and sell used cars online.
The system removes middlemen and provides a transparent marketplace where buyers and sellers can interact directly.

The platform includes features like car listing, chat with sellers, wishlist, admin approval system, and dashboard analytics.

🎯 Objectives

Provide a digital marketplace for used car trading.

Allow sellers to list cars with images and details.

Allow buyers to browse cars and contact sellers.

Provide admin control for listing approval.

Enable buyer–seller communication through chat.

Implement a dummy checkout/payment system for demonstration.

🧰 Technologies Used
Frontend

HTML5

CSS3

JavaScript

Backend

PHP (Core PHP)

Database

MySQL

Tools

XAMPP

phpMyAdmin

Visual Studio Code

⚙️ System Modules
👨‍💼 Admin Module

Admin manages the entire platform.

Features:

Admin login

Approve / reject car listings

Delete car listings

Manage users

View analytics

Monitor transactions

🧑‍💻 Seller Module

Sellers can list cars and manage listings.

Features:

Seller registration and login

Add car listing with images

Edit car details

Manage listings

View buyer messages

View orders

🧑 Buyer Module

Buyers can search and purchase cars.

Features:

Buyer registration and login

Browse cars by brand

View car details

Add cars to wishlist

Chat with sellers

Dummy checkout/payment

💬 Chat System

Buyers can communicate with sellers regarding car details.

Features:

Buyer sends message to seller

Seller replies to buyer

Messages stored in database

Conversation linked to car listing

💳 Dummy Payment System

A simulated checkout process is implemented.

Features:

Buyer clicks Buy Now

Order stored in database

Payment marked as paid (demo)

📁 Project Folder Structure
carconnect
│
├── admin
│   ├── admin_dashboard.php
│   ├── manage_cars.php
│   ├── manage_users.php
│   └── view_reports.php
│
├── buyer
│   ├── home.php
│   ├── car_listings.php
│   ├── car_details.php
│   ├── wishlist.php
│   ├── chat.php
│   └── checkout.php
│
├── seller
│   ├── seller_dashboard.php
│   ├── add_car.php
│   ├── edit_car.php
│   ├── manage_listings.php
│   ├── view_orders.php
│   └── chat.php
│
├── includes
│   ├── db_connect.php
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── assets
│   ├── css
│   ├── js
│   └── images
│
├── uploads
│
└── index.php
🗄 Database Setup

Open phpMyAdmin → SQL → Run the following script

📄 SQL Database Script
CREATE DATABASE online_car_connect;
USE online_car_connect;

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
email VARCHAR(100) UNIQUE,
password VARCHAR(255),
role ENUM('admin','buyer','seller'),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE car_listings (
id INT AUTO_INCREMENT PRIMARY KEY,
seller_id INT,
make VARCHAR(100),
model VARCHAR(100),
year INT,
price DECIMAL(10,2),
mileage INT,
fuel_type VARCHAR(50),
transmission VARCHAR(50),
description TEXT,
image_path VARCHAR(255),
status VARCHAR(20) DEFAULT 'pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE wishlist (
id INT AUTO_INCREMENT PRIMARY KEY,
buyer_id INT,
car_id INT
);

CREATE TABLE messages (
id INT AUTO_INCREMENT PRIMARY KEY,
car_id INT,
sender_id INT,
receiver_id INT,
message TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
id INT AUTO_INCREMENT PRIMARY KEY,
car_id INT,
buyer_id INT,
seller_id INT,
price DECIMAL(10,2),
payment_status VARCHAR(20),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
🔑 Default Admin Login
Email: admin@carconnect.com
Password: admin123

Insert manually if needed:

INSERT INTO users(name,email,password,role)
VALUES('Admin','admin@carconnect.com','admin123','admin');
▶️ How to Run the Project

Install XAMPP

Start Apache & MySQL

Place project in:

htdocs/carconnect

Import database in phpMyAdmin

Open browser

http://localhost/carconnect
🚀 Future Enhancements

Real-time chat system

AI-based car recommendations

GPS-based nearby sellers

Advanced analytics dashboard

Mobile app version

Vehicle history verification

📜 Conclusion

Online Car Connect simplifies the process of buying and selling used cars by providing a transparent and secure digital platform.
The system reduces paperwork, eliminates middlemen, and improves communication between buyers and sellers.