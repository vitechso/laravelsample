<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Auth\User\User;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        /*
         * Remove the socialite session variable if exists
         */

        \Session::forget(config('access.socialite_session_name'));

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => __('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $errors = [];

        if (config('auth.users.confirm_email') && !$user->confirmed) {
            $errors = [$this->username() => __('auth.notconfirmed', ['url' => route('confirm.send', [$user->email])])];
        }

        if (!$user->active) {
            $errors = [$this->username() => __('auth.active')];
        }

        if ($errors) {
            auth()->logout();  //logout

            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }
        // echo $user->hasRole('company');exit;
        if($user->hasRole('administrator')){

            //echo "<pre>";print_r($user->email);die;
            User::where(["email"=>$user->email])->update(["last_login"=>date("Y-m-d H:i:s")]);
        //     // return redirect()->route('admin.dashboard');
            return redirect()->intended(route('admin.users'));
        // // exit;
        // }elseif($user->hasRole('company')){
        //     // return redirect()->route('artist.dashboard');
        //     return redirect()->intended($this->redirectPath());
        //     return redirect()->intended($this->redirectPath());
        }elseif($user->hasRole('company')){
            User::where(["email"=>$user->email])->update(["last_login"=>date("Y-m-d H:i:s")]);
            return redirect()->intended(route('company.users'));
        }elseif($user->hasRole('admin')){
            User::where(["email"=>$user->email])->update(["last_login"=>date("Y-m-d H:i:s")]);
            return redirect()->intended(route('companyadmin.signees'));
        }else{
        return redirect()->intended($this->redirectPath());
        }
    }
}
