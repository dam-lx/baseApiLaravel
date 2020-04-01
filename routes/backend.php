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
 * backend module
 */
    //Template Controller
    Route::group(['prefix'=>'admin'],function(){
        //switch language
        Route::post('language', 'TemplateController@switchLanguage')->name('backend.switch_language');
        //Template Controller
        Route::group(['prefix' => 'template'],function(){
            Route::get('/', 'TemplateController@index')->name('backend_dashboard');
            
            Route::get('executeSchedule', 'TemplateController@executeSchedule')->name('backend_schedule_template');
    
            Route::get('form', 'TemplateController@form')->name('form_template');
    
            Route::get('form_component', 'TemplateController@form_component')->name('form_component_template');
            
            Route::get('form_notify', 'TemplateController@form_notify')->name('form_notify_template');
    
            Route::get('custom_component', 'TemplateController@custom_component')->name('custom_component_template');
            
            Route::get('components', 'TemplateController@components')->name('component_template');
            
            Route::get('card', 'TemplateController@card')->name('card_template');
            Route::get('widgets', 'TemplateController@widgets')->name('widgets_template');
            Route::get('buttons', 'TemplateController@buttons')->name('button_template');
            Route::get('chart', 'TemplateController@chart')->name('chart_template');
    
            Route::get('upload', 'TemplateController@upload')->name('upload_template');
            Route::get('images/s3', 'TemplateController@getImageFromS3')->name('getimage_s3_template');
            Route::get('images/local', 'TemplateController@getImageFromLocal')->name('getimage_local_template');
    
            Route::post('doUpload', 'TemplateController@doUpload')->name('doUpload_template');
            Route::post('doUploadS3', 'TemplateController@doUploadS3')->name('doUploadS3_template');
            Route::post('doDeleteFile', 'TemplateController@doDeleteFile')->name('doDeleteFile_template');
            Route::post('doDeleteFileS3', 'TemplateController@doDeleteFileS3')->name('doDeleteFileS3_template');
    
    
    
            Route::get('general-element', 'TemplateController@generalElement')->name('generalElement_template');
            
            Route::get('icons', 'TemplateController@icons')->name('icons_template');
            
            Route::get('glyphicons', 'TemplateController@glyphicons')->name('glyphicons_template');
            
            Route::get('calendar', 'TemplateController@calendar')->name('calendar_template');
            
            Route::get('tables', 'TemplateController@tables')->name('table_template');
            
            Route::get('data_table', 'TemplateController@data_table')->name('data_table_template');
            Route::get('exports', 'TemplateController@exports')->name('export_template');
    
            Route::get('doExports', 'TemplateController@doExports')->name('doExports_template');
    
            
            Route::get('loginPage', 'TemplateController@loginPage')->name('loginPage_template');
            
            Route::get('lockScreenPage', 'TemplateController@lockScreenPage')->name('lockScreenPage_template');
            
            Route::get('invoicePage', 'TemplateController@invoicePage')->name('invoicePage_template');
            
            Route::get('calendarPage', 'TemplateController@calendarPage')->name('calendarPage_template');
            
            Route::get('mailPage', 'TemplateController@mailPage')->name('mailPage_template');
            
            Route::get('errorPage', 'TemplateController@errorPage')->name('errorPage_template');
            
            Route::get('doExportsCommon/{type}', 'TemplateController@doExportsCommon')->name('doExportsCommon_template');
    
            Route::post('doImport', 'TemplateController@doImport')->name('doImport_template');
        });
        
        //User Controller
        Route::group(["prefix" => "user"],function(){
            //get view user
            Route::get("list",'UserController@getList')->name("backend.user.list");
            //get profile user
            Route::get("profile",'UserController@profile')->name("backend.user.profile");
            Route::post("profile",'UserController@editProfile');
            //get user and paginate
            Route::get("paginate",'UserController@paginate')->name("backend.user.paginate");
            //add user
            Route::get("add",'UserController@add')->name("backend.user.add");
            Route::post("add",'UserController@addPost');
            //edit user
            Route::get("edit",'UserController@getById')->name("backend.user.edit");
            Route::post("edit",'UserController@editPost')->name("editPost");
            //delete user
            Route::post("delete",'UserController@delete')->name("backend.user.delete");
        });
    });
    
