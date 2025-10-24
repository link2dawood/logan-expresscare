<?php include 'layouts/header.php'; ?>

    <!-- Page Header Start -->
    <div class="page-header bg-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-2" data-cursor="-opaque">Staff <span>Onboarding</span></h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="./">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">staff onboarding</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Staff Onboarding Form Start -->
    <div class="page-staff-onboarding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center wow fadeInUp">
                        <h3>Welcome to our team</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">Complete your onboarding <span>documentation</span></h2>
                        <p>Please fill out all required information and upload necessary documents to complete your registration.</p>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <!-- Progress Indicator Start -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="onboarding-progress wow fadeInUp" data-wow-delay="0.2s">
                        <div class="progress-steps">
                            <div class="progress-step active" data-step="1">
                                <div class="step-number">1</div>
                                <div class="step-label">Documents</div>
                            </div>
                            <div class="progress-step" data-step="2">
                                <div class="step-number">2</div>
                                <div class="step-label">Acknowledgments</div>
                            </div>
                            <div class="progress-step" data-step="3">
                                <div class="step-number">3</div>
                                <div class="step-label">Review</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Progress Indicator End -->

            <div class="row">
                <div class="col-lg-12">
                    <!-- Staff Onboarding Form Start -->
                    <div class="staff-onboarding-form-wrapper wow fadeInUp" data-wow-delay="0.4s">
                        <form id="staffOnboardingForm" method="POST" enctype="multipart/form-data" data-toggle="validator">
                            
                            <!-- Step 1: Document Uploads -->
                            <div class="form-step active" id="step-1">
                                <div class="step-header">
                                    <h3>Step 1: Upload Required Documents</h3>
                                    <p>Please upload all required documentation. Accepted formats: PDF, JPG, PNG (Max 5MB per file)</p>
                                </div>

                                <div class="row">
                                    <!-- Personal Information Section -->
                                    <div class="col-lg-12">
                                        <div class="section-divider">
                                            <h4>Personal Information</h4>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="First Name *" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Last Name *" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Email Address *" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone Number *" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- Position Selection -->
                                    <div class="form-group col-md-6 mb-4">
                                        <select name="position" class="form-control form-select" id="position" required>
                                            <option value="" disabled selected>Select Position *</option>
                                            <option value="disability_support_worker">Disability Support Worker</option>
                                            <option value="nurse">Nurse</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <select name="employment_type" class="form-control form-select" id="employment_type" required>
                                            <option value="" disabled selected>Employment Type *</option>
                                            <option value="permanent">Permanent</option>
                                            <option value="casual">Casual</option>
                                            <option value="contractor">Contractor</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- Credentials & Certifications Section -->
                                    <div class="col-lg-12">
                                        <div class="section-divider">
                                            <h4>Credentials & Certifications</h4>
                                        </div>
                                    </div>

                                    <!-- 1. NDIS Worker Screening Check -->
                                    <div class="col-lg-12">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">1. NDIS Worker Screening Check *</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="ndis_screening" class="form-control file-input" id="ndis_screening" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="ndis_screening" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <!-- 2. Working with Children Check -->
                                    <div class="col-lg-9">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">2. Working with Children Check *</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="children_check" class="form-control file-input" id="children_check" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="children_check" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 mb-4">
                                        <input type="date" name="children_check_expiry" class="form-control" id="children_check_expiry" placeholder="Expiry Date" required>
                                        <small class="form-text">Expiry Date *</small>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- 3. 100 Points of ID -->
                                    <div class="col-lg-12">
                                        <div class="section-divider-small">
                                            <h5>3. 100 Points of ID *</h5>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">Passport</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="passport" class="form-control file-input" id="passport" accept=".pdf,.jpg,.jpeg,.png">
                                                <label for="passport" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">Visa</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="visa" class="form-control file-input" id="visa" accept=".pdf,.jpg,.jpeg,.png">
                                                <label for="visa" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">Driver's License</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="drivers_license" class="form-control file-input" id="drivers_license" accept=".pdf,.jpg,.jpeg,.png">
                                                <label for="drivers_license" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 4. NDIS Worker Orientation Module Certificate -->
                                    <div class="col-lg-12">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">4. NDIS Worker Orientation Module Certificate *</label>
                                            <div class="info-link">
                                                <a href="https://training.ndiscommission.gov.au/" target="_blank">
                                                    <i class="fas fa-info-circle"></i> Get Certificate
                                                </a>
                                            </div>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="ndis_orientation" class="form-control file-input" id="ndis_orientation" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="ndis_orientation" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <!-- 5. Supporting Safe and Enjoyable Meals -->
                                    <div class="col-lg-9">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">5. Supporting Safe and Enjoyable Meals Certificate *</label>
                                            <div class="info-link">
                                                <a href="https://training.ndiscommission.gov.au/course/index.php?categoryid=5" target="_blank">
                                                    <i class="fas fa-info-circle"></i> Get Certificate
                                                </a>
                                            </div>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="meals_certificate" class="form-control file-input" id="meals_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="meals_certificate" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 mb-4">
                                        <input type="date" name="meals_certificate_expiry" class="form-control" id="meals_certificate_expiry" required>
                                        <small class="form-text">Expiry Date *</small>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- 6. Infection Prevention and Control Awareness Course -->
                                    <div class="col-lg-9">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">6. Infection Prevention and Control Awareness Certificate *</label>
                                            <div class="info-link">
                                                <a href="https://www.sentrient.com.au/course/ipc.html" target="_blank">
                                                    <i class="fas fa-info-circle"></i> Get Certificate
                                                </a>
                                            </div>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="ipc_certificate" class="form-control file-input" id="ipc_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="ipc_certificate" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <small class="form-text">Must be completed in the last 12 months</small>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 mb-4">
                                        <input type="date" name="ipc_certificate_expiry" class="form-control" id="ipc_certificate_expiry" required>
                                        <small class="form-text">Expiry Date *</small>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- 7. First Aid Certificate -->
                                    <div class="col-lg-9">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">7. First Aid Certificate *</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="first_aid_certificate" class="form-control file-input" id="first_aid_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="first_aid_certificate" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 mb-4">
                                        <input type="date" name="first_aid_expiry" class="form-control" id="first_aid_expiry" required>
                                        <small class="form-text">Expiry (3 years) *</small>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- 8. CPR Certificate -->
                                    <div class="col-lg-9">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">8. CPR Certificate *</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="cpr_certificate" class="form-control file-input" id="cpr_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="cpr_certificate" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 mb-4">
                                        <input type="date" name="cpr_expiry" class="form-control" id="cpr_expiry" required>
                                        <small class="form-text">Expiry (1 year) *</small>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- 9. Qualifications/Resume -->
                                    <div class="col-lg-12">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">9. Qualifications/Resume *</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="resume" class="form-control file-input" id="resume" accept=".pdf,.doc,.docx" required>
                                                <label for="resume" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <!-- 10. Staff Contract -->
                                    <div class="col-lg-12">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">10. Staff Contract *</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="staff_contract" class="form-control file-input" id="staff_contract" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="staff_contract" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <small class="form-text">Upload signed contract for your position and employment type</small>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <!-- 11. National Police Check -->
                                    <div class="col-lg-9">
                                        <div class="file-upload-group mb-4">
                                            <label class="file-upload-label">11. National Police Check *</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" name="police_check" class="form-control file-input" id="police_check" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <label for="police_check" class="file-upload-btn">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-name">Choose File</span>
                                                </label>
                                            </div>
                                            <small class="form-text">Valid for 1 year from issue date</small>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 mb-4">
                                        <input type="date" name="police_check_expiry" class="form-control" id="police_check_expiry" required>
                                        <small class="form-text">Expiry Date *</small>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- Step Navigation -->
                                    <div class="col-lg-12">
                                        <div class="form-step-navigation">
                                            <button type="button" class="btn-default next-step"><span>Next Step</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Acknowledgments and Signatures -->
                            <div class="form-step" id="step-2">
                                <div class="step-header">
                                    <h3>Step 2: Acknowledgments & Agreements</h3>
                                    <p>Please review and acknowledge the following documents and policies</p>
                                </div>

                                <div class="row">
                                    <!-- Contractor Details (Conditional) -->
                                    <div class="col-lg-12" id="contractor-section" style="display: none;">
                                        <div class="section-divider">
                                            <h4>Contractor Details</h4>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4" id="abn-field" style="display: none;">
                                        <input type="text" name="abn" class="form-control" id="abn" placeholder="ABN Number">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4" id="business-name-field" style="display: none;">
                                        <input type="text" name="business_name" class="form-control" id="business_name" placeholder="Business Name">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <!-- Document Acknowledgments -->
                                    <div class="col-lg-12">
                                        <div class="section-divider">
                                            <h4>Document Acknowledgments</h4>
                                            <p>Please check each box to confirm you have read, understood, and agree to the following:</p>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="acknowledgment-list">
                                            <!-- Staff Detail Form -->
                                            <div class="acknowledgment-item">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="ack_staff_details" id="ack_staff_details" required>
                                                    <label for="ack_staff_details">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>Staff Detail Form</strong>
                                                            <small>I confirm that all personal and professional details provided are accurate and complete</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Staff Induction Checklist -->
                                            <div class="acknowledgment-item">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="ack_induction" id="ack_induction" required>
                                                    <label for="ack_induction">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>Staff Induction Checklist</strong>
                                                            <small>I acknowledge completion of the staff induction process and understand my role and responsibilities</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Code of Conduct -->
                                            <div class="acknowledgment-item">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="ack_code_conduct" id="ack_code_conduct" required>
                                                    <label for="ack_code_conduct">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>Code of Conduct</strong>
                                                            <small>I have read and agree to abide by the organization's Code of Conduct</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Training Tracker -->
                                            <div class="acknowledgment-item">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="ack_training_tracker" id="ack_training_tracker" required>
                                                    <label for="ack_training_tracker">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>Training Tracker</strong>
                                                            <small>I understand my responsibility to maintain current training and certifications</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Employee Handbook -->
                                            <div class="acknowledgment-item">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="ack_handbook" id="ack_handbook" required>
                                                    <label for="ack_handbook">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>Employee Handbook</strong>
                                                            <small>I have received and reviewed the Employee Handbook and understand the policies and procedures</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Tax Details -->
                                            <div class="acknowledgment-item">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="ack_tax_declaration" id="ack_tax_declaration" required>
                                                    <label for="ack_tax_declaration">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>Tax Declaration</strong>
                                                            <small>I have completed and submitted my tax file number declaration</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Bank Details -->
                                            <div class="acknowledgment-item">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="ack_bank_details" id="ack_bank_details" required>
                                                    <label for="ack_bank_details">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>Bank Details</strong>
                                                            <small>I have provided accurate bank account details for payment processing</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step Navigation -->
                                    <div class="col-lg-12">
                                        <div class="form-step-navigation">
                                            <button type="button" class="btn-default btn-secondary prev-step"><span>Previous</span></button>
                                            <button type="button" class="btn-default next-step"><span>Next Step</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Review and Submit -->
                            <div class="form-step" id="step-3">
                                <div class="step-header">
                                    <h3>Step 3: Review & Submit</h3>
                                    <p>Please review your information before submitting</p>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="review-section">
                                            <div class="review-info-box">
                                                <div class="icon-box">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="review-content">
                                                    <h4>Almost There!</h4>
                                                    <p>You've completed all required sections. Please review your information and click submit to complete your onboarding process.</p>
                                                    <ul class="review-checklist">
                                                        <li><i class="fas fa-check"></i> All required documents uploaded</li>
                                                        <li><i class="fas fa-check"></i> Expiry dates provided where required</li>
                                                        <li><i class="fas fa-check"></i> All acknowledgments confirmed</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Final Consent -->
                                            <div class="final-consent">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" name="final_consent" id="final_consent" required>
                                                    <label for="final_consent">
                                                        <span class="checkbox-icon"></span>
                                                        <span class="checkbox-text">
                                                            <strong>I hereby declare that all information provided is true and accurate to the best of my knowledge. I understand that providing false information may result in termination of employment.</strong>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step Navigation -->
                                    <div class="col-lg-12">
                                        <div class="form-step-navigation">
                                            <button type="button" class="btn-default btn-secondary prev-step"><span>Previous</span></button>
                                            <button type="submit" class="btn-default btn-submit"><span>Submit Application</span></button>
                                        </div>
                                        <div id="msgSubmit" class="h3 hidden"></div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                    <!-- Staff Onboarding Form End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Staff Onboarding Form End -->

    <!-- Additional Styles -->
    <style>
        .page-staff-onboarding {
            padding: 80px 0;
        }

        .onboarding-progress {
            margin-bottom: 60px;
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            position: relative;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .progress-step::after {
            content: '';
            position: absolute;
            top: 25px;
            left: 60px;
            width: 100px;
            height: 2px;
            background: var(--divider-color);
            z-index: -1;
        }

        .progress-step:last-child::after {
            display: none;
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--secondary-color);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .progress-step.active .step-number,
        .progress-step.completed .step-number {
            background: var(--accent-color);
            color: var(--white-color);
        }

        .step-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color);
            text-align: center;
        }

        .progress-step.active .step-label {
            color: var(--primary-color);
        }

        .staff-onboarding-form-wrapper {
            background: var(--white-color);
            border-radius: 20px;
            padding: 60px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .step-header h3 {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .step-header p {
            color: var(--text-color);
            margin-bottom: 0;
        }

        .section-divider {
            margin: 30px 0 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--divider-color);
        }

        .section-divider h4 {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .section-divider p {
            font-size: 14px;
            color: var(--text-color);
            margin-bottom: 0;
        }

        .section-divider-small {
            margin: 20px 0 15px;
        }

        .section-divider-small h5 {
            font-size: 18px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .file-upload-group {
            position: relative;
        }

        .file-upload-label {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .info-link {
            margin-bottom: 10px;
        }

        .info-link a {
            color: var(--accent-color);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .info-link a:hover {
            color: var(--primary-color);
        }

        .info-link i {
            margin-right: 5px;
        }

        .file-upload-wrapper {
            position: relative;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .file-upload-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            background: var(--secondary-color);
            border: 2px dashed var(--divider-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0;
        }

        .file-upload-btn:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--white-color);
        }

        .file-upload-btn i {
            font-size: 24px;
        }

        .file-name {
            font-weight: 600;
        }

        .form-text {
            display: block;
            font-size: 13px;
            color: var(--text-color);
            margin-top: 5px;
        }

        .acknowledgment-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .acknowledgment-item {
            background: var(--secondary-color);
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .acknowledgment-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .custom-checkbox {
            display: flex;
            align-items: flex-start;
        }

        .custom-checkbox input[type="checkbox"] {
            display: none;
        }

        .custom-checkbox label {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            cursor: pointer;
            margin: 0;
        }

        .checkbox-icon {
            width: 24px;
            height: 24px;
            min-width: 24px;
            border: 2px solid var(--divider-color);
            border-radius: 5px;
            background: var(--white-color);
            position: relative;
            transition: all 0.3s ease;
        }

        .custom-checkbox input[type="checkbox"]:checked + label .checkbox-icon {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        .custom-checkbox input[type="checkbox"]:checked + label .checkbox-icon::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--white-color);
            font-size: 12px;
        }

        .checkbox-text {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .checkbox-text strong {
            color: var(--primary-color);
            font-size: 16px;
        }

        .checkbox-text small {
            color: var(--text-color);
            font-size: 14px;
            line-height: 1.5;
        }

        .form-step-navigation {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .btn-secondary::before {
            background-image: url('../images/arrow-primary.svg');
            transform: translateY(-50%) rotate(180deg);
        }

        .btn-secondary:hover::before {
            transform: translateY(-50%) rotate(225deg);
        }

        .review-section {
            max-width: 800px;
            margin: 0 auto;
        }

        .review-info-box {
            background: var(--secondary-color);
            border-radius: 15px;
            padding: 40px;
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }

        .review-info-box .icon-box {
            width: 80px;
            height: 80px;
            min-width: 80px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-info-box .icon-box i {
            font-size: 36px;
            color: var(--white-color);
        }

        .review-content h4 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .review-content p {
            margin-bottom: 20px;
        }

        .review-checklist {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .review-checklist li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .review-checklist li i {
            color: var(--accent-color);
            font-size: 18px;
        }

        .final-consent {
            background: #fff9f0;
            border: 2px solid var(--accent-color);
            border-radius: 10px;
            padding: 25px;
        }

        .btn-submit {
            background: #28a745;
        }

        .btn-submit:hover::after {
            background: #1e7e34;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .staff-onboarding-form-wrapper {
                padding: 40px 30px;
            }

            .progress-steps {
                gap: 20px;
            }

            .progress-step::after {
                width: 50px;
            }

            .review-info-box {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 767px) {
            .staff-onboarding-form-wrapper {
                padding: 30px 20px;
            }

            .step-header h3 {
                font-size: 24px;
            }

            .form-step-navigation {
                flex-direction: column;
            }

            .form-step-navigation .btn-default {
                width: 100%;
                text-align: center;
            }

            .progress-steps {
                flex-direction: column;
                gap: 20px;
            }

            .progress-step::after {
                display: none;
            }
        }
    </style>

    <!-- Custom JavaScript for Multi-step Form -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('staffOnboardingForm');
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.progress-step');
            const nextButtons = document.querySelectorAll('.next-step');
            const prevButtons = document.querySelectorAll('.prev-step');
            const employmentTypeSelect = document.getElementById('employment_type');
            const contractorSection = document.getElementById('contractor-section');
            const abnField = document.getElementById('abn-field');
            const businessNameField = document.getElementById('business-name-field');
            
            let currentStep = 0;

            // Show/hide contractor fields
            employmentTypeSelect.addEventListener('change', function() {
                if (this.value === 'contractor') {
                    contractorSection.style.display = 'block';
                    abnField.style.display = 'block';
                    businessNameField.style.display = 'block';
                } else {
                    contractorSection.style.display = 'none';
                    abnField.style.display = 'none';
                    businessNameField.style.display = 'none';
                }
            });

            // File input change handler
            document.querySelectorAll('.file-input').forEach(input => {
                input.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    const fileName = label.querySelector('.file-name');
                    if (this.files.length > 0) {
                        fileName.textContent = this.files[0].name;
                        label.style.background = 'var(--accent-color)';
                        label.style.color = 'var(--white-color)';
                        label.style.borderColor = 'var(--accent-color)';
                    } else {
                        fileName.textContent = 'Choose File';
                        label.style.background = 'var(--secondary-color)';
                        label.style.color = '';
                        label.style.borderColor = 'var(--divider-color)';
                    }
                });
            });

            // Next button click
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (validateStep(currentStep)) {
                        goToStep(currentStep + 1);
                    }
                });
            });

            // Previous button click
            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    goToStep(currentStep - 1);
                });
            });

            function goToStep(step) {
                if (step < 0 || step >= steps.length) return;
                
                steps[currentStep].classList.remove('active');
                progressSteps[currentStep].classList.remove('active');
                progressSteps[currentStep].classList.add('completed');
                
                currentStep = step;
                
                steps[currentStep].classList.add('active');
                progressSteps[currentStep].classList.add('active');
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function validateStep(step) {
                const currentStepElement = steps[step];
                const requiredFields = currentStepElement.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (field.type === 'checkbox') {
                        if (!field.checked) {
                            isValid = false;
                            field.closest('.acknowledgment-item')?.classList.add('error-highlight');
                            setTimeout(() => {
                                field.closest('.acknowledgment-item')?.classList.remove('error-highlight');
                            }, 2000);
                        }
                    } else if (field.type === 'file') {
                        if (!field.files || field.files.length === 0) {
                            isValid = false;
                            field.closest('.file-upload-group')?.classList.add('error-highlight');
                            setTimeout(() => {
                                field.closest('.file-upload-group')?.classList.remove('error-highlight');
                            }, 2000);
                        }
                    } else if (!field.value) {
                        isValid = false;
                        field.classList.add('error');
                        setTimeout(() => {
                            field.classList.remove('error');
                        }, 2000);
                    }
                });

                if (!isValid) {
                    alert('Please fill in all required fields before proceeding.');
                }

                return isValid;
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!validateStep(currentStep)) {
                    return;
                }

                // Show loading message
                const submitBtn = document.querySelector('.btn-submit');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span>Submitting...</span>';
                submitBtn.disabled = true;

                // Prepare form data for API submission
                const formData = new FormData(this);
                
                // Convert checkboxes to 1/0 for API
                const checkboxes = ['ack_staff_details', 'ack_induction', 'ack_code_conduct', 'ack_training_tracker', 
                                   'ack_handbook', 'ack_tax_declaration', 'ack_bank_details', 'final_consent'];
                checkboxes.forEach(name => {
                    const checkbox = document.querySelector(`input[name="${name}"]`);
                    formData.set(name, checkbox && checkbox.checked ? '1' : '0');
                });
                
                // Submit directly to Laravel API
                fetch('http://admin.loganexpresscare.com.au/api/v1/staff-onboarding/submit', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to success page
                        window.location.href = 'onboarding-success.php';
                    } else {
                        // Show error message
                        let errorMsg = data.message || 'An error occurred';
                        if (data.errors) {
                            errorMsg += '\n\n' + Object.values(data.errors).flat().join('\n');
                        }
                        alert('Error: ' + errorMsg);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the form. Please try again.');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });

            // Error highlighting styles
            const style = document.createElement('style');
            style.textContent = `
                .error-highlight {
                    animation: shake 0.5s;
                    border: 2px solid var(--error-color) !important;
                }
                .error {
                    border-color: var(--error-color) !important;
                }
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-10px); }
                    75% { transform: translateX(10px); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>

<?php include 'layouts/footer.php'; ?>


