<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyReCaptchaToken;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginDetail;
use App\Models\Utility;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use WhichBrowser\Parser;

class AuthenticatedSessionController extends Controller
{
    public function __construct()
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        }
    }

    /**
     * Show login form
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function store(LoginRequest $request)
    {
        $settings = Utility::settings();
        $validation = [];

        // Recaptcha validation (if enabled)
        if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] === 'yes') {
            if ($settings['google_recaptcha_version'] === 'v2-checkbox') {
                $validation['g-recaptcha-response'] = 'required';
            } elseif ($settings['google_recaptcha_version'] === 'v3') {
                $result = event(new VerifyReCaptchaToken($request));
                if (!isset($result[0]['status']) || $result[0]['status'] !== true) {
                    $request->merge(['g-recaptcha-response' => null]);
                    $validation['g-recaptcha-response'] = 'required';
                }
            }
        }

        $this->validate($request, $validation);

        $remember = $request->filled('remember');

        // ✅ Check login success
        if (!Auth::attempt($request->only('email', 'password'), $remember)) {
            return back()->with('error', __('Invalid email or password.'))->withInput();
        }

        if ($remember) {
            config(['session.lifetime' => 43200]); // 30 days
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Account status checks
        if ($user->is_active == 0 || $user->is_disable == 0) {
            auth()->logout();
            return redirect()->back()->with('error', __('Your account is disabled.'));
        }

        // Terminated employee check
        if ($user->type === 'employee') {
            $employee = Employee::where('user_id', $user->id)->first();
            if ($employee) {
                $termination = \App\Models\Termination::where('employee_id', $employee->id)->first();
                if ($termination) {
                    auth()->logout();
                    return redirect()->route('login')->with('error', __('Your account has been terminated. Please contact administrator.'));
                }
            }
        }

        // Company user plan validation
        if ($user->type === 'company') {
            $plan = Plan::find($user->plan);
            if ($plan && $plan->duration !== 'Lifetime') {
                $datetime1 = new \DateTime($user->plan_expire_date);
                $datetime2 = new \DateTime(date('Y-m-d'));
                $interval = $datetime2->diff($datetime1);
                $days = $interval->format('%r%a');

                if ($days <= 0) {
                    $user->assignplan(1);
                    return redirect()->intended(RouteServiceProvider::HOME)->with('error', __('Your plan is expired.'));
                }
            }
        }

        // Company user downgrade to free plan if needed
        if ($user->type === 'company') {
            $free_plan = Plan::where('price', '=', '0.0')->first();
            $plan = Plan::find($user->plan);

            if ($user->plan != $free_plan->id && $plan->duration !== 'Lifetime' && date('Y-m-d') > $user->plan_expire_date) {
                $user->plan = $free_plan->id;
                $user->plan_expire_date = null;
                $user->save();

                $users = User::where('created_by', $user->creatorId())->get();
                $employees = Employee::where('created_by', $user->creatorId())->get();

                $this->updatePlanUserLimits($users, $employees, $free_plan);

                return redirect()->route('dashboard')->with('error', __('Your plan expired. Please upgrade your plan.'));
            }
        }

        // Save login details for non-admin users
        if (!in_array($user->type, ['company', 'super admin'])) {
            $this->saveLoginDetails($user);
        }

        // Redirect employees based on approval status
        if ($user->type === 'employee') {
            $employee = Employee::where('user_id', $user->id)->first();
            if ($employee) {
                // If employee is not approved, redirect to their profile page
                if ($employee->approval_status !== 'approved') {
                    return redirect()->route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id));
                }
                // If approved, they will go to dashboard (normal flow)
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Show login form in specific language
     */
    public function showLoginForm($lang = '')
    {
        if ($lang == '') {
            $lang = Utility::getValByName('default_language');
        }
        \App::setLocale($lang);
        return view('auth.login', compact('lang'));
    }

    /**
     * Forgot password form
     */
    public function showLinkRequestForm($lang = '')
    {
        if ($lang == '') {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);
        return view('auth.forgot-password', compact('lang'));
    }

    /**
     * Handle password reset request
     */
    public function storeLinkRequestForm(Request $request)
    {
        $settings = Utility::settings();
        $validation = [];

        if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes') {
            $validation['g-recaptcha-response'] = 'required';
        }

        $this->validate($request, $validation);

        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('E-Mail has not been sent due to SMTP configuration.');
        }
    }

    /**
     * Logout user
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Update user and employee plan limits when downgrading
     */
    private function updatePlanUserLimits($users, $employees, $free_plan)
    {
        if ($free_plan->max_users == -1) {
            foreach ($users as $user) {
                $user->is_active = 1;
                $user->save();
            }
        } else {
            foreach ($users as $index => $user) {
                $user->is_active = ($index < $free_plan->max_users) ? 1 : 0;
                $user->save();
            }
        }

        if ($free_plan->max_employees == -1) {
            foreach ($employees as $employee) {
                $employee->is_active = 1;
                $employee->save();
            }
        } else {
            foreach ($employees as $index => $employee) {
                $employee->is_active = ($index < $free_plan->max_employees) ? 1 : 0;
                $employee->save();
            }
        }
    }

    /**
     * Save login details
     */
    private function saveLoginDetails($user)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
        $whichbrowser = new Parser($_SERVER['HTTP_USER_AGENT'] ?? '');
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : [];

        $query['browser_name'] = $whichbrowser->browser->name ?? null;
        $query['os_name'] = $whichbrowser->os->name ?? null;
        $query['browser_language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
        $query['device_type'] = Utility::get_device_type($_SERVER['HTTP_USER_AGENT'] ?? '');
        $query['referrer_host'] = $referrer['host'] ?? null;
        $query['referrer_path'] = $referrer['path'] ?? null;

        if (isset($query['timezone'])) {
            date_default_timezone_set($query['timezone']);
        }

        $json = json_encode($query);

        LoginDetail::create([
            'user_id' => $user->id,
            'ip' => $ip,
            'date' => now(),
            'Details' => $json,
            'created_by' => $user->creatorId(),
        ]);
    }
}
