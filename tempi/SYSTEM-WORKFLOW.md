# Staff Onboarding System - Workflow Diagram

## 📊 System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    STAFF ONBOARDING SYSTEM                      │
│                     Logan Express Care                          │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────┐     ┌──────────────────┐     ┌──────────────┐
│   APPLICANT     │────▶│   APPLICATION    │────▶│  HR REVIEW   │
│   (Staff)       │     │   PROCESSING     │     │  (Admin)     │
└─────────────────┘     └──────────────────┘     └──────────────┘
```

---

## 🔄 Complete User Flow

### Step-by-Step Process

```
START
  │
  ├─▶ [1] Staff Member visits staff-onboarding.php
  │      │
  │      ├─▶ Sees professional form matching site theme
  │      ├─▶ Progress indicator shows 3 steps
  │      └─▶ Begins Step 1
  │
  ├─▶ [2] STEP 1: Document Upload
  │      │
  │      ├─▶ Enters personal information
  │      │   ├─ First Name, Last Name
  │      │   ├─ Email, Phone
  │      │   ├─ Position (DSW/Nurse/Other)
  │      │   └─ Employment Type (Permanent/Casual/Contractor)
  │      │
  │      ├─▶ Uploads 10 required documents
  │      │   ├─ NDIS Worker Screening Check
  │      │   ├─ Working with Children Check + Expiry
  │      │   ├─ 100 Points ID (Passport/Visa/License)
  │      │   ├─ NDIS Worker Orientation Module
  │      │   ├─ Safe & Enjoyable Meals + Expiry
  │      │   ├─ Infection Control + Expiry
  │      │   ├─ First Aid & CPR + 2 Expiries
  │      │   ├─ Qualifications/Resume
  │      │   ├─ Staff Contract
  │      │   └─ National Police Check + Expiry
  │      │
  │      ├─▶ Client-side validation checks:
  │      │   ├─ All required fields filled
  │      │   ├─ Valid email format
  │      │   └─ Files selected for required uploads
  │      │
  │      └─▶ Click "Next Step" → Move to Step 2
  │
  ├─▶ [3] STEP 2: Acknowledgments
  │      │
  │      ├─▶ IF contractor selected:
  │      │   ├─ Show ABN field
  │      │   └─ Show Business Name field
  │      │
  │      ├─▶ Check 7 acknowledgment boxes:
  │      │   ├─ ☑ Staff Detail Form
  │      │   ├─ ☑ Staff Induction Checklist
  │      │   ├─ ☑ Code of Conduct
  │      │   ├─ ☑ Training Tracker
  │      │   ├─ ☑ Employee Handbook
  │      │   ├─ ☑ Tax Declaration
  │      │   └─ ☑ Bank Details
  │      │
  │      └─▶ Click "Next Step" → Move to Step 3
  │
  ├─▶ [4] STEP 3: Review & Submit
  │      │
  │      ├─▶ Review completion checklist
  │      ├─▶ Read final consent declaration
  │      ├─▶ Check final consent box
  │      └─▶ Click "Submit Application"
  │
  ├─▶ [5] FORM SUBMISSION (AJAX)
  │      │
  │      ├─▶ Button shows "Submitting..."
  │      │
  │      ├─▶ FormData sent to form-staff-onboarding.php
  │      │
  │      └─▶ Backend Processing:
  │          │
  │          ├─▶ [5.1] Validate all inputs
  │          │   ├─ Sanitize text inputs
  │          │   ├─ Validate email format
  │          │   └─ Check required fields
  │          │
  │          ├─▶ [5.2] Process file uploads
  │          │   ├─ Check file size (< 5MB)
  │          │   ├─ Validate file type
  │          │   ├─ Generate unique filename
  │          │   ├─ Move to uploads/staff-onboarding/
  │          │   └─ Store filename in variable
  │          │
  │          ├─▶ [5.3] Save to CSV
  │          │   ├─ Open uploads/staff_applications.csv
  │          │   ├─ Append new row with data
  │          │   └─ Close file
  │          │
  │          ├─▶ [5.4] Send email notification
  │          │   ├─ To: HR email address
  │          │   ├─ Subject: Staff Onboarding Application
  │          │   ├─ Body: Complete application details
  │          │   └─ Send via PHP mail()
  │          │
  │          └─▶ [5.5] Return response
  │              ├─ If success: "success"
  │              └─ If error: Error message
  │
  ├─▶ [6] REDIRECT TO SUCCESS PAGE
  │      │
  │      ├─▶ User sees onboarding-success.php
  │      ├─▶ Animated checkmark confirmation
  │      ├─▶ "What Happens Next" timeline
  │      ├─▶ Contact information
  │      └─▶ Links to return home or learn more
  │
  ├─▶ [7] EMAIL CONFIRMATION
  │      │
  │      ├─▶ Staff member receives confirmation email
  │      └─▶ HR team receives application notification
  │
  └─▶ END (Staff member's part complete)


HR TEAM WORKFLOW:

  ├─▶ [8] HR ACCESSES ADMIN DASHBOARD
  │      │
  │      ├─▶ Visit admin-applications.php
  │      ├─▶ Enter admin password
  │      └─▶ Access granted
  │
  ├─▶ [9] VIEW APPLICATIONS
  │      │
  │      ├─▶ See statistics dashboard
  │      │   ├─ Total applications
  │      │   ├─ Permanent positions
  │      │   ├─ Casual positions
  │      │   └─ Contractors
  │      │
  │      ├─▶ View applications table
  │      │   ├─ Date submitted
  │      │   ├─ Applicant name
  │      │   ├─ Contact info
  │      │   ├─ Position applied for
  │      │   └─ Document status
  │      │
  │      ├─▶ Use filters
  │      │   ├─ Search by name/email
  │      │   ├─ Filter by position
  │      │   └─ Filter by employment type
  │      │
  │      └─▶ Click "View" on application
  │
  ├─▶ [10] REVIEW APPLICATION DETAILS
  │      │
  │      ├─▶ See modal with full details
  │      ├─▶ Personal information
  │      ├─▶ Document status with expiry dates
  │      └─▶ File location information
  │
  ├─▶ [11] ACCESS UPLOADED FILES
  │      │
  │      ├─▶ Navigate to uploads/staff-onboarding/
  │      ├─▶ Open and review documents
  │      └─▶ Verify authenticity
  │
  └─▶ [12] MAKE DECISION
         │
         ├─▶ Approve → Contact applicant for interview
         ├─▶ Reject → Send notification
         └─▶ Request more info → Contact applicant
```

---

## 🗂️ Data Flow Diagram

```
┌──────────────────┐
│  Staff Member    │
│  (Browser)       │
└────────┬─────────┘
         │
         │ Fills form & uploads files
         ▼
┌──────────────────────────┐
│  staff-onboarding.php    │
│  (Frontend Form)         │
│  • 3-step interface      │
│  • Client validation     │
│  • File selection        │
└────────┬─────────────────┘
         │
         │ AJAX POST (FormData)
         ▼
┌────────────────────────────────┐
│  form-staff-onboarding.php     │
│  (Backend Processing)          │
│  • Validate inputs             │
│  • Process uploads             │
│  • Save data                   │
└────┬───────────┬───────────────┘
     │           │
     │           ├─────────────────────┐
     │           │                     │
     ▼           ▼                     ▼
┌─────────┐  ┌──────────┐  ┌──────────────────┐
│  Files  │  │   CSV    │  │  Email (SMTP)    │
│uploads/ │  │  Backup  │  │  To: HR Team     │
│staff-   │  │staffapps │  │  Subject: New    │
│onboard/ │  │.csv      │  │  Application     │
└─────────┘  └──────────┘  └──────────────────┘
     │
     │
     ▼
┌──────────────────────────┐
│  admin-applications.php  │
│  (Admin Dashboard)       │
│  • View applications     │
│  • Filter/Search         │
│  • Statistics            │
└──────────────────────────┘
```

---

## 📁 File System Structure

```
project-root/
│
├── Frontend Files
│   ├── staff-onboarding.php          ← Main form (entry point)
│   ├── onboarding-success.php        ← Success confirmation
│   └── admin-applications.php        ← Admin dashboard
│
├── Backend Files
│   └── form-staff-onboarding.php     ← Form processor
│
├── Data Storage
│   └── uploads/
│       ├── .htaccess                 ← Security rules
│       ├── staff_applications.csv    ← Application backup
│       └── staff-onboarding/         ← Uploaded documents
│           ├── ndis_screening_xxx.pdf
│           ├── children_check_xxx.pdf
│           └── ... (more documents)
│
├── Database (Optional)
│   └── database-schema.sql           ← MySQL schema
│
└── Documentation
    ├── PROJECT-SUMMARY.md            ← Overview
    ├── QUICK-START-GUIDE.md          ← Setup guide
    ├── STAFF-ONBOARDING-README.md    ← Full docs
    └── SYSTEM-WORKFLOW.md            ← This file
```

---

## 🔐 Security Flow

```
┌─────────────────┐
│ User Input      │
└────────┬────────┘
         │
         ▼
┌─────────────────────┐
│ Client Validation   │
│ • Required fields   │
│ • Email format      │
│ • File selected     │
└────────┬────────────┘
         │
         ▼
┌─────────────────────────┐
│ Server Validation       │
│ • Sanitize inputs       │
│ • Validate email        │
│ • Check file size       │
│ • Verify file type      │
│ • XSS protection        │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Secure Storage          │
│ • Unique filenames      │
│ • Protected directory   │
│ • .htaccess rules       │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Admin Access            │
│ • Password protected    │
│ • Session management    │
└─────────────────────────┘
```

---

## 📧 Email Notification Flow

```
Form Submitted
     │
     ▼
┌──────────────────────┐
│ Generate Email Body  │
│ • Personal info      │
│ • Documents list     │
│ • Expiry dates       │
│ • Acknowledgments    │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ PHP mail() Function  │
│ To: HR Team          │
│ From: Applicant      │
│ Subject: Application │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ Email Server (SMTP)  │
│ Delivers email       │
└──────────┬───────────┘
           │
           ├────────────────────┐
           │                    │
           ▼                    ▼
    ┌──────────┐        ┌──────────┐
    │ HR Team  │        │ Applicant│
    │ Inbox    │        │ Confirm  │
    └──────────┘        └──────────┘
```

---

## 🔄 Application Review Workflow

```
┌─────────────────┐
│ New Application │
│ Submitted       │
└────────┬────────┘
         │
         ▼
┌────────────────────────┐
│ HR Receives Email      │
│ Notification           │
└────────┬───────────────┘
         │
         ▼
┌────────────────────────┐
│ HR Logs into           │
│ Admin Dashboard        │
└────────┬───────────────┘
         │
         ▼
┌────────────────────────┐
│ Reviews Application    │
│ • Personal info        │
│ • Documents            │
│ • Expiry dates         │
└────────┬───────────────┘
         │
         ▼
┌────────────────────────┐
│ Accesses Uploaded      │
│ Documents from Server  │
└────────┬───────────────┘
         │
         ▼
┌────────────────────────┐
│ Verification Process   │
│ • Check authenticity   │
│ • Verify expiry dates  │
│ • Confirm qualifications│
└────────┬───────────────┘
         │
         ├──────────┬──────────┐
         │          │          │
         ▼          ▼          ▼
    ┌────────┐ ┌────────┐ ┌─────────┐
    │Approve │ │Reject  │ │Request  │
    │        │ │        │ │More Info│
    └───┬────┘ └───┬────┘ └────┬────┘
        │          │           │
        ▼          ▼           ▼
  ┌──────────┐ ┌──────────┐ ┌──────────┐
  │Schedule  │ │Send      │ │Contact   │
  │Interview │ │Rejection │ │Applicant │
  └──────────┘ └──────────┘ └──────────┘
```

---

## 🎯 Document Processing Flow

```
For EACH uploaded document:

File Selected
     │
     ▼
┌─────────────────┐
│ Check file size │
│ (< 5MB?)        │
└────┬────────────┘
     │
     ├─ No ──▶ Show Error
     │
     ├─ Yes
     ▼
┌─────────────────┐
│ Check file type │
│ (PDF/JPG/PNG?)  │
└────┬────────────┘
     │
     ├─ No ──▶ Show Error
     │
     ├─ Yes
     ▼
┌─────────────────────┐
│ Generate unique     │
│ filename            │
│ field_time_rand.ext │
└────┬────────────────┘
     │
     ▼
┌─────────────────────┐
│ Move to             │
│ uploads/staff-      │
│ onboarding/         │
└────┬────────────────┘
     │
     ▼
┌─────────────────────┐
│ Update visual       │
│ feedback            │
│ (Green button)      │
└────┬────────────────┘
     │
     ▼
┌─────────────────────┐
│ Store filename      │
│ for submission      │
└─────────────────────┘
```

---

## 📊 Admin Dashboard Data Flow

```
┌──────────────────┐
│ Admin Login      │
│ (Password Auth)  │
└────────┬─────────┘
         │
         ▼
┌──────────────────────┐
│ Load CSV File        │
│ staff_applications   │
│ .csv                 │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ Parse Data           │
│ • Line by line       │
│ • Create array       │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ Calculate Statistics │
│ • Total apps         │
│ • By type            │
│ • By position        │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ Render Dashboard     │
│ • Stats cards        │
│ • Applications table │
│ • Filters            │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ User Interactions    │
│ • Search             │
│ • Filter             │
│ • View details       │
└──────────────────────┘
```

---

## 🔄 Multi-Step Navigation

```
┌──────────────────────────────────────────────────┐
│         Progress Indicator                       │
│  ●──────────── ○──────────── ○                  │
│  Step 1        Step 2        Step 3              │
│  Documents     Acknowledge   Review              │
└──────────────────────────────────────────────────┘

Step 1 (Active)
     │
     │ User clicks "Next Step"
     │
     ▼
┌──────────────┐
│ Validate     │  ─── No ──▶ Show Errors
│ Step 1       │            Stay on Step 1
└──────┬───────┘
       │
       │ Yes
       ▼
┌──────────────┐
│ Hide Step 1  │
│ Show Step 2  │
│ Update prog  │
└──────┬───────┘
       │
       ▼
Step 2 (Active)
     │
     │ User clicks "Next Step"
     │
     ▼
┌──────────────┐
│ Validate     │  ─── No ──▶ Show Errors
│ Step 2       │            Stay on Step 2
└──────┬───────┘
       │
       │ Yes
       ▼
┌──────────────┐
│ Hide Step 2  │
│ Show Step 3  │
│ Update prog  │
└──────┬───────┘
       │
       ▼
Step 3 (Active)
     │
     │ User clicks "Submit"
     │
     ▼
┌──────────────┐
│ Validate     │  ─── No ──▶ Show Errors
│ Step 3       │            Stay on Step 3
└──────┬───────┘
       │
       │ Yes
       ▼
Submit to Backend
```

---

## 🎨 Conditional Display Logic

```
Employment Type Selection
     │
     ├─ Permanent ──▶ Hide contractor fields
     │
     ├─ Casual ─────▶ Hide contractor fields
     │
     └─ Contractor ─▶ Show contractor fields
                      ├─ ABN Number field
                      └─ Business Name field
```

---

## 📱 Responsive Behavior

```
Desktop (1200px+)
┌─────────────────────────────────┐
│  Header                         │
├─────────────────────────────────┤
│  Progress: Step1  Step2  Step3  │
├─────────────────────────────────┤
│  [Form Fields in 2 columns]     │
│  [Field] [Field]                │
│  [Field] [Field]                │
├─────────────────────────────────┤
│  [Previous] [Next]              │
└─────────────────────────────────┘

Mobile (< 768px)
┌──────────────┐
│  Header      │
├──────────────┤
│  Progress    │
│    Step 1    │
│    Step 2    │
│    Step 3    │
├──────────────┤
│  [Field]     │
│  [Field]     │
│  [Field]     │
├──────────────┤
│  [Previous]  │
│  [Next]      │
└──────────────┘
```

---

## 🔍 Search & Filter Logic

```
Admin Dashboard
     │
     ▼
┌────────────────────┐
│ Load all apps      │
│ Display in table   │
└────────┬───────────┘
         │
         ▼
┌────────────────────┐
│ User types search  │
│ OR selects filter  │
└────────┬───────────┘
         │
         ▼
┌────────────────────────────┐
│ JavaScript filters rows    │
│ • Match search term        │
│ • Match position filter    │
│ • Match employment filter  │
└────────┬───────────────────┘
         │
         ▼
┌────────────────────────────┐
│ Show/hide rows            │
│ • Matching rows: visible   │
│ • Non-matching: hidden     │
└────────────────────────────┘
```

---

## ✅ Validation Logic

### Client-Side (JavaScript)
```
On "Next Step" click:
  │
  ├─▶ Get all required fields in current step
  │
  ├─▶ For each field:
  │   ├─ Is it empty?
  │   ├─ Is file selected (for file inputs)?
  │   └─ Is checkbox checked (for checkboxes)?
  │
  ├─▶ If any validation fails:
  │   ├─ Add error highlight
  │   ├─ Show error message
  │   └─ Prevent navigation
  │
  └─▶ If all validations pass:
      └─ Navigate to next step
```

### Server-Side (PHP)
```
On form submission:
  │
  ├─▶ Validate text inputs
  │   ├─ Not empty?
  │   ├─ Sanitize with htmlspecialchars()
  │   └─ Email valid format?
  │
  ├─▶ Validate file uploads
  │   ├─ File uploaded?
  │   ├─ Size < 5MB?
  │   ├─ Allowed file type?
  │   └─ Move to safe location?
  │
  ├─▶ If any validation fails:
  │   ├─ Add to error message
  │   └─ Return error to client
  │
  └─▶ If all validations pass:
      ├─ Save data
      ├─ Send email
      └─ Return success
```

---

## 🎯 Success!

This workflow ensures:
- ✅ Secure file handling
- ✅ Data validation at multiple points
- ✅ User-friendly experience
- ✅ Complete audit trail
- ✅ Easy HR review process
- ✅ Professional communication

---

**The system is designed for reliability, security, and ease of use!**


