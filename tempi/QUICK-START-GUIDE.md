# Quick Start Guide - Staff Onboarding System

## 🚀 Get Started in 5 Minutes

This guide will help you quickly set up and start using the staff onboarding system.

---

## 📋 What You Got

### Main Files Created:
1. **staff-onboarding.php** - The main onboarding form (frontend)
2. **form-staff-onboarding.php** - Form processing (backend)
3. **admin-applications.php** - Admin dashboard to view applications
4. **database-schema.sql** - Optional database schema
5. **STAFF-ONBOARDING-README.md** - Complete documentation
6. **uploads/.htaccess** - Security for uploaded files

---

## ⚡ Quick Setup (3 Steps)

### Step 1: Create Upload Directory
```bash
mkdir -p uploads/staff-onboarding
chmod 755 uploads
chmod 755 uploads/staff-onboarding
```

Or via FTP: Create folder `uploads/staff-onboarding` with 755 permissions.

### Step 2: Configure Email
Edit `form-staff-onboarding.php` (line 118):
```php
$EmailTo = "hr@loganexpresscare.com.au"; // Your HR email here
```

### Step 3: Test It!
1. Visit: `http://yoursite.com/staff-onboarding.php`
2. Fill out the form with test data
3. Check your email for submission notification
4. View submissions at: `http://yoursite.com/admin-applications.php`
   - Default password: `admin123` (change this!)

---

## 🎯 Key Features at a Glance

### For Staff Members:
✅ **3-Step Form Process**
- Step 1: Upload 10 required documents
- Step 2: Acknowledge policies and agreements
- Step 3: Review and submit

✅ **User-Friendly Interface**
- Progress indicator
- Drag-and-drop file uploads
- Mobile responsive
- Real-time validation

✅ **Document Requirements Clearly Shown**
- Links to training courses
- Expiry date tracking
- File format guidance

### For Administrators:
✅ **Admin Dashboard**
- View all applications
- Filter by position/type
- Search by name/email
- Application statistics

✅ **Email Notifications**
- Instant notification on submission
- Complete application details
- Document list with filenames

✅ **Data Storage**
- CSV backup file
- Optional database integration
- Secure file storage

---

## 📁 File Structure

```
your-project/
├── staff-onboarding.php          ← Main form
├── form-staff-onboarding.php     ← Backend processor
├── admin-applications.php        ← Admin dashboard
├── database-schema.sql           ← Optional database
├── STAFF-ONBOARDING-README.md    ← Full documentation
├── QUICK-START-GUIDE.md          ← This file
└── uploads/
    ├── .htaccess                 ← Security
    ├── staff_applications.csv    ← Generated automatically
    └── staff-onboarding/         ← Document storage
        ├── ndis_screening_xxx.pdf
        ├── children_check_xxx.pdf
        └── ...
```

---

## 🔐 Admin Access

**Dashboard URL:** `http://yoursite.com/admin-applications.php`

**Default Password:** `admin123`

⚠️ **IMPORTANT:** Change the admin password immediately!

Edit `admin-applications.php` (line 7):
```php
$admin_password = "your_secure_password_here";
```

---

## 📝 Document Requirements Checklist

The form requires these 10 documents:

| # | Document | Expiry Date? |
|---|----------|--------------|
| 1 | NDIS Worker Screening Check | No |
| 2 | Working with Children Check | ✅ Yes |
| 3 | 100 Points of ID (Passport/Visa/License) | No |
| 4 | NDIS Worker Orientation Module | No |
| 5 | Supporting Safe and Enjoyable Meals | ✅ Yes |
| 6 | Infection Prevention and Control | ✅ Yes (12 months) |
| 7 | First Aid & CPR Certificate | ✅ Yes (3 years / 1 year) |
| 8 | Qualifications/Resume | No |
| 9 | Staff Contract | No |
| 10 | National Police Check | ✅ Yes (1 year) |

---

## 🎨 Customization Quick Tips

### Change Colors
Edit `staff-onboarding.php` CSS variables:
```css
:root {
    --primary-color: #273A29;    /* Dark green */
    --accent-color: #E2A76F;     /* Beige/tan */
    --secondary-color: #F5F5F5;  /* Light gray */
}
```

### Add to Navigation
Edit `layouts/header.php`:
```php
<li class="nav-item">
    <a class="nav-link" href="staff-onboarding.php">Join Our Team</a>
</li>
```

### Change File Size Limit
Edit `form-staff-onboarding.php` (line 4):
```php
$max_file_size = 10 * 1024 * 1024; // 10MB
```

---

## ✅ Testing Checklist

Before going live, test these scenarios:

- [ ] Upload a small PDF (< 5MB)
- [ ] Try to upload large file (> 5MB) - should show error
- [ ] Try to upload wrong file type (.exe) - should reject
- [ ] Fill all required fields
- [ ] Skip a required field - should show validation
- [ ] Check all 3 steps navigate properly
- [ ] Verify email notification arrives
- [ ] Check files are saved in upload folder
- [ ] Login to admin dashboard
- [ ] View submitted application details
- [ ] Test on mobile device
- [ ] Test different browsers

---

## 🐛 Common Issues & Quick Fixes

### Issue: Files not uploading
**Fix:** Check folder permissions
```bash
chmod 755 uploads/staff-onboarding
```

### Issue: Email not sending
**Fix:** Check PHP mail configuration or use SMTP
```php
// Option 1: Check PHP mail is enabled
// Option 2: Use PHPMailer library (recommended)
```

### Issue: Page shows blank
**Fix:** Enable PHP error reporting
```php
// Add to top of file temporarily
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Issue: Upload size too small
**Fix:** Update php.ini
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

---

## 📚 Where to Find More Help

1. **Full Documentation**: Read `STAFF-ONBOARDING-README.md`
2. **Database Setup**: Use `database-schema.sql`
3. **PHP Errors**: Check your server error logs
4. **Frontend Issues**: Check browser console (F12)

---

## 🔄 Optional: Database Integration

Want to use a database instead of CSV?

### Quick Setup:
1. Import `database-schema.sql` to your MySQL database
2. Edit `form-staff-onboarding.php`
3. Add database connection:
```php
// Add at top of file
$conn = new mysqli("localhost", "username", "password", "database");

// Replace CSV save section with database insert
```

See `STAFF-ONBOARDING-README.md` for detailed instructions.

---

## 🎉 You're Ready!

### Next Steps:
1. ✅ Test the form end-to-end
2. ✅ Change admin password
3. ✅ Configure email recipient
4. ✅ Add link to your website navigation
5. ✅ Train HR staff on admin dashboard
6. ✅ Go live!

---

## 📞 Need Help?

- Check the full README: `STAFF-ONBOARDING-README.md`
- Review database schema: `database-schema.sql`
- Test with sample data first
- Ensure all prerequisites are met (PHP, file permissions, etc.)

---

## 🔒 Security Reminders

Before going live:
- [ ] Change admin password from default
- [ ] Verify .htaccess is protecting uploads
- [ ] Use HTTPS for the form
- [ ] Regular backup of applications
- [ ] Keep file size limits reasonable
- [ ] Validate all user inputs (already implemented)

---

## 📊 What Gets Stored Where

| Data | Storage Location | Format |
|------|-----------------|---------|
| Application Info | `uploads/staff_applications.csv` | CSV |
| Uploaded Documents | `uploads/staff-onboarding/` | PDF/JPG/PNG |
| Email Notification | Your inbox | Email |
| Optional: Database | MySQL | Tables |

---

## 🎯 Success Metrics

After launch, track:
- Number of applications received
- Most common position applied for
- Average time to complete form
- Document compliance rates
- Expiring certifications (via admin dashboard)

---

**Last Updated:** October 11, 2025  
**Version:** 1.0  
**Support:** Logan Express Care HR Team

---

## 🚀 Quick Commands Reference

```bash
# Create folders
mkdir -p uploads/staff-onboarding

# Set permissions
chmod 755 uploads uploads/staff-onboarding

# Import database (optional)
mysql -u username -p database_name < database-schema.sql

# Check PHP version
php -v

# Test file uploads
php -i | grep upload

# View recent applications
tail -f uploads/staff_applications.csv
```

---

**🎊 Congratulations! Your staff onboarding system is ready to use!**


