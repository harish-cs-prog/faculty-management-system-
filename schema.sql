CREATE DATABASE IF NOT EXISTS faculty_engagement
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE faculty_engagement;

CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  department_name VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  role ENUM('admin','faculty') NOT NULL DEFAULT 'faculty',
  department_id INT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_user_department FOREIGN KEY (department_id)
    REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS engagement (
  id INT AUTO_INCREMENT PRIMARY KEY,
  faculty_id INT NOT NULL,
  engagement_date DATE NOT NULL,
  period_name VARCHAR(40) NOT NULL,
  status ENUM('Engaged','Not Engaged') NOT NULL,
  remarks VARCHAR(255) NULL,
  entered_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_faculty_date_period (faculty_id, engagement_date, period_name),
  KEY idx_engagement_date (engagement_date),
  CONSTRAINT fk_eng_faculty FOREIGN KEY (faculty_id) REFERENCES users(id),
  CONSTRAINT fk_eng_entered_by FOREIGN KEY (entered_by) REFERENCES users(id)
) ENGINE=InnoDB;
