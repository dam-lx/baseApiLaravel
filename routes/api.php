<?php
    
    /*
    |--------------------------------------------------------------------------
    | Api Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register Api routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your Api!
    |
    */
    Route::match(array('GET','POST'), '/api/v1/auth/login', 'Auth\UserController@login')->name('api_v1_login_call');
    Route::match(array('GET','POST'), '/api/v1/register-normal', 'Auth\UserController@registerNormal')->name('api_v1_register_call');
    Route::match(array('GET','POST'), '/api/v1/forgot-password', 'Auth\UserController@forgotPassword')->name('api_v1_forgot_password_call');
    Route::match(array('GET','POST'), '/api/v1/job', 'Auth\UserController@getJob')->name('api_v1_get_country_call');
    Route::match(array('GET','POST'), '/api/v1/pref', 'Auth\UserController@getPref')->name('api_v1_get_job_call');
    Route::match(array('GET','POST'), '/api/v1/pref-and-job', 'Auth\UserController@getPrefAndJob')->name('api_v1_get_pref_job_call');
    Route::match(array('GET','POST'), '/api/v1/news', 'ProductController@news')->name('api_v1_news_call');
    
    Route::group(["prefix" => "/api/v1/user",'middleware' => ['auth:api']], function ()
    {
        Route::match(array('GET','POST'), 'logout', 'Auth\UserController@logout')->name('api.v1.logout.call');
        Route::match(array('GET','POST'), 'info', 'Auth\UserController@UserInfo')->name('api.v1.userInfor.call');
        Route::match(array('PUT','POST'), 'update', 'Auth\UserController@UserUpdate')->name('api.v1.userUpdate.call');
        Route::match(array('DELETE'), 'delete', 'Auth\UserController@destroy')->name('api.v1.userDelete.call');
        Route::match(array('GET','POST'), 'favorites', 'Auth\UserController@getListFavorite')->name('api.v1.favorites.call');
        Route::match(array('DELETE'), 'favorite/{id}', 'Auth\UserController@deleteProductFavorite')->name('api.v1.deleteFavorite.call');
        Route::match(array('GET'), 'delivery', 'ShippingController@index')->name('api.v1.getListShippingAddress.call');
        Route::match(array('POST'), 'delivery', 'ShippingController@create')->name('api.v1.addShippingAddress.call');
        Route::match(array('GET'), 'delivery/{id}', 'ShippingController@detail')->name('api.v1.getShippingAddress.call');
        Route::match(array('PUT'), 'delivery/{id}', 'ShippingController@edit')->name('api.v1.editShippingAddress.call');
        Route::match(array('DELETE'), 'delivery/{id}', 'ShippingController@delete')->name('api.v1.deleteShippingAddress.call');
        Route::match(array('GET'), 'orders', 'Auth\UserController@orderHistory')->name('api.v1.orderHistory.call');
        Route::match(array('GET'), 'order/{id}', 'Auth\UserController@orderDetail')->name('api.v1.orderDetail.call');
        Route::match(array('GET'), 'mails/{orderId}', 'Auth\UserController@mailHistory')->name('api.v1.mailHistory.call');
        Route::match(array('GET'), 'mail/{id}', 'Auth\UserController@mailDetail')->name('api.v1.mailDetail.call');
    });
    
    Route::group(["prefix" => "/api/v1/product"], function ()
    {
        Route::get('list', 'ProductController@getList')->name('api.v1.product.list');
        Route::get('list-html', 'ProductController@getListFromHtml')->name('api.v1.product.list_html');
        Route::get('detail/{id?}', 'ProductController@detail')->name('api.v1.product.detail');
        Route::get('shipping-address/{id?}', 'ProductController@getShippingAddress')->name('api.v1.product.shipping_address');
        Route::post('shipping-address/{id?}', 'ProductController@editShippingAddress');
        Route::post('add-to-favorite/{id?}', 'ProductController@addToFavorite')->name('api.v1.product.addToFavorite')->middleware('auth:api');
        Route::post('add-to-cart/{id?}', 'ProductController@addToCart')->name('api.v1.product.addToCart');
        Route::post('payment/{id?}', 'ProductController@payment')->name('api.v1.product.payment');
        Route::post('contact', 'ProductController@contact');
        Route::get('content-contact', 'ProductController@getContentContact');
        Route::get('standard2', 'ProductController@getClassCategory2');
        Route::get('price', 'ProductController@getPriceClassProduct');
    });
    
    Route::match(array('GET','POST'), '/api/v1/cart', 'ProductController@getCart');
    Route::match(array('GET', 'POST'), '/api/v1/order', 'ShoppingController@order')->name('api.v1.order.detail');
    
    Route::group(["prefix" => "/api/v1/shopping"], function ()
    {
        Route::match(array('GET','POST'),'detail', 'ShoppingController@detail')->name('api.v1.shopping.detail');
        Route::match(array('GET','POST'),'check-order', 'ShoppingController@checkOrder')->name('api.v1.check.order.detail');
        Route::get('payment', 'ShoppingController@getPaymentInfo')->name('api.v1.shopping.payment');
        Route::post('payment', 'ShoppingController@payment');
        Route::post('change-address', 'ShoppingController@changeAddrShopping')->name('api.v1.change.address.detail');
        Route::post('change-delivery', 'ShoppingController@changeDeliveryShopping')->name('api.v1.change.delivery.detail');
        Route::post('change-payment', 'ShoppingController@changePaymentShopping')->name('api.v1.change.payment.detail');
        Route::post('change-addr-nonmember', 'ShoppingController@changeAddrNonMember')->name('api.v1.change.addr.nonmember');
        Route::get('/sln_card_payment', 'ShoppingController@allCategory')->name('api.v1.category.all');
    });
    
    
    Route::group(["prefix" => "/api/v1/category"], function ()
    {
        Route::get('home', 'CategoryController@homeCategory')->name('api.v1.category.home');
        Route::get('all', 'CategoryController@allCategory')->name('api.v1.category.all');
    });
    
    Route::match(array('GET','POST'), '/api/v1/address-by-postcode', 'Auth\UserController@GetAddressByPostcode');
    
    Route::group(["prefix" => "help"], function ()
    {
        Route::get('agreement','HelpController@agreement');
        Route::get('about','HelpController@about');
        Route::get('privacy','HelpController@privacy');
        Route::get('tradelaw','HelpController@tradelaw');
    });
