<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_onboarding', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->enum('position', ['disability_support_worker', 'nurse', 'other']);
            $table->enum('employment_type', ['permanent', 'casual', 'contractor']);
            
            // Contractor Details (optional)
            $table->string('abn')->nullable();
            $table->string('business_name')->nullable();
            
            // Documents - Store file paths/names
            $table->string('ndis_screening')->nullable();
            $table->string('children_check')->nullable();
            $table->date('children_check_expiry')->nullable();
            
            // 100 Points of ID
            $table->string('passport')->nullable();
            $table->string('visa')->nullable();
            $table->string('drivers_license')->nullable();
            
            // Certificates
            $table->string('ndis_orientation')->nullable();
            $table->string('meals_certificate')->nullable();
            $table->date('meals_certificate_expiry')->nullable();
            $table->string('ipc_certificate')->nullable();
            $table->date('ipc_certificate_expiry')->nullable();
            $table->string('first_aid_certificate')->nullable();
            $table->date('first_aid_expiry')->nullable();
            $table->string('cpr_certificate')->nullable();
            $table->date('cpr_expiry')->nullable();
            
            // Other Documents
            $table->string('resume')->nullable();
            $table->string('staff_contract')->nullable();
            $table->string('police_check')->nullable();
            $table->date('police_check_expiry')->nullable();
            
            // Acknowledgments
            $table->boolean('ack_staff_details')->default(false);
            $table->boolean('ack_induction')->default(false);
            $table->boolean('ack_code_conduct')->default(false);
            $table->boolean('ack_training_tracker')->default(false);
            $table->boolean('ack_handbook')->default(false);
            $table->boolean('ack_tax_declaration')->default(false);
            $table->boolean('ack_bank_details')->default(false);
            $table->boolean('final_consent')->default(false);
            
            // Status tracking
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_notes')->nullable();
            
            // Approval tracking
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('email');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_onboarding');
    }
};



