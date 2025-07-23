<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Identity Documents
            $table->string('document_type')->nullable(); // passport, driver_license, national_id
            $table->string('document_number')->nullable();
            $table->string('document_front')->nullable(); // File path for front image
            $table->string('document_back')->nullable(); // File path for back image
            $table->date('document_expiry')->nullable();
            
            // Business Documents (Optional)
            $table->string('business_license')->nullable(); // Business license file
            $table->string('tax_certificate')->nullable(); // Tax registration certificate
            $table->string('insurance_certificate')->nullable(); // Insurance document
            $table->json('certifications')->nullable(); // Professional certifications
            $table->json('additional_documents')->nullable(); // Any other supporting documents
            
            // Verification Details
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'expired'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Important Dates
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // KYC expiration date
            
            // Compliance
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('privacy_accepted')->default(false);
            $table->string('ip_address')->nullable(); // IP when submitted
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_documents');
    }
};
