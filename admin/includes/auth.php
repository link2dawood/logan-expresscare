<?php
/**
 * Authentication System
 * Logan Express Care - Admin Panel
 */

// Note: session_start() should be called in the including file before any output
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        try {
            $query = "SELECT id, username, password, email, full_name, role, is_active 
                      FROM admin_users 
                      WHERE username = :username AND is_active = 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                if (password_verify($password, $user['password'])) {
                    // Update last login
                    $this->updateLastLogin($user['id']);
                    
                    // Set session variables
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_name'] = $user['full_name'];
                    $_SESSION['admin_role'] = $user['role'];
                    $_SESSION['admin_logged_in'] = true;
                    
                    return [
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => $user
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Invalid password'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'User not found or inactive'
                ];
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Login failed. Please try again.'
            ];
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Get current user data
     */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'],
                'email' => $_SESSION['admin_email'],
                'name' => $_SESSION['admin_name'],
                'role' => $_SESSION['admin_role']
            ];
        }
        return null;
    }
    
    /**
     * Require login for protected pages
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $userRole = $_SESSION['admin_role'];
        
        // Admin has access to everything
        if ($userRole === 'admin') {
            return true;
        }
        
        return $userRole === $role;
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        try {
            $query = "UPDATE admin_users SET last_login = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Verify current password
            $query = "SELECT password FROM admin_users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                if (password_verify($currentPassword, $user['password'])) {
                    // Update password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateQuery = "UPDATE admin_users SET password = :password WHERE id = :id";
                    $updateStmt = $this->db->prepare($updateQuery);
                    $updateStmt->bindParam(':password', $hashedPassword);
                    $updateStmt->bindParam(':id', $userId);
                    
                    if ($updateStmt->execute()) {
                        return [
                            'success' => true,
                            'message' => 'Password updated successfully'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Failed to update password'
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => 'Current password is incorrect'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Password change failed. Please try again.'
            ];
        }
    }
}
?>
