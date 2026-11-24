CREATE DATABASE IF NOT EXISTS school_db;
USE school_db;

DROP TABLE IF EXISTS students;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    course VARCHAR(50) NOT NULL
);

INSERT INTO students (name, course) VALUES
('Juan Dela Cruz', 'BSCS'),
('Maria Santos', 'BSIT');
