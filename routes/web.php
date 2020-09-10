<?php



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/





/**

 * Auth routes

 */

Route::group(['namespace' => 'Auth'], function () {



    // Authentication Routes...

    Route::get('login', 'LoginController@showLoginForm')->name('login');

    Route::post('login', 'LoginController@login');

    Route::get('logout', 'LoginController@logout')->name('logout');



    // Registration Routes...

    if (config('auth.users.registration')) {

        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');

        Route::post('register', 'RegisterController@register');

    }



    // Password Reset Routes...

    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');

    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');

    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');

    Route::post('password/reset', 'ResetPasswordController@reset');



    // Confirmation Routes...

    if (config('auth.users.confirm_email')) {

        Route::get('confirm/{user_by_code}', 'ConfirmController@confirm')->name('confirm');

        Route::get('confirm/resend/{user_by_email}', 'ConfirmController@sendEmail')->name('confirm.send');

    }



    // Social Authentication Routes...

    Route::get('social/redirect/{provider}', 'SocialLoginController@redirect')->name('social.redirect');

    Route::get('social/login/{provider}', 'SocialLoginController@login')->name('social.login');

});



/**

 * Backend routes

 */

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'admin'], function () {



    // Dashboard

    Route::get('/', 'DashboardController@index')->name('dashboard');



    //Users

    Route::get('users', 'UserController@index')->name('users');
    Route::get('pact-template', 'PactController@index')->name('pact-template');
    Route::post('pact-template/add', 'PactController@store')->name('pact-template.add');
    Route::get('pact-template/{pacttemplate}/edit', 'PactController@edit')->name('pact-template.edit');
    // Route::put('pact-template/edit', 'PactController@update')->name('pact-template.edit');
    Route::any('pact-template/{id}/destroy', 'PactController@destroy')->name('pact-template.destroy');
    Route::post('pact-template/addsection', 'PactController@addsection')->name('pact-template.addsection');
    Route::post('pact-template/addclauses', 'PactController@addclauses')->name('pact-template.addclauses');

    Route::get('reports', 'UserController@reports')->name('reports');

    /********** company route ***********/
    // Route::get('users/company', 'UserController@company')->name('company');
    // Route::post('users/company', 'UserController@store_company')->name('company');
    // Route::post('users/company_add', 'UserController@create_companyadd')->name('company-add');
    
    /********** company route ***********/




    Route::get('users/restore', 'UserController@restore')->name('users.restore');
    Route::get('users/create', 'UserController@create')->name('users.add');
    // Route::post('users/company', 'UserController@store_company')->name('company');
    Route::get('users/create-admin', 'UserController@create_admin')->name('users.add-admin');
    Route::post('users/create', 'UserController@store')->name('users.add');

    Route::get('users/{id}/restore', 'UserController@restoreUser')->name('users.restore-user');

    Route::get('users/{user}', 'UserController@show')->name('users.show');

    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');

    Route::put('users/update', 'UserController@update')->name('users.update');

    Route::any('users/{id}/destroy', 'UserController@destroy')->name('users.destroy');



    Route::get('permissions', 'PermissionController@index')->name('permissions');

    Route::get('permissions/{user}/repeat', 'PermissionController@repeat')->name('permissions.repeat');

    Route::get('dashboard/log-chart', 'DashboardController@getLogChartData')->name('dashboard.log.chart');

    Route::get('dashboard/registration-chart', 'DashboardController@getRegistrationChartData')->name('dashboard.registration.chart');
});



Route::get('/', 'HomeController@index');
Route::get('company/users', 'ComanyController@index')->name('company.users')->middleware('auth');
Route::get('company/add', 'ComanyController@create')->name('company.add')->middleware('auth');
Route::post('company/add', 'ComanyController@addstore')->name('company.add')->middleware('auth');
Route::any('company/{id}/destroy', 'ComanyController@destroy')->name('company.destroy')->middleware('auth');
Route::put('company/edit', 'ComanyController@update')->name('company.edit');
Route::post('company/pactshowlist', 'ComanyController@pactshowlist')->name('company.pactshowlist');
Route::post('company/checkphonenumber', 'ComanyController@checkphonenumber')->name('company.checkphonenumber');

Route::get('company/pactall', 'ComanyController@pactall')->name('company.pactall');
Route::get('company/pact', 'ComanyController@pact')->name('company.pact');
Route::get('company/{pacttemplate}/pactedit', 'ComanyController@pactedit')->name('company.pactedit');
Route::post('company/pact', 'ComanyController@pact_sectionadd')->name('company.pact');
Route::post('company/pact_clauses', 'ComanyController@pact_clauses')->name('company.pact_clauses');
Route::any('company/{id}/pact_destroy', 'ComanyController@pact_destroy')->name('company.pact_destroy');

Route::post('company/assign_userpact', 'ComanyController@assign_userpact')->name('company.assign_userpact');

Route::get('company/sendsms-bytwillo', 'ComanyController@sendsms_bytwillo')->name('company.sendsms-bytwillo');



Route::get('company/pact-template', 'ComanyController@pact_template')->name('company.pact-template');
Route::get('company/{pacttemplate}/pactexport', 'ComanyController@pactexport')->name('company.pactexport');
Route::post('company/pact_export_add', 'ComanyController@pact_export_add')->name('company.pact_export_add');
Route::get('company/{pacttemplate}/pact_templateview', 'ComanyController@pact_templateview')->name('company.pact_templateview');


Route::get('companyadmin/signees', 'CompanyadminController@index')->name('companyadmin.signees')->middleware('auth');
Route::post('companyadmin/assign_userpact', 'CompanyadminController@assign_userpact')->name('companyadmin.assign_userpact');
// Route::get('companyadmin/add', 'CompanyadminController@create')->name('companyadmin.add')->middleware('auth');
Route::post('companyadmin/add', 'CompanyadminController@addstore')->name('companyadmin.add')->middleware('auth');
Route::any('companyadmin/{id}/destroy', 'CompanyadminController@destroy')->name('companyadmin.destroy')->middleware('auth');
Route::put('companyadmin/edit', 'CompanyadminController@update')->name('companyadmin.edit');

Route::get('/testindex', 'HomeController@index123')->name('testindex');
Route::get('pact-signees-form/{id}', 'HomeController@pact_signees_form')->name('pact-signees-form');
Route::any('signees-pact-sections/{id}', 'HomeController@signees_pact_sections')->name('signees-pact-sections');
Route::post('signees-pact-signature/{id}', 'HomeController@signees_pact_signature')->name('signees-pact-signature');
Route::any('signature-template/{id}', 'HomeController@signature_template')->name('signature-template');
Route::any('thankyou/{id}', 'HomeController@signature_save')->name('thankyou');

Route::post('saveimage', 'HomeController@saveimage')->name('saveimage');

/** not valid page check */
// Route::get('pact-signees-form', 'HomeController@pact_signees_form')->name('pact-signees-form');
// Route::any('signees-pact-sections', 'HomeController@signees_pact_sections')->name('signees-pact-sections');
// Route::any('signature-template', 'HomeController@signature_template')->name('signature-template');
// Route::any('thankyou', 'HomeController@signature_save')->name('thankyou');
/** not valid page check */
/**

 * Membership

 */

Route::group(['as' => 'protection.'], function () {

    Route::get('membership', 'MembershipController@index')->name('membership')->middleware('protection:' . config('protection.membership.product_module_number') . ',protection.membership.failed');



    Route::get('membership/access-denied', 'MembershipController@failed')->name('membership.failed');

    Route::get('membership/product', 'MembershipController@product')->name('membership.product');

    Route::get('membership/clear-cache/', 'MembershipController@clearValidationCache')->name('membership.clear_validation_cache');

});

