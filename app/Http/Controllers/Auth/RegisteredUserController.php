<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = \App\Models\User::find(1);
        \App::setLocale($user->lang);
        $registerPage = getSettingsValByName('register_page');

        if ($registerPage == 'on') {
            $menu = Page::where('slug', 'terms_conditions')->first();
            return view('auth.register', compact('menu'));
        } else {
            return view('auth.login');
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $google_recaptcha = getSettingsValByName('google_recaptcha');
        if ($google_recaptcha == 'on') {
            $validation['g-recaptcha-response'] = 'required|captcha';
        } else {
            $validation = [];
        }
        $this->validate($request, $validation);

        // Step 1: Basic user validation
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['required', 'string', 'max:20'],
            
            // Step 2: Business profile validation
            'business_type' => ['required', 'integer', 'exists:categories,id'],
            'service_country' => ['required', 'string', 'max:2'],
            'service_zipcode' => ['required', 'string', 'max:20'],
            'service_city' => ['required', 'string', 'max:100'],
            'service_address' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'max:1000'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:20'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // Step 3: KYC validation (optional)
            'document_type' => ['nullable', 'string'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_front' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'document_back' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'kyc_terms_accepted' => ['nullable', 'boolean'],
            
            // Step 4: Terms & Conditions validation (required)
            'terms_accepted' => ['required', 'accepted'],
            'privacy_accepted' => ['required', 'accepted'],
            'marketing_emails' => ['nullable', 'boolean'],
        ]);
        
        // Get default subscription (Basic plan)
        $defaultSubscription = \App\Models\Subscription::find(1);
        
        // Calculate trial expiry date based on subscription settings
        $trialDays = $defaultSubscription ? $defaultSubscription->getTrialDays() : 30;
        $subscriptionExpireDate = $trialDays > 0 
            ? Carbon::now()->addDays($trialDays)->isoFormat('YYYY-MM-DD')
            : Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD'); // fallback
        
        // Create user
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'type' => 'owner',
            'lang' => 'english',
            'subscription' => 1,
            'subscription_expire_date' => $subscriptionExpireDate,
            'parent_id' => 1,
        ];
        
        $owner_email_verification = getSettingsValByName('owner_email_verification');
        $owner = User::create($userData);
        $userRole = Role::findByName('owner');
        $owner->assignRole($userRole);
        
        // Get the selected subcategory and its parent category
        $selectedSubcategory = \App\Models\Category::find($request->business_type);
        $parentCategory = $selectedSubcategory ? $selectedSubcategory->parent : null;

        // Create business profile
        $businessProfileData = [
            'user_id' => $owner->id,
            'business_type' => $request->business_type, // Keep for backward compatibility
            'category_id' => $parentCategory ? $parentCategory->id : null,
            'subcategory_id' => $selectedSubcategory ? $selectedSubcategory->id : null,
            'service_country' => $request->service_country,
            'service_zipcode' => $request->service_zipcode,
            'service_city' => $request->service_city,
            'service_address' => $request->service_address,
            'bio' => $request->bio,
            'business_name' => $request->business_name,
            'business_phone' => $request->business_phone,
            'business_address' => $request->business_address,
            'website' => $request->website,
            'is_completed' => true,
        ];
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoPath = $logoFile->store('business_logos', 'public');
            $businessProfileData['logo'] = $logoPath;
        }
        
        $businessProfile = \App\Models\BusinessProfile::create($businessProfileData);
        
        // Create KYC document if provided
        if ($request->filled('document_type') || $request->hasFile('document_front')) {
            $kycData = [
                'user_id' => $owner->id,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'terms_accepted' => $request->has('kyc_terms_accepted'),
                'privacy_accepted' => true, // Assuming privacy is accepted with terms
                'ip_address' => $request->ip(),
                'status' => 'draft',
            ];
            
            // Handle document uploads
            if ($request->hasFile('document_front')) {
                $frontFile = $request->file('document_front');
                $frontPath = $frontFile->store('kyc_documents', 'public');
                $kycData['document_front'] = $frontPath;
            }
            
            if ($request->hasFile('document_back')) {
                $backFile = $request->file('document_back');
                $backPath = $backFile->store('kyc_documents', 'public');
                $kycData['document_back'] = $backPath;
            }
            
            // If documents are provided, mark as submitted
            if ($request->hasFile('document_front') && $request->document_type) {
                $kycData['status'] = 'submitted';
                $kycData['submitted_at'] = now();
            }
            
            \App\Models\KycDocument::create($kycData);
        }
        
        Auth::login($owner);
        defaultTemplate($owner->id);
        defaultClientCreate($owner->id);
        
        if ($owner_email_verification == 'on') {
            $token = sha1($owner->email);
            $url = route('email-verification', $token);

            $owner->email_verification_token = $token;
            $owner->save();

            $data = [
                'module' => 'email_verification',
                'subject' => 'Email Verification',
                'email' => $owner->email,
                'name' => $owner->name,
                'url' => $url,
            ];
            $to = $owner->email;
            $response = sendEmailVerification($to, $data);
            if ($response['status'] == 'success') {
                auth()->logout();
                return redirect()->route('login')->with('success', __('Registration completed! We have sent an account verification email to your registered email inbox. Please check your email and follow the instructions to verify your account.'));
            } else {
                $owner->delete();
                return redirect()->back()->with('error',  $response['message']);
            }
        } else {
            $module = 'owner_create';
            $setting = settings();
            if (!empty($owner)) {
                $data['subject'] = 'New User Created';
                $data['module'] = $module;
                $data['password'] = $request->password;
                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['url'] = env('APP_URL');
                $data['logo'] = $setting['company_logo'];
                $to = $owner->email;
                commonEmailSend($to, $data);
            }
            $owner->email_verified_at = now();
            $owner->email_verification_token = null;
            $owner->save();
            
            // Welcome message with registration completion info
            $welcomeMessage = __('Welcome to our platform! Your account has been created successfully.');
            if ($businessProfile) {
                $welcomeMessage .= ' ' . __('Your business profile is complete.');
            }
            if ($owner->kycDocument) {
                if ($owner->kycDocument->status === 'submitted') {
                    $welcomeMessage .= ' ' . __('Your KYC documents have been submitted for review.');
                } else {
                    $welcomeMessage .= ' ' . __('You can complete KYC verification from your profile settings.');
                }
            }
            
            return redirect(RouteServiceProvider::HOME)->with('success', $welcomeMessage);
        }
    }
}
