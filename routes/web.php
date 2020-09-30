<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
//Route::middleware('auth')->group(function () { // TODO:
    Route::prefix('/payment')->group(function () {
        /*
         * Since this is an example the api will be written here for ease of visualization
         * Add the auth middleware and separate the API if needed.
         *
         * Vì đây là ví dụ nên API viết ở đây cho dễ hình dung.
         * Thêm middleware auth và tách riêng ra API nếu cần thiết.
         */
        // Step 1. API - create link token (thể tạo API cho app)
        Route::post('/create-link-token', 'App\Http\Controllers\PaymentController@plaidCreateLinkToken')->name('payment.create-link-token');
        // Step 2: show website - link to bank (nếu là API thì truyền APP truyền link_token ở bước 1 cho webview)
        Route::get('/plaid-link', 'App\Http\Controllers\PaymentController@plaidLink')->name('payment.plaid-link');
        // Step 3: Get access token and create Stripe connect account
        Route::post('/create-account', 'App\Http\Controllers\PaymentController@getAccessTokenAndCreateAccount')->name('payment.create-account');

        // Step 4: Success page (Success Connect account)
        // NOTE: App sẽ bắt url này ở webview để biết là thành công để xử lý bước tiếp theo ở app
        Route::post('/connect-success', 'App\Http\Controllers\PaymentController@connectSuccess')->name('payment.connect-success');

        // Step 5: API - withdrawal
        Route::get('/withdrawal', 'App\Http\Controllers\PaymentController@withdrawal')->name('payment.withdrawal');

        // TODO: Cân 1 API để lấy thông tin bank name để hiển thị ở app nếu cần.
    });
//});
