<?php include 'layouts/header.php'; ?>

<!-- Page Header Start -->
<div class="page-header bg-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-content">
                    <h1 class="text-anime-style-2" data-cursor="-opaque">Referral Form</h1>
                    <nav class="breadcrumb">
                        <a href="./">Home</a>
                        <span>/</span>
                        <span>Referral</span>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Referral Form Section Start -->
<div class="referral-form-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Section Title Start -->
                <div class="section-title section-title-center">
                    <h3 class="wow fadeInUp">Get Started</h3>
                    <h2 class="text-anime-style-2" data-cursor="-opaque">Ready To <span>Make a Referral?</span></h2>
                    <p class="wow fadeInUp" data-wow-delay="0.2s">Fill out the form below to refer a participant to our senior care services</p>
                </div>
                <!-- Section Title End -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <!-- Referral Form Start -->
                <div class="referral-form wow fadeInUp" data-wow-delay="0.4s">
                    <form id="referralForm" method="POST" action="process-referral.php">
                        
                        <!-- Referral Source Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Referral Source</h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="referral_source">I am completing this for <span class="required">*</span></label>
                                        <select class="form-control" id="referral_source" name="referral_source" required>
                                            <option value="">Please Select</option>
                                            <option value="myself">Myself</option>
                                            <option value="family_member">Family Member</option>
                                            <option value="friend">Friend</option>
                                            <option value="healthcare_provider">Healthcare Provider</option>
                                            <option value="social_worker">Social Worker</option>
                                            <option value="case_manager">Case Manager</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Participant Details Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Participant Details</h3>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter first name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter last name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="dob">Date of Birth <span class="required">*</span></label>
                                        <input type="date" class="form-control" id="dob" name="dob" placeholder="mm/dd/yyyy" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="gender">Gender <span class="required">*</span></label>
                                        <select class="form-control" id="gender" name="gender" required>
                                            <option value="">Please Select</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="non_binary">Non-Binary</option>
                                            <option value="prefer_not_to_say">Prefer Not to Say</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="home_address">Home Address <span class="required">*</span></label>
                                        <textarea class="form-control" id="home_address" name="home_address" rows="2" placeholder="Enter complete address" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="phone_number">Participant Phone Number <span class="required">*</span></label>
                                        <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="(123) 456-7890" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="email_address">Participant Email Address</label>
                                        <input type="email" class="form-control" id="email_address" name="email_address" placeholder="email@example.com">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ndis_number">Participant NDIS Number</label>
                                        <input type="text" class="form-control" id="ndis_number" name="ndis_number" placeholder="Enter NDIS number">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Does The Participant Have A Legal Guardian / Nominee? <span class="required">*</span></label>
                                        <div class="radio-group">
                                            <label class="radio-label">
                                                <input type="radio" name="has_guardian" value="yes" required> Yes
                                            </label>
                                            <label class="radio-label">
                                                <input type="radio" name="has_guardian" value="no" required> No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cultural Details Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Cultural Details</h3>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="country_of_birth">Participant Country Of Birth</label>
                                        <input type="text" class="form-control" id="country_of_birth" name="country_of_birth" placeholder="Enter country">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="require_interpreter">Does The Participant Require An Interpreter?</label>
                                        <select class="form-control" id="require_interpreter" name="require_interpreter">
                                            <option value="">Please Select</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="relevant_culture">Relevant Culture Or Religious Considerations? (If Any)</label>
                                        <input type="text" class="form-control" id="relevant_culture" name="relevant_culture" placeholder="Enter details">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="identify_aboriginal">Does The Listed Participant Identify As An Aboriginal Or Torres Strait Islander?</label>
                                        <select class="form-control" id="identify_aboriginal" name="identify_aboriginal">
                                            <option value="">Please Select</option>
                                            <option value="aboriginal">Aboriginal</option>
                                            <option value="torres_strait">Torres Strait Islander</option>
                                            <option value="both">Both</option>
                                            <option value="neither">Neither</option>
                                            <option value="prefer_not_to_say">Prefer Not to Say</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Services Request Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Services Request</h3>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="primary_service">Type Of Primary Service Required <span class="required">*</span></label>
                                        <select class="form-control" id="primary_service" name="primary_service" required>
                                            <option value="">Please Select</option>
                                            <option value="assistance_daily_activities">Assistance With Daily Personal Activities</option>
                                            <option value="assistance_travel">Assistance With Travel/Transport</option>
                                            <option value="assistance_daily_life">Assistance With Daily Life Tasks</option>
                                            <option value="household_tasks">Household Tasks</option>
                                            <option value="participate_community">Participate In Community</option>
                                            <option value="group_activities">Group/Centre Activities</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="hours_required">Number Of Hours Requested For Service</label>
                                        <input type="number" class="form-control" id="hours_required" name="hours_required" placeholder="Enter hours" min="1">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="secondary_service">Type Of Secondary Service Required</label>
                                        <select class="form-control" id="secondary_service" name="secondary_service">
                                            <option value="">Please Select</option>
                                            <option value="assistance_daily_activities">Assistance With Daily Personal Activities</option>
                                            <option value="assistance_travel">Assistance With Travel/Transport</option>
                                            <option value="assistance_daily_life">Assistance With Daily Life Tasks</option>
                                            <option value="household_tasks">Household Tasks</option>
                                            <option value="participate_community">Participate In Community</option>
                                            <option value="group_activities">Group/Centre Activities</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="additional_service">Additional Service Required</label>
                                        <select class="form-control" id="additional_service" name="additional_service">
                                            <option value="">Please Select</option>
                                            <option value="assistance_daily_activities">Assistance With Daily Personal Activities</option>
                                            <option value="assistance_travel">Assistance With Travel/Transport</option>
                                            <option value="assistance_daily_life">Assistance With Daily Life Tasks</option>
                                            <option value="household_tasks">Household Tasks</option>
                                            <option value="participate_community">Participate In Community</option>
                                            <option value="group_activities">Group/Centre Activities</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="participant_conditions">Participant's Relevant Conditions / Disability (Please List)</label>
                                        <textarea class="form-control" id="participant_conditions" name="participant_conditions" rows="3" placeholder="Enter details"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="extra_information">Extra Information That May Assist With Preparation For Initial Appointment</label>
                                        <textarea class="form-control" id="extra_information" name="extra_information" rows="3" placeholder="Enter any additional information"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="special_assessments">Special Assessments Or Therapies Required</label>
                                        <textarea class="form-control" id="special_assessments" name="special_assessments" rows="3" placeholder="Enter details"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="practitioner_notes">Notes For Practitioner (Additional Relevant Details)</label>
                                        <textarea class="form-control" id="practitioner_notes" name="practitioner_notes" rows="3" placeholder="Enter notes"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Referrer Information Section (Optional) -->
                        <div class="form-section">
                            <h3 class="form-section-title">Referrer Information (If Different From Participant)</h3>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="referrer_name">Referrer Name</label>
                                        <input type="text" class="form-control" id="referrer_name" name="referrer_name" placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="referrer_relationship">Relationship to Participant</label>
                                        <input type="text" class="form-control" id="referrer_relationship" name="referrer_relationship" placeholder="e.g., Daughter, Social Worker">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="referrer_phone">Referrer Phone Number</label>
                                        <input type="tel" class="form-control" id="referrer_phone" name="referrer_phone" placeholder="(123) 456-7890">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="referrer_email">Referrer Email Address</label>
                                        <input type="email" class="form-control" id="referrer_email" name="referrer_email" placeholder="email@example.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consent Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Consent & Privacy</h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="consent_privacy" value="yes" required>
                                            <span>I consent to the collection and use of the information provided in this referral form for the purpose of assessing and delivering care services. I understand that this information will be kept confidential and handled in accordance with privacy laws. <span class="required">*</span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="consent_contact" value="yes" required>
                                            <span>I authorize Logan Express Care to contact the participant or their guardian/nominee to discuss care options and services. <span class="required">*</span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn-default btn-highlighted">Submit Referral</button>
                                </div>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="form-messages" class="form-messages"></div>
                            </div>
                        </div>

                    </form>
                </div>
                <!-- Referral Form End -->
            </div>
        </div>
    </div>
</div>
<!-- Referral Form Section End -->

<style>
.referral-form-section {
    padding: 80px 0;
}

.form-section {
    background: #f8f9fa;
    padding: 30px;
    margin-bottom: 30px;
    border-radius: 10px;
    border-left: 4px solid #D4A574;
}

.form-section-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 25px;
    color: #2c3e50;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 8px;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #D4A574;
    box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.1);
}

.required {
    color: #e74c3c;
}

.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 10px;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 400;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    line-height: 1.6;
}

.checkbox-label input[type="checkbox"],
.radio-label input[type="radio"] {
    margin-top: 3px;
    cursor: pointer;
}

.form-messages {
    margin-top: 20px;
    padding: 15px;
    border-radius: 5px;
    display: none;
}

.form-messages.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    display: block;
}

.form-messages.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    display: block;
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

@media (max-width: 768px) {
    .referral-form-section {
        padding: 40px 0;
    }
    
    .form-section {
        padding: 20px;
    }
    
    .radio-group {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
document.getElementById('referralForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('form-messages');
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
    
    fetch('process-referral.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            messageDiv.className = 'form-messages success';
            messageDiv.textContent = data.message || 'Referral submitted successfully! We will contact you soon.';
            this.reset();
            
            // Scroll to success message
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            messageDiv.className = 'form-messages error';
            messageDiv.textContent = data.message || 'There was an error submitting your referral. Please try again.';
        }
    })
    .catch(error => {
        messageDiv.className = 'form-messages error';
        messageDiv.textContent = 'There was an error submitting your referral. Please try again.';
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<?php include 'layouts/footer.php'; ?>