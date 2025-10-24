# Logan Express Care - Staff Onboarding System

## 📁 Project Structure

This is a professional staff onboarding system for Logan Express Care built with PHP and Laravel API backend.

---

## 🎯 System Overview

### **Staff Onboarding Portal** (`/admin/`)
All staff onboarding functionality is contained within the `/admin/` directory:

- **User Signup & Login** - Staff create accounts and access their dashboard
- **Onboarding Form** - Multi-step document upload and acknowledgment process
- **Status Tracking** - Real-time tracking of application status
- **Admin Review** - Dedicated admin panel for HR to review applications

### **Public Website** (Root Directory)
- Professional business website with services, team, blog, and contact pages
- Completely separate from the staff onboarding system

---

## 🔐 Access Points

### **For Staff:**
- **Dashboard:** `domain.com/admin/`
- **Signup:** `domain.com/admin/user-signup.php`
- **Login:** `domain.com/admin/user-login.php`

### **For Administrators:**
- **Admin Login:** `domain.com/admin/superadmin-login.php`
- **Admin Dashboard:** `domain.com/admin/admin-dashboard.php`

---

## 📊 Key Features

### **Staff Portal:**
- ✅ Secure user registration and authentication
- ✅ Multi-step onboarding form with document uploads
- ✅ Real-time application status tracking
- ✅ Progress indicator with active state animations
- ✅ Beautiful, responsive design

### **Admin Panel:**
- ✅ Comprehensive application management
- ✅ Document review and download
- ✅ Status updates and admin notes
- ✅ User management
- ✅ Dashboard with statistics

---

## 🛠️ Technical Stack

- **Frontend:** PHP, HTML5, CSS3, JavaScript
- **Backend API:** Laravel (http://admin.loganexpresscare.com.au/api/v1/)
- **Database:** MySQL
- **Authentication:** Session-based with password hashing
- **File Storage:** Local file system with organized directory structure

---

## 📂 Directory Structure

```
/
├── admin/                          # Staff Onboarding System
│   ├── index.php                  # User Dashboard
│   ├── user-login.php            # User Login
│   ├── user-signup.php           # User Signup
│   ├── user-onboarding.php       # Onboarding Form
│   ├── success.php               # Success Page
│   ├── superadmin-login.php      # Admin Login
│   ├── admin-dashboard.php       # Admin Dashboard
│   ├── applications.php          # Application Management
│   ├── users.php                 # User Management
│   └── config/
│       └── database.php          # Database Configuration
│
├── css/                           # Stylesheets
├── js/                            # JavaScript files
├── images/                        # Image assets
├── layouts/                       # Header & Footer
├── uploads/
│   └── staff-onboarding/         # Uploaded documents
│
└── [public website pages]        # About, Services, Contact, etc.
```

---

## 🚀 Getting Started

### **Database Setup:**
The database automatically creates tables on first connection. Default admin credentials:
- Username: `admin`
- Password: `admin123`

### **For Staff:**
1. Visit `/admin/` on your domain
2. Click "Sign Up" to create an account
3. Complete the onboarding form
4. Track your application status

### **For Administrators:**
1. Visit `/admin/superadmin-login.php`
2. Login with admin credentials
3. Review and manage staff applications

---

## 📝 Onboarding Process

### **Step 1: Account Creation**
Staff create an account with email and password

### **Step 2: Form Submission**
Complete multi-step form with:
- Personal information
- Employment details
- Document uploads (NDIS screening, certifications, etc.)
- Acknowledgments and agreements

### **Step 3: Admin Review**
HR team reviews application and documents

### **Step 4: Decision**
Application is approved or requires more information

---

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ Session-based authentication
- ✅ SQL injection protection (PDO prepared statements)
- ✅ File upload validation
- ✅ Access control for admin functions

---

## 📱 Responsive Design

The system is fully responsive and works on:
- 💻 Desktop
- 📱 Tablet
- 📱 Mobile

---

## 🎨 Design Features

- Clean, modern interface
- Intuitive navigation
- Progress tracking with animations
- Status-based color coding
- Professional forms and layouts

---

## 📞 Support

For technical support or questions, contact the Logan Express Care IT team.

---

**© 2024 Logan Express Care. All rights reserved.**
