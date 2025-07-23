@extends('layouts.auth')
@php
    $settings = settings();
@endphp
@section('tab-title')
    {{ __('Become a Provider') }}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/multi-step-registration.css') }}">
@endpush
@push('script-page')
    @if ($settings['google_recaptcha'] == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
    <script src="{{ asset('assets/js/multi-step-registration.js') }}"></script>
@endpush
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="d-flex justify-content-center">
                    <div class="auth-header">
                        <h2 class="text-secondary"><b>{{ __('Become a Provider') }} </b></h2>
                        <p class="f-16 mt-2">{{ __('Join our platform and start managing your field service business') }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="step-progress">
                <div class="step active" id="step-1">
                    <div class="step-circle">1</div>
                    <div class="step-label">{{ __('Basic Info') }}</div>
                </div>
                <div class="step" id="step-2">
                    <div class="step-circle">2</div>
                    <div class="step-label">{{ __('Business Info') }}</div>
                </div>
                <div class="step" id="step-3">
                    <div class="step-circle">3</div>
                    <div class="step-label">{{ __('KYC (Optional)') }}</div>
                </div>
                <div class="step" id="step-4">
                    <div class="step-circle">4</div>
                    <div class="step-label">{{ __('Confirm') }}</div>
                </div>
            </div>

            {{ Form::open(['route' => 'register', 'method' => 'post', 'id' => 'multi-step-form', 'enctype' => 'multipart/form-data']) }}
            
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif

            <!-- Step 1: Basic Information -->
            <div class="step-form active" id="form-step-1">
                <h4 class="mb-3">{{ __('Personal Information') }}</h4>
                
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Full Name') }}" required />
                    <label for="name">{{ __('Full Name') }} *</label>
                    @error('name')
                        <span class="invalid-name text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email Address') }}" required />
                    <label for="email">{{ __('Email Address') }} *</label>
                    @error('email')
                        <span class="invalid-email text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('Password') }}" required />
                    <label for="password">{{ __('Password') }} *</label>
                    @error('password')
                        <span class="invalid-password text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required />
                    <label for="password_confirmation">{{ __('Confirm Password') }} *</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="{{ __('Phone Number') }}" required />
                    <label for="phone_number">{{ __('Phone Number') }} *</label>
                    @error('phone_number')
                        <span class="invalid-phone text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Step 2: Business Information -->
            <div class="step-form" id="form-step-2">
                <h4 class="mb-3">{{ __('Business Information') }}</h4>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="business_name" name="business_name" placeholder="{{ __('Business Name') }}" />
                    <label for="business_name">{{ __('Business Name') }}</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Business Type') }} *</label>
                    <select class="form-control" name="business_type" id="business_type" required>
                        <option value="">{{ __('Select Business Type') }}</option>
                        @foreach(\App\Models\Category::with('children')->where('parent_id', 0)->where('active', true)->orderBy('name')->get() as $category)
                            <optgroup label="{{ $category->name }}">
                                @foreach($category->children->where('active', true)->sortBy('name') as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('business_type')
                        <span class="text-danger small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Service Location') }} *</label>
                    <p class="text-muted small mb-3">{{ __('Enter the primary location where you provide services') }}</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-control" name="service_country" id="service_country" required>
                                    <option value="">{{ __('Select Country') }}</option>
                                    <option value="US">{{ __('United States') }}</option>
                                    <option value="CA">{{ __('Canada') }}</option>
                                    <option value="GB">{{ __('United Kingdom') }}</option>
                                    <option value="AU">{{ __('Australia') }}</option>
                                    <option value="DE">{{ __('Germany') }}</option>
                                    <option value="FR">{{ __('France') }}</option>
                                    <option value="IT">{{ __('Italy') }}</option>
                                    <option value="ES">{{ __('Spain') }}</option>
                                    <option value="NL">{{ __('Netherlands') }}</option>
                                    <option value="BE">{{ __('Belgium') }}</option>
                                    <option value="CH">{{ __('Switzerland') }}</option>
                                    <option value="AT">{{ __('Austria') }}</option>
                                    <option value="SE">{{ __('Sweden') }}</option>
                                    <option value="NO">{{ __('Norway') }}</option>
                                    <option value="DK">{{ __('Denmark') }}</option>
                                    <option value="FI">{{ __('Finland') }}</option>
                                    <option value="IE">{{ __('Ireland') }}</option>
                                    <option value="PT">{{ __('Portugal') }}</option>
                                    <option value="GR">{{ __('Greece') }}</option>
                                    <option value="PL">{{ __('Poland') }}</option>
                                    <option value="CZ">{{ __('Czech Republic') }}</option>
                                    <option value="HU">{{ __('Hungary') }}</option>
                                    <option value="SK">{{ __('Slovakia') }}</option>
                                    <option value="SI">{{ __('Slovenia') }}</option>
                                    <option value="HR">{{ __('Croatia') }}</option>
                                    <option value="BG">{{ __('Bulgaria') }}</option>
                                    <option value="RO">{{ __('Romania') }}</option>
                                    <option value="LT">{{ __('Lithuania') }}</option>
                                    <option value="LV">{{ __('Latvia') }}</option>
                                    <option value="EE">{{ __('Estonia') }}</option>
                                    <option value="MT">{{ __('Malta') }}</option>
                                    <option value="CY">{{ __('Cyprus') }}</option>
                                    <option value="LU">{{ __('Luxembourg') }}</option>
                                    <option value="JP">{{ __('Japan') }}</option>
                                    <option value="KR">{{ __('South Korea') }}</option>
                                    <option value="CN">{{ __('China') }}</option>
                                    <option value="IN">{{ __('India') }}</option>
                                    <option value="SG">{{ __('Singapore') }}</option>
                                    <option value="MY">{{ __('Malaysia') }}</option>
                                    <option value="TH">{{ __('Thailand') }}</option>
                                    <option value="ID">{{ __('Indonesia') }}</option>
                                    <option value="PH">{{ __('Philippines') }}</option>
                                    <option value="VN">{{ __('Vietnam') }}</option>
                                    <option value="BD">{{ __('Bangladesh') }}</option>
                                    <option value="PK">{{ __('Pakistan') }}</option>
                                    <option value="LK">{{ __('Sri Lanka') }}</option>
                                    <option value="NP">{{ __('Nepal') }}</option>
                                    <option value="MM">{{ __('Myanmar') }}</option>
                                    <option value="KH">{{ __('Cambodia') }}</option>
                                    <option value="LA">{{ __('Laos') }}</option>
                                    <option value="BN">{{ __('Brunei') }}</option>
                                    <option value="MV">{{ __('Maldives') }}</option>
                                    <option value="BT">{{ __('Bhutan') }}</option>
                                    <option value="MX">{{ __('Mexico') }}</option>
                                    <option value="BR">{{ __('Brazil') }}</option>
                                    <option value="AR">{{ __('Argentina') }}</option>
                                    <option value="CL">{{ __('Chile') }}</option>
                                    <option value="CO">{{ __('Colombia') }}</option>
                                    <option value="PE">{{ __('Peru') }}</option>
                                    <option value="VE">{{ __('Venezuela') }}</option>
                                    <option value="EC">{{ __('Ecuador') }}</option>
                                    <option value="BO">{{ __('Bolivia') }}</option>
                                    <option value="PY">{{ __('Paraguay') }}</option>
                                    <option value="UY">{{ __('Uruguay') }}</option>
                                    <option value="GY">{{ __('Guyana') }}</option>
                                    <option value="SR">{{ __('Suriname') }}</option>
                                    <option value="GF">{{ __('French Guiana') }}</option>
                                    <option value="ZA">{{ __('South Africa') }}</option>
                                    <option value="NG">{{ __('Nigeria') }}</option>
                                    <option value="KE">{{ __('Kenya') }}</option>
                                    <option value="EG">{{ __('Egypt') }}</option>
                                    <option value="MA">{{ __('Morocco') }}</option>
                                    <option value="DZ">{{ __('Algeria') }}</option>
                                    <option value="TN">{{ __('Tunisia') }}</option>
                                    <option value="LY">{{ __('Libya') }}</option>
                                    <option value="SD">{{ __('Sudan') }}</option>
                                    <option value="ET">{{ __('Ethiopia') }}</option>
                                    <option value="UG">{{ __('Uganda') }}</option>
                                    <option value="TZ">{{ __('Tanzania') }}</option>
                                    <option value="RW">{{ __('Rwanda') }}</option>
                                    <option value="BI">{{ __('Burundi') }}</option>
                                    <option value="DJ">{{ __('Djibouti') }}</option>
                                    <option value="SO">{{ __('Somalia') }}</option>
                                    <option value="ER">{{ __('Eritrea') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                                <label for="service_country">{{ __('Country') }} *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="service_zipcode" name="service_zipcode" placeholder="{{ __('Zip/Postal Code') }}" required />
                                <label for="service_zipcode">{{ __('Zip/Postal Code') }} *</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="service_city" name="service_city" placeholder="{{ __('City') }}" required />
                        <label for="service_city">{{ __('City') }} *</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="service_address" name="service_address" placeholder="{{ __('Street Address') }}" required />
                        <label for="service_address">{{ __('Street Address') }} *</label>
                    </div>
                    
                    @error('service_country')
                        <span class="text-danger small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    @error('service_zipcode')
                        <span class="text-danger small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    @error('service_city')
                        <span class="text-danger small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    @error('service_address')
                        <span class="text-danger small" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Business Logo') }}</label>
                    <div class="file-upload-area" id="logo-upload">
                        <i class="ti ti-cloud-upload" style="font-size: 2rem; color: #6c757d;"></i>
                        <p class="mt-2 mb-0">{{ __('Click to upload or drag and drop') }}</p>
                        <small class="text-muted">{{ __('PNG, JPG up to 2MB') }}</small>
                    </div>
                    <input type="file" name="logo" id="logo" accept="image/*" style="display: none;">
                </div>

                <div class="form-floating mb-3">
                    <textarea class="form-control" id="bio" name="bio" style="height: 100px" placeholder="{{ __('Business Description') }}" required></textarea>
                    <label for="bio">{{ __('Tell us about your business') }} *</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" class="form-control" id="business_phone" name="business_phone" placeholder="{{ __('Business Phone') }}" />
                    <label for="business_phone">{{ __('Business Phone') }}</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="business_address" name="business_address" placeholder="{{ __('Business Address') }}" />
                    <label for="business_address">{{ __('Business Address') }}</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="url" class="form-control" id="website" name="website" placeholder="{{ __('Website') }}" />
                    <label for="website">{{ __('Website (Optional)') }}</label>
                </div>
            </div>

            <!-- Step 3: KYC (Optional) -->
            <div class="step-form" id="form-step-3">
                <h4 class="mb-3">{{ __('Identity Verification (Optional)') }}</h4>
                <p class="text-muted mb-4">{{ __('Complete KYC to increase trust and get verified badge. You can skip this step and complete it later.') }}</p>

                <div class="mb-3">
                    <label class="form-label">{{ __('Document Type') }}</label>
                    <select class="form-control" name="document_type" id="document_type">
                        <option value="">{{ __('Select Document Type') }}</option>
                        @foreach(\App\Models\KycDocument::getDocumentTypes() as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="kyc-document-upload">
                    <label class="form-label">{{ __('Document Front Side') }}</label>
                    <div class="file-upload-area" data-target="document_front">
                        <i class="ti ti-id" style="font-size: 2rem; color: #6c757d;"></i>
                        <p class="mt-2 mb-0">{{ __('Upload front side of document') }}</p>
                    </div>
                    <input type="file" name="document_front" accept="image/*" style="display: none;">
                </div>

                <div class="kyc-document-upload">
                    <label class="form-label">{{ __('Document Back Side') }}</label>
                    <div class="file-upload-area" data-target="document_back">
                        <i class="ti ti-id" style="font-size: 2rem; color: #6c757d;"></i>
                        <p class="mt-2 mb-0">{{ __('Upload back side of document') }}</p>
                    </div>
                    <input type="file" name="document_back" accept="image/*" style="display: none;">
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="document_number" name="document_number" placeholder="{{ __('Document Number') }}" />
                    <label for="document_number">{{ __('Document Number') }}</label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="kyc_terms_accepted" id="kyc_terms_accepted">
                    <label class="form-check-label" for="kyc_terms_accepted">
                        {{ __('I accept the terms and conditions for KYC verification') }}
                    </label>
                </div>
            </div>

            <!-- Step 4: Terms & Conditions (Required) -->
            <div class="step-form" id="form-step-4">
                <h4 class="mb-3">{{ __('Confirm') }}</h4>
                <p class="text-muted mb-4">{{ __('Please review your information and accept our terms and conditions to complete your registration.') }}</p>

                <!-- Business Details Preview -->
                <div class="business-preview-section mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Your Business Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Business Owner') }}</label>
                                        <p class="mb-0" id="preview-name">-</p>
                                    </div>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Email Address') }}</label>
                                        <p class="mb-0" id="preview-email">-</p>
                                    </div>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Phone Number') }}</label>
                                        <p class="mb-0" id="preview-phone">-</p>
                                    </div>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Business Type') }}</label>
                                        <p class="mb-0" id="preview-business-type">-</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Service Location') }}</label>
                                        <p class="mb-0" id="preview-service-location">-</p>
                                    </div>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Business Name') }}</label>
                                        <p class="mb-0" id="preview-business-name">{{ __('Not provided') }}</p>
                                    </div>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Website') }}</label>
                                        <p class="mb-0" id="preview-website">{{ __('Not provided') }}</p>
                                    </div>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Business Logo') }}</label>
                                        <div id="preview-logo">
                                            <span class="text-muted">{{ __('No logo uploaded') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Business Description') }}</label>
                                        <p class="mb-0" id="preview-bio">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="kyc-preview-section" style="display: none;">
                                <div class="col-12">
                                    <hr>
                                    <h6 class="text-muted mb-3">{{ __('KYC Information') }}</h6>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Document Type') }}</label>
                                        <p class="mb-0" id="preview-document-type">{{ __('Not provided') }}</p>
                                    </div>
                                    <div class="preview-item mb-3">
                                        <label class="form-label fw-bold">{{ __('Document Number') }}</label>
                                        <p class="mb-0" id="preview-document-number">{{ __('Not provided') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($menu) && $menu)
                <div class="terms-content-box mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $menu->title }}</h5>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            {!! $menu->content !!}
                        </div>
                    </div>
                </div>
                @endif

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" required>
                    <label class="form-check-label" for="terms_accepted">
                        <strong>{{ __('I have read and agree to the Terms & Conditions') }} *</strong>
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="privacy_accepted" id="privacy_accepted" required>
                    <label class="form-check-label" for="privacy_accepted">
                        <strong>{{ __('I agree to the Privacy Policy') }} *</strong>
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="marketing_emails" id="marketing_emails">
                    <label class="form-check-label" for="marketing_emails">
                        {{ __('I agree to receive marketing emails and updates (optional)') }}
                    </label>
                </div>
            </div>

            <!-- Terms and Conditions -->
            @php
                $menu = \App\Models\Page::where('slug', 'terms_conditions')->first();
            @endphp
            <div class="form-check mb-3" id="terms-section" style="display: none;">
                <input class="form-check-input input-primary" type="checkbox" id="agree" name="agree" required />
                <label class="form-check-label text-muted" for="agree">
                    <span class="h5 mb-0">
                        {{ __('I agree with') }}
                        <span><a href="{{ !empty($menu->slug) ? route('page', $menu->slug) : '#' }}" target="_blank">{{ __('Terms and conditions') }}</a>.</span>
                    </span>
                </label>
            </div>

            @if ($settings['google_recaptcha'] == 'on')
                <div class="form-group" id="recaptcha-section" style="display: none;">
                    <label for="email" class="form-label"></label>
                    {!! NoCaptcha::display() !!}
                    @error('g-recaptcha-response')
                        <span class="small text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-navigation" id="prev-btn" style="display: none;">
                    <i class="ti ti-arrow-left me-2"></i>{{ __('Previous') }}
                </button>
                <div></div>
                <button type="button" class="btn btn-secondary btn-navigation" id="next-btn">
                    {{ __('Next') }}<i class="ti ti-arrow-right ms-2"></i>
                </button>
                <button type="submit" class="btn btn-secondary btn-navigation" id="submit-btn" style="display: none;">
                    <i class="ti ti-check me-2"></i>{{ __('Start Your Journey') }}
                </button>
                <button type="button" class="btn btn-outline-secondary btn-navigation" id="skip-btn" style="display: none;">
                    {{ __('Skip KYC & Continue') }}<i class="ti ti-arrow-right ms-2"></i>
                </button>
            </div>

            {{ Form::close() }}

            <hr class="mt-4" />
            <h5 class="d-flex justify-content-center">{{__('Already have an account?')}} <a class="ms-1 text-secondary"
                    href="{{ route('login') }}">{{ __('Login in here') }}</a>
            </h5>
        </div>
    </div>
@endsection
