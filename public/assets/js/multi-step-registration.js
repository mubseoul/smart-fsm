/**
 * Multi-Step Registration Form JavaScript
 * Handles navigation, validation, and interactive elements
 */

class MultiStepRegistration {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.init();
    }

    init() {
        this.bindEvents();
        this.showStep(1);
    }

    bindEvents() {
        // Navigation buttons
        const nextBtn = document.getElementById("next-btn");
        const prevBtn = document.getElementById("prev-btn");
        const submitBtn = document.getElementById("submit-btn");
        const skipBtn = document.getElementById("skip-btn");

        if (nextBtn) {
            nextBtn.addEventListener("click", () => this.nextStep());
        }

        if (prevBtn) {
            prevBtn.addEventListener("click", () => this.prevStep());
        }

        if (skipBtn) {
            skipBtn.addEventListener("click", () => this.skipKYC());
        }

        // File uploads
        this.bindFileUploads();
    }

    nextStep() {
        if (this.validateStep(this.currentStep)) {
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.showStep(this.currentStep);
            }
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
        }
    }

    skipKYC() {
        // Skip KYC and go to Terms & Conditions step
        this.currentStep = 4;
        this.showStep(this.currentStep);
    }

    showStep(step) {
        // Hide all forms
        document.querySelectorAll(".step-form").forEach((form) => {
            form.classList.remove("active");
        });

        // Reset all step states
        document.querySelectorAll(".step").forEach((stepEl) => {
            stepEl.classList.remove("active", "completed");
        });

        // Show current form
        const currentForm = document.getElementById(`form-step-${step}`);
        if (currentForm) {
            currentForm.classList.add("active");
        }

        // Update progress indicators
        for (let i = 1; i <= this.totalSteps; i++) {
            const stepEl = document.getElementById(`step-${i}`);
            if (stepEl) {
                if (i < step) {
                    stepEl.classList.add("completed");
                } else if (i === step) {
                    stepEl.classList.add("active");
                }
            }
        }

        // Update business preview if on step 4
        if (step === 4) {
            this.updateBusinessPreview();
        }

        // Update navigation buttons
        this.updateNavigationButtons(step);
    }

    updateNavigationButtons(step) {
        const prevBtn = document.getElementById("prev-btn");
        const nextBtn = document.getElementById("next-btn");
        const submitBtn = document.getElementById("submit-btn");
        const skipBtn = document.getElementById("skip-btn");
        const termsSection = document.getElementById("terms-section");
        const recaptchaSection = document.getElementById("recaptcha-section");

        // Previous button
        if (prevBtn) {
            prevBtn.style.display = step === 1 ? "none" : "inline-block";
        }

        // Navigation for final step (Terms & Conditions)
        if (step === this.totalSteps) {
            if (nextBtn) nextBtn.style.display = "none";
            if (submitBtn) submitBtn.style.display = "inline-block";
            if (skipBtn) skipBtn.style.display = "none";
            if (termsSection) termsSection.style.display = "block";
            if (recaptchaSection) recaptchaSection.style.display = "block";
        } else if (step === 3) {
            // Step 3 is KYC (optional)
            if (nextBtn) nextBtn.style.display = "inline-block";
            if (submitBtn) submitBtn.style.display = "none";
            if (skipBtn) skipBtn.style.display = "inline-block";
            if (termsSection) termsSection.style.display = "none";
            if (recaptchaSection) recaptchaSection.style.display = "none";
        } else {
            if (nextBtn) nextBtn.style.display = "inline-block";
            if (submitBtn) submitBtn.style.display = "none";
            if (skipBtn) skipBtn.style.display = "none";
            if (termsSection) termsSection.style.display = "none";
            if (recaptchaSection) recaptchaSection.style.display = "none";
        }
    }

    validateStep(step) {
        let isValid = true;

        if (step === 1) {
            isValid = this.validateBasicInfo();
        } else if (step === 2) {
            isValid = this.validateBusinessInfo();
        } else if (step === 4) {
            isValid = this.validateTermsStep();
        }
        // Step 3 (KYC) is optional, so no validation required

        return isValid;
    }

    validateBasicInfo() {
        let isValid = true;
        const requiredFields = [
            "name",
            "email",
            "password",
            "password_confirmation",
            "phone_number",
        ];

        requiredFields.forEach((fieldName) => {
            const input = document.getElementById(fieldName);
            if (input && !input.value.trim()) {
                this.showFieldError(input, "This field is required");
                isValid = false;
            } else if (input) {
                this.clearFieldError(input);
            }
        });

        // Email validation
        const emailInput = document.getElementById("email");
        if (emailInput && emailInput.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                this.showFieldError(
                    emailInput,
                    "Please enter a valid email address"
                );
                isValid = false;
            }
        }

        // Password confirmation
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById(
            "password_confirmation"
        );
        if (
            password &&
            confirmPassword &&
            password.value !== confirmPassword.value
        ) {
            this.showFieldError(confirmPassword, "Passwords do not match");
            isValid = false;
        }

        return isValid;
    }

    validateBusinessInfo() {
        let isValid = true;

        // Business type
        const businessType = document.getElementById("business_type");
        if (!businessType || !businessType.value) {
            this.showAlert("Please select a business type");
            isValid = false;
        }

        // Service location fields
        const serviceCountry = document.getElementById("service_country");
        if (!serviceCountry || !serviceCountry.value) {
            this.showAlert("Please select a country");
            isValid = false;
        }

        const serviceZipcode = document.getElementById("service_zipcode");
        if (!serviceZipcode || !serviceZipcode.value.trim()) {
            this.showFieldError(serviceZipcode, "Please enter zip/postal code");
            isValid = false;
        } else {
            this.clearFieldError(serviceZipcode);
        }

        const serviceCity = document.getElementById("service_city");
        if (!serviceCity || !serviceCity.value.trim()) {
            this.showFieldError(serviceCity, "Please enter city");
            isValid = false;
        } else {
            this.clearFieldError(serviceCity);
        }

        const serviceAddress = document.getElementById("service_address");
        if (!serviceAddress || !serviceAddress.value.trim()) {
            this.showFieldError(serviceAddress, "Please enter street address");
            isValid = false;
        } else {
            this.clearFieldError(serviceAddress);
        }

        // Bio
        const bio = document.getElementById("bio");
        if (!bio || !bio.value.trim()) {
            this.showFieldError(bio, "Please tell us about your business");
            isValid = false;
        } else {
            this.clearFieldError(bio);
        }

        return isValid;
    }

    showFieldError(input, message) {
        input.classList.add("is-invalid");
        input.classList.remove("is-valid");

        // Remove existing error message
        const existingError = input.parentNode.querySelector(".field-error");
        if (existingError) {
            existingError.remove();
        }

        // Add error message
        const errorDiv = document.createElement("div");
        errorDiv.className = "field-error text-danger small mt-1";
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }

    clearFieldError(input) {
        input.classList.remove("is-invalid");
        input.classList.add("is-valid");

        // Remove error message
        const existingError = input.parentNode.querySelector(".field-error");
        if (existingError) {
            existingError.remove();
        }
    }

    validateTermsStep() {
        let isValid = true;

        // Check terms acceptance
        const termsAccepted = document.getElementById("terms_accepted");
        if (!termsAccepted || !termsAccepted.checked) {
            this.showFieldError(
                termsAccepted,
                "You must accept the Terms & Conditions to continue"
            );
            isValid = false;
        } else {
            this.clearFieldError(termsAccepted);
        }

        // Check privacy policy acceptance
        const privacyAccepted = document.getElementById("privacy_accepted");
        if (!privacyAccepted || !privacyAccepted.checked) {
            this.showFieldError(
                privacyAccepted,
                "You must accept the Privacy Policy to continue"
            );
            isValid = false;
        } else {
            this.clearFieldError(privacyAccepted);
        }

        return isValid;
    }

    updateBusinessPreview() {
        // Basic Information
        const name = document.getElementById("name")?.value || "-";
        const email = document.getElementById("email")?.value || "-";
        const phone = document.getElementById("phone_number")?.value || "-";

        document.getElementById("preview-name").textContent = name;
        document.getElementById("preview-email").textContent = email;
        document.getElementById("preview-phone").textContent = phone;

        // Business Information
        const businessTypeSelect = document.getElementById("business_type");
        const businessType =
            businessTypeSelect?.options[businessTypeSelect.selectedIndex]
                ?.text || "-";
        document.getElementById("preview-business-type").textContent =
            businessType;

        // Service Location
        const serviceCountry =
            document.getElementById("service_country")?.options[
                document.getElementById("service_country")?.selectedIndex
            ]?.text || "";
        const serviceCity =
            document.getElementById("service_city")?.value || "";
        const serviceZipcode =
            document.getElementById("service_zipcode")?.value || "";
        const serviceAddress =
            document.getElementById("service_address")?.value || "";

        const serviceLocation = [
            serviceAddress,
            serviceCity,
            serviceZipcode,
            serviceCountry,
        ]
            .filter((item) => item)
            .join(", ");
        document.getElementById("preview-service-location").textContent =
            serviceLocation || "-";

        // Optional Business Information
        const businessName =
            document.getElementById("business_name")?.value || "Not provided";
        const website =
            document.getElementById("website")?.value || "Not provided";
        const bio = document.getElementById("bio")?.value || "-";

        document.getElementById("preview-business-name").textContent =
            businessName;
        document.getElementById("preview-website").textContent = website;
        document.getElementById("preview-bio").textContent = bio;

        // Logo Preview
        const logoInput = document.getElementById("logo");
        const logoPreview = document.getElementById("preview-logo");

        if (logoInput?.files && logoInput.files[0]) {
            const file = logoInput.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                logoPreview.innerHTML = `<img src="${e.target.result}" alt="Business Logo" style="max-width: 100px; max-height: 60px; border-radius: 4px;">`;
            };
            reader.readAsDataURL(file);
        } else {
            logoPreview.innerHTML =
                '<span class="text-muted">No logo uploaded</span>';
        }

        // KYC Information (if provided)
        const documentType = document.getElementById("document_type")?.value;
        const documentNumber =
            document.getElementById("document_number")?.value;
        const kycPreviewSection = document.getElementById(
            "kyc-preview-section"
        );

        if (documentType || documentNumber) {
            kycPreviewSection.style.display = "block";

            const documentTypeOptions =
                document.getElementById("document_type")?.options;
            const documentTypeText =
                documentType && documentTypeOptions
                    ? documentTypeOptions[
                          document.getElementById("document_type").selectedIndex
                      ]?.text
                    : "Not provided";

            document.getElementById("preview-document-type").textContent =
                documentTypeText;
            document.getElementById("preview-document-number").textContent =
                documentNumber || "Not provided";
        } else {
            kycPreviewSection.style.display = "none";
        }
    }

    showAlert(message) {
        // You can customize this to use your preferred alert system
        alert(message);
    }

    bindFileUploads() {
        // File upload areas
        document.querySelectorAll(".file-upload-area").forEach((area) => {
            area.addEventListener("click", () => {
                const target = area.dataset.target || "logo";
                const input = document.getElementById(target);
                if (input) {
                    input.click();
                }
            });

            // Drag and drop
            area.addEventListener("dragover", (e) => {
                e.preventDefault();
                area.classList.add("dragover");
            });

            area.addEventListener("dragleave", () => {
                area.classList.remove("dragover");
            });

            area.addEventListener("drop", (e) => {
                e.preventDefault();
                area.classList.remove("dragover");

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const target = area.dataset.target || "logo";
                    const input = document.getElementById(target);
                    if (input) {
                        input.files = files;
                        this.updateFileDisplay(area, files[0]);
                    }
                }
            });
        });

        // File input change handlers
        document.querySelectorAll('input[type="file"]').forEach((input) => {
            input.addEventListener("change", (e) => {
                const file = e.target.files[0];
                if (file) {
                    const uploadArea =
                        document.querySelector(`[data-target="${input.id}"]`) ||
                        document.getElementById("logo-upload");
                    if (uploadArea) {
                        this.updateFileDisplay(uploadArea, file);
                    }
                }
            });
        });
    }

    updateFileDisplay(area, file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);

        area.classList.add("file-uploaded");
        area.innerHTML = `
            <i class="ti ti-check" style="font-size: 2rem; color: #28a745;"></i>
            <p class="mt-2 mb-0">${file.name}</p>
            <small class="text-muted">${fileSize} MB</small>
        `;
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    window.multiStepRegistration = new MultiStepRegistration();
});

// Make removeServiceArea globally accessible for inline onclick handlers
window.removeServiceArea = function (index) {
    if (window.multiStepRegistration) {
        window.multiStepRegistration.removeServiceArea(index);
    }
};
