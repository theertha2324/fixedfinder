Fixed-Finder
sql commands:

1. create a database
CREATE DATABASE fixedfinder;
USE fixedfinder;

2. users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    password VARCHAR(255),
    role ENUM('admin','user','mechanic') DEFAULT 'user',
    location VARCHAR(255),
    garage_location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

3. mechanics table
CREATE TABLE mechanics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    status VARCHAR(50),
    latitude DOUBLE,
    longitude DOUBLE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

4. request table
CREATE TABLE requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    mechanic_id INT,
    problem TEXT,
    location VARCHAR(255),
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rating INT DEFAULT 0,
    feedback TEXT,
    user_phone VARCHAR(15),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE CASCADE
);

5. complaints table 
CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mechanic_id INT,
    complaint TEXT,
    image VARCHAR(255),
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    role VARCHAR(50),
    reviewed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
); 

6. (Optional) Insert Sample Data

Admin
INSERT INTO users (name, email, phone, password, role, location)
VALUES ('Admin', 'admin@gmail.com', '9999999999', 'admin123', 'admin', 'Puttur');

user
INSERT INTO users (name, email, phone, password, role, location)
VALUES ('Ramesh', 'ramesh@gmail.com', '1122334455', 'user123', 'user', 'Puttur');

mechanic user 
INSERT INTO users (name, email, phone, password, role, location, garage_location)
VALUES ('Thejas', 'thejas@gmail.com', '6666666666', 'mech123', 'mechanic', 'Puttur', 'Puttur Garage');

mechanic entry
INSERT INTO mechanics (user_id, status, latitude, longitude)
VALUES (3, 'available', 12.76, 75.20);

