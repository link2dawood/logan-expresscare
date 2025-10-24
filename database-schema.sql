-- =====================================================
-- Staff Onboarding Database Schema
-- =====================================================
-- Description: Database schema for staff onboarding system
-- Created: 2025-10-11
-- =====================================================

-- Create database (optional)
-- CREATE DATABASE IF NOT EXISTS getgo_loganexpresscare;
-- USE getgo_loganexpresscare;

-- =====================================================
-- Staff Applications Table
-- =====================================================
CREATE TABLE IF NOT EXISTS staff_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Personal Information
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    position VARCHAR(50) NOT NULL,
    employment_type VARCHAR(20) NOT NULL,
    
    -- Contractor Details
    abn VARCHAR(20) DEFAULT NULL,
    business_name VARCHAR(100) DEFAULT NULL,
    
    -- Document Files (stored filenames)
    ndis_screening VARCHAR(255) DEFAULT NULL,
    children_check VARCHAR(255) DEFAULT NULL,
    children_check_expiry DATE DEFAULT NULL,
    
    passport VARCHAR(255) DEFAULT NULL,
    visa VARCHAR(255) DEFAULT NULL,
    drivers_license VARCHAR(255) DEFAULT NULL,
    
    ndis_orientation VARCHAR(255) DEFAULT NULL,
    
    meals_certificate VARCHAR(255) DEFAULT NULL,
    meals_certificate_expiry DATE DEFAULT NULL,
    
    ipc_certificate VARCHAR(255) DEFAULT NULL,
    ipc_certificate_expiry DATE DEFAULT NULL,
    
    first_aid_certificate VARCHAR(255) DEFAULT NULL,
    first_aid_expiry DATE DEFAULT NULL,
    cpr_expiry DATE DEFAULT NULL,
    
    resume VARCHAR(255) DEFAULT NULL,
    staff_contract VARCHAR(255) DEFAULT NULL,
    
    police_check VARCHAR(255) DEFAULT NULL,
    police_check_expiry DATE DEFAULT NULL,
    
    -- Acknowledgments
    ack_staff_details BOOLEAN DEFAULT FALSE,
    ack_induction BOOLEAN DEFAULT FALSE,
    ack_code_conduct BOOLEAN DEFAULT FALSE,
    ack_training_tracker BOOLEAN DEFAULT FALSE,
    ack_handbook BOOLEAN DEFAULT FALSE,
    ack_tax_declaration BOOLEAN DEFAULT FALSE,
    ack_bank_details BOOLEAN DEFAULT FALSE,
    final_consent BOOLEAN DEFAULT FALSE,
    
    -- Application Status
    status ENUM('pending', 'under_review', 'approved', 'rejected', 'incomplete') DEFAULT 'pending',
    reviewed_by INT DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Admin Users Table (for reviewing applications)
-- =====================================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'hr', 'manager') DEFAULT 'hr',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL,
    
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Application Notes/Comments Table
-- =====================================================
CREATE TABLE IF NOT EXISTS application_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    admin_user_id INT NOT NULL,
    note_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES staff_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE RESTRICT,
    
    INDEX idx_application_id (application_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Document Expiry Tracking Table
-- =====================================================
CREATE TABLE IF NOT EXISTS document_expiry_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    staff_email VARCHAR(100) NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    expiry_date DATE NOT NULL,
    reminder_sent BOOLEAN DEFAULT FALSE,
    reminder_sent_at DATETIME DEFAULT NULL,
    
    FOREIGN KEY (application_id) REFERENCES staff_applications(id) ON DELETE CASCADE,
    
    INDEX idx_application_id (application_id),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_reminder_sent (reminder_sent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Job Postings Table (for managing open positions)
-- =====================================================
CREATE TABLE IF NOT EXISTS job_postings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_title VARCHAR(100) NOT NULL,
    position_type VARCHAR(50) NOT NULL,
    employment_type VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT NOT NULL,
    salary_range VARCHAR(100) DEFAULT NULL,
    location VARCHAR(100) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    posted_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closes_at DATE DEFAULT NULL,
    
    FOREIGN KEY (posted_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    
    INDEX idx_is_active (is_active),
    INDEX idx_position_type (position_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert Sample Admin User
-- =====================================================
-- Password: admin123 (hashed with PASSWORD_DEFAULT in PHP)
-- IMPORTANT: Change this password immediately after first login
INSERT INTO admin_users (username, password, email, full_name, role, is_active)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin@loganexpresscare.com.au',
    'System Administrator',
    'admin',
    TRUE
);

-- =====================================================
-- Insert Sample Job Postings
-- =====================================================
INSERT INTO job_postings (position_title, position_type, employment_type, description, requirements, location, is_active)
VALUES 
(
    'Disability Support Worker',
    'disability_support_worker',
    'permanent',
    'We are seeking a compassionate and dedicated Disability Support Worker to join our team. You will provide high-quality support to NDIS participants in their daily activities.',
    '- NDIS Worker Screening Check\n- Working with Children Check\n- First Aid and CPR Certificate\n- Excellent communication skills\n- Valid driver''s license',
    'Logan, QLD',
    TRUE
),
(
    'Registered Nurse',
    'nurse',
    'casual',
    'Join our team as a Registered Nurse providing quality healthcare services to NDIS participants. Flexible casual hours available.',
    '- Current AHPRA Nursing Registration\n- NDIS Worker Screening Check\n- Working with Children Check\n- First Aid Certificate\n- Minimum 2 years experience',
    'Logan, QLD',
    TRUE
);

-- =====================================================
-- Views for Reporting
-- =====================================================

-- View: Applications pending review
CREATE OR REPLACE VIEW pending_applications AS
SELECT 
    sa.id,
    sa.first_name,
    sa.last_name,
    sa.email,
    sa.phone,
    sa.position,
    sa.employment_type,
    sa.status,
    sa.created_at
FROM staff_applications sa
WHERE sa.status = 'pending'
ORDER BY sa.created_at ASC;

-- View: Expiring documents in next 30 days
CREATE OR REPLACE VIEW expiring_documents AS
SELECT 
    sa.id as application_id,
    CONCAT(sa.first_name, ' ', sa.last_name) as staff_name,
    sa.email,
    'Children Check' as document_type,
    sa.children_check_expiry as expiry_date,
    DATEDIFF(sa.children_check_expiry, CURDATE()) as days_until_expiry
FROM staff_applications sa
WHERE sa.children_check_expiry IS NOT NULL 
    AND sa.children_check_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND sa.status = 'approved'

UNION ALL

SELECT 
    sa.id,
    CONCAT(sa.first_name, ' ', sa.last_name),
    sa.email,
    'First Aid Certificate',
    sa.first_aid_expiry,
    DATEDIFF(sa.first_aid_expiry, CURDATE())
FROM staff_applications sa
WHERE sa.first_aid_expiry IS NOT NULL 
    AND sa.first_aid_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND sa.status = 'approved'

UNION ALL

SELECT 
    sa.id,
    CONCAT(sa.first_name, ' ', sa.last_name),
    sa.email,
    'CPR Certificate',
    sa.cpr_expiry,
    DATEDIFF(sa.cpr_expiry, CURDATE())
FROM staff_applications sa
WHERE sa.cpr_expiry IS NOT NULL 
    AND sa.cpr_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND sa.status = 'approved'

UNION ALL

SELECT 
    sa.id,
    CONCAT(sa.first_name, ' ', sa.last_name),
    sa.email,
    'Police Check',
    sa.police_check_expiry,
    DATEDIFF(sa.police_check_expiry, CURDATE())
FROM staff_applications sa
WHERE sa.police_check_expiry IS NOT NULL 
    AND sa.police_check_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND sa.status = 'approved'

ORDER BY expiry_date ASC;

-- View: Application statistics
CREATE OR REPLACE VIEW application_statistics AS
SELECT 
    DATE(created_at) as application_date,
    COUNT(*) as total_applications,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
    SUM(CASE WHEN employment_type = 'permanent' THEN 1 ELSE 0 END) as permanent_count,
    SUM(CASE WHEN employment_type = 'casual' THEN 1 ELSE 0 END) as casual_count,
    SUM(CASE WHEN employment_type = 'contractor' THEN 1 ELSE 0 END) as contractor_count
FROM staff_applications
GROUP BY DATE(created_at)
ORDER BY application_date DESC;

-- =====================================================
-- Stored Procedures
-- =====================================================

DELIMITER //

-- Procedure: Get application details with all information
CREATE PROCEDURE GetApplicationDetails(IN app_id INT)
BEGIN
    SELECT 
        sa.*,
        au.full_name as reviewed_by_name,
        (SELECT COUNT(*) FROM application_notes WHERE application_id = app_id) as note_count
    FROM staff_applications sa
    LEFT JOIN admin_users au ON sa.reviewed_by = au.id
    WHERE sa.id = app_id;
END //

-- Procedure: Approve application
CREATE PROCEDURE ApproveApplication(
    IN app_id INT,
    IN admin_id INT,
    IN review_notes TEXT
)
BEGIN
    UPDATE staff_applications
    SET 
        status = 'approved',
        reviewed_by = admin_id,
        reviewed_at = NOW(),
        notes = review_notes
    WHERE id = app_id;
    
    -- Insert note
    INSERT INTO application_notes (application_id, admin_user_id, note_text)
    VALUES (app_id, admin_id, review_notes);
END //

-- Procedure: Get expiring documents report
CREATE PROCEDURE GetExpiringDocumentsReport(IN days_ahead INT)
BEGIN
    SELECT * FROM expiring_documents
    WHERE days_until_expiry <= days_ahead
    ORDER BY days_until_expiry ASC;
END //

DELIMITER ;

-- =====================================================
-- Triggers
-- =====================================================

DELIMITER //

-- Trigger: Auto-populate document expiry tracking after insert
CREATE TRIGGER after_application_insert
AFTER INSERT ON staff_applications
FOR EACH ROW
BEGIN
    -- Track Children Check expiry
    IF NEW.children_check_expiry IS NOT NULL THEN
        INSERT INTO document_expiry_tracking 
        (application_id, staff_email, document_type, expiry_date)
        VALUES (NEW.id, NEW.email, 'children_check', NEW.children_check_expiry);
    END IF;
    
    -- Track First Aid expiry
    IF NEW.first_aid_expiry IS NOT NULL THEN
        INSERT INTO document_expiry_tracking 
        (application_id, staff_email, document_type, expiry_date)
        VALUES (NEW.id, NEW.email, 'first_aid', NEW.first_aid_expiry);
    END IF;
    
    -- Track CPR expiry
    IF NEW.cpr_expiry IS NOT NULL THEN
        INSERT INTO document_expiry_tracking 
        (application_id, staff_email, document_type, expiry_date)
        VALUES (NEW.id, NEW.email, 'cpr', NEW.cpr_expiry);
    END IF;
    
    -- Track Police Check expiry
    IF NEW.police_check_expiry IS NOT NULL THEN
        INSERT INTO document_expiry_tracking 
        (application_id, staff_email, document_type, expiry_date)
        VALUES (NEW.id, NEW.email, 'police_check', NEW.police_check_expiry);
    END IF;
END //

DELIMITER ;

-- =====================================================
-- Indexes for Performance
-- =====================================================
CREATE INDEX idx_full_name ON staff_applications(first_name, last_name);
CREATE INDEX idx_employment_status ON staff_applications(employment_type, status);
CREATE INDEX idx_expiry_dates ON staff_applications(children_check_expiry, first_aid_expiry, cpr_expiry, police_check_expiry);

-- =====================================================
-- Sample Queries
-- =====================================================

-- Get all pending applications
-- SELECT * FROM pending_applications;

-- Get expiring documents in next 30 days
-- CALL GetExpiringDocumentsReport(30);

-- Get application details
-- CALL GetApplicationDetails(1);

-- Approve an application
-- CALL ApproveApplication(1, 1, 'All documents verified and approved');

-- Get application statistics
-- SELECT * FROM application_statistics WHERE application_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);

-- =====================================================
-- End of Schema
-- =====================================================


