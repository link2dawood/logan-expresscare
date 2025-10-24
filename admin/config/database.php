<?php
/**
 * Database Configuration
 * Logan Express Care - Admin Panel
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'booking_system';
    private $username = 'admin-getgo';
    private $password = 'zain123@getgo';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // First, connect without database to create it if needed
            $tempConn = new PDO(
                "mysql:host=" . $this->host . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );
            
            // Create database if it doesn't exist
            $tempConn->exec("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` 
                            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Now connect to the specific database
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Auto-setup: Check if tables exist, if not create them
            $this->autoSetupTables();
            
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
        
        return $this->conn;
    }
    
    /**
     * Automatically setup database tables if they don't exist
     */
    private function autoSetupTables() {
        try {
            // Check if admin_users table exists
            $query = "SHOW TABLES LIKE 'admin_users'";
            $stmt = $this->conn->query($query);
            
            if ($stmt->rowCount() === 0) {
                // Tables don't exist, create them
                $this->createTables();
                $this->seedDefaultData();
            }
        } catch(PDOException $e) {
            error_log("Auto-setup error: " . $e->getMessage());
        }
    }
    
    /**
     * Create all database tables
     */
    private function createTables() {
        // Staff Onboarding Table
        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS staff_onboarding (
                id INT AUTO_INCREMENT PRIMARY KEY,
                
                -- Personal Information
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                password VARCHAR(255) NOT NULL,
                position VARCHAR(50) DEFAULT NULL,
                employment_type VARCHAR(20) DEFAULT NULL,
                
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
                cpr_certificate VARCHAR(255) DEFAULT NULL,
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Admin Users Table
        $this->conn->exec("
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
    }
    
    /**
     * Seed default data (admin user and sample job postings)
     */
    private function seedDefaultData() {
        try {
            // Insert default admin user
            // Username: admin, Password: admin123
            $stmt = $this->conn->prepare("
                INSERT INTO admin_users (username, password, email, full_name, role, is_active)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $username = 'admin';
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $email = 'admin@loganexpresscare.com.au';
            $fullName = 'System Administrator';
            $role = 'admin';
            $isActive = 1;
            
            $stmt->execute([$username, $password, $email, $fullName, $role, $isActive]);
            
            error_log("Database tables created and seeded successfully with default admin user (username: admin, password: admin123)");
            
        } catch(PDOException $e) {
            error_log("Seeding error: " . $e->getMessage());
        }
    }
}
?>
