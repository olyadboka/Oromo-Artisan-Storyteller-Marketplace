<?php
include 'dbConnection/dbConnection.php';

// users table..................
$sql0 = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'artisan', 'storyteller', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($con->query($sql0)) {
    echo "Table users created successfully.<br>";
} else {
    echo "Error creating users table: ";
}

// artisans table................
$sql1 = "CREATE TABLE  artisans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_name VARCHAR(100),
    bio TEXT,
    specialization VARCHAR(100),
    years_experience INT,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($con->query($sql1)) {
    echo "Table artisans created successfully.<br>";
} else {
    echo "Error creating artisans table: " ;
}

// products table............
$sql2 = "CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artisan_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category ENUM('jewelry', 'textiles', 'pottery', 'basketry', 'woodwork', 'other'),
    price DECIMAL(10,2) NOT NULL,
    quantity INT DEFAULT 1,
    materials VARCHAR(255),
    creation_time VARCHAR(50),
    is_featured BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artisan_id) REFERENCES artisans(id) ON DELETE CASCADE
)";

if ($con->query($sql2)) {
    echo "Table products created successfully.<br>";
} else {
    echo "Error creating products table: " ;
}

// product_images table......
$sql3 = "CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT false,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if ($con->query($sql3)) {
    echo "Table product_images created successfully.<br>";
} else {
    echo "Error creating product_images table: ";
}

//  storytellers table..........
$sql4 = "CREATE TABLE  storytellers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    artistic_name VARCHAR(100),
    bio TEXT,
    specialization ENUM('folklore', 'history', 'mythology', 'community_stories'),
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($con->query($sql4)) {
    echo "Table storytellers created successfully.<br>";
} else {
    echo "Error creating storytellers table: " ;
}

// stories table.............
$sql5 = "CREATE TABLE stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    storyteller_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    media_type ENUM('audio', 'video', 'text') NOT NULL,
    media_url VARCHAR(255) NOT NULL,
    duration INT,
    word_count INT,
    language ENUM('Afaan Oromo', 'English', 'Amharic') NOT NULL,
    age_group ENUM('all', 'children', 'adults') DEFAULT 'all',
    is_featured BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (storyteller_id) REFERENCES storytellers(id) ON DELETE CASCADE
)";

if ($con->query($sql5)) {
    echo "Table stories created successfully.<br>";
} else {
    echo "Error creating stories table: " ;
}

// story_themes table .............
$sql6 = "CREATE TABLE  story_themes (
    story_id INT NOT NULL,
    theme VARCHAR(50) NOT NULL,
    PRIMARY KEY (story_id, theme),
    FOREIGN KEY (story_id) REFERENCES stories(id) ON DELETE CASCADE
)";

if ($con->query($sql6)) {
    echo "Table story_themes created successfully.<br>";
} else {
    echo "Error creating story_themes table: ";
}

//  story_transcriptions table ..............
$sql7 = "CREATE TABLE story_transcriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT NOT NULL,
    language ENUM('Afaan Oromo', 'English', 'Amharic') NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (story_id) REFERENCES stories(id) ON DELETE CASCADE
)";

if ($con->query($sql7)) {
    echo "Table story_transcriptions created successfully.<br>";
} else {
    echo "Error creating story_transcriptions table: " ;
}