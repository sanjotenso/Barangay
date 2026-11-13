CREATE DATABASE IF NOT EXISTS barangay_id_scanner
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE barangay_id_scanner;

CREATE TABLE IF NOT EXISTS residents (
  id VARCHAR(32) NOT NULL,
  id_number VARCHAR(100) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  address VARCHAR(500) NULL,
  contact VARCHAR(50) NULL,
  photo_path VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_residents_id_number (id_number)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS attendance_logs (
  id VARCHAR(32) NOT NULL,
  resident_id VARCHAR(32) NULL,
  resident_id_number VARCHAR(100) NOT NULL,
  resident_name VARCHAR(255) NOT NULL,
  occurred_at DATETIME(3) NOT NULL,
  purpose ENUM('visitor', 'event', 'certificate') NOT NULL,
  status ENUM('in', 'out') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_attendance_logs_occurred_at (occurred_at),
  KEY idx_attendance_logs_resident_time (resident_id, occurred_at),
  CONSTRAINT fk_attendance_logs_resident
    FOREIGN KEY (resident_id) REFERENCES residents (id)
    ON DELETE SET NULL
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
