<?php
use \Illuminate\Support\Facades\Route;

Route::get('/' ,                                                        ['as' => 'frontend.index' ,                                                        'uses' => 'Frontend\IndexController@index' ]);


// Authentication Routes...
Route::get('/login',                                                    ['as' => 'frontend.show_login_form',                                               'uses' => 'Frontend\Auth\LoginController@showLoginForm']);
Route::post('login',                                                    ['as' => 'frontend.login',                                                         'uses' => 'Frontend\Auth\LoginController@login']);
Route::post('logout',                                                   ['as' => 'frontend.logout',                                                        'uses' => 'Frontend\Auth\LoginController@logout']);
Route::get('register',                                                  ['as' => 'frontend.show_register_form',                                            'uses' => 'Frontend\Auth\RegisterController@showRegistrationForm']);
Route::post('register',                                                 ['as' => 'frontend.register',                                                      'uses' => 'Frontend\Auth\RegisterController@register']);
Route::get('password/reset',                                            ['as' => 'password.request',                                                       'uses' => 'Frontend\Auth\ForgotPasswordController@showLinkRequestForm']);
Route::post('password/email',                                           ['as' => 'password.email',                                                         'uses' => 'Frontend\Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::get('password/reset/{token}',                                    ['as' => 'password.reset',                                                         'uses' => 'Frontend\Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset',                                           ['as' => 'password.update',                                                        'uses' => 'Frontend\Auth\ResetPasswordController@reset']);
Route::get('email/verify',                                              ['as' => 'verification.notice',                                                    'uses' => 'Frontend\Auth\VerificationController@show']);
Route::get('/email/verify/{id}/{hash}',                                 ['as' => 'verification.verify',                                                    'uses' => 'Frontend\Auth\VerificationController@verify']);
Route::post('email/resend',                                             ['as' => 'verification.resend',                                                    'uses' => 'Frontend\Auth\VerificationController@resend']);


Route::group(['middleware' => 'verified'] , function (){
    Route::get('/dashboard',                                            ['as' => 'frontend.dashboard' ,                                                    'uses' => 'Frontend\UserController@index']);
    Route::get('/create/post',                                          ['as' => 'users.post.create' ,                                                     'uses' => 'Frontend\UserController@create_post']);
    Route::post('/store/post',                                          ['as' => 'users.post.store' ,                                                      'uses' => 'Frontend\UserController@store_post']);

    Route::get('/edit/post/{post_id}',                                  ['as' => 'users.post.edit' ,                                                       'uses' => 'Frontend\UserController@edit_post']);
    Route::put('/update/post/{post_id}',                                ['as' => 'users.post.update' ,                                                     'uses' => 'Frontend\UserController@update_post']);
    Route::post('/delete/post/media/{media_id}',                        ['as' => 'users.post.media.destroy' ,                                              'uses' => 'Frontend\UserController@destroy_post_media']);
    Route::delete('/delete/post/{post_id}',                             ['as' => 'users.post.destroy' ,                                                    'uses' => 'Frontend\UserController@destroy_post']);

    Route::get('/comments',                                             ['as' => 'users.comments' ,                                                        'uses' => 'Frontend\UserController@show_comments']);
    Route::get('/edit/comment/{comment_id}',                            ['as' => 'users.comments.edit' ,                                                   'uses' => 'Frontend\UserController@edit_comment']);
    Route::put('/update/comment/{comment_id}',                          ['as' => 'users.comment.update' ,                                                  'uses' => 'Frontend\UserController@update_comment']);
    Route::delete('/destroy/comment/{comment_id}',                      ['as' => 'users.comment.destroy' ,                                                 'uses' => 'Frontend\UserController@destroy_comment']);


    Route::get('/edit/info',                                            ['as' => 'users.info.edit' ,                                                       'uses' => 'Frontend\UserController@edit_info']);
    Route::post('/update/info',                                         ['as' => 'users.info.update' ,                                                     'uses' => 'Frontend\UserController@update_info']);
    Route::post('/update/password',                                     ['as' => 'users.password.update' ,                                                 'uses' => 'Frontend\UserController@update_password']);


    Route::any('user/notifications/get' , 'Frontend\NotificationsController@getNotifications');
    Route::any('user/notifications/read' , 'Frontend\NotificationsController@markAsRead');
    Route::any('user/notifications/read/{id}' , 'Frontend\NotificationsController@markAsReadAndRedirect');
});



Route::group(['prefix' => 'admin'] , function (){
    Route::get('/login',                                                ['as' => 'admin.show_login_form',                                            'uses' => 'Backend\Auth\LoginController@showLoginForm']);
    Route::post('login',                                                ['as' => 'admin.login',                                                      'uses' => 'Backend\Auth\LoginController@login']);
    Route::post('logout',                                               ['as' => 'admin.logout',                                                     'uses' => 'Backend\Auth\LoginController@logout']);
    Route::get('password/reset',                                        ['as' => 'password.request',                                                 'uses' => 'Backend\Auth\ForgotPasswordController@showLinkRequestForm']);
    Route::post('password/email',                                       ['as' => 'password.email',                                                   'uses' => 'Backend\Auth\ForgotPasswordController@sendResetLinkEmail']);
    Route::get('password/reset/{token}',                                ['as' => 'password.reset',                                                   'uses' => 'Backend\Auth\ResetPasswordController@showResetForm']);
    Route::post('password/reset',                                       ['as' => 'password.update',                                                  'uses' => 'Backend\Auth\ResetPasswordController@reset']);

    Route::group(['middleware' => ['roles' , 'role:admin|editor']],function (){
        Route::get('/' ,                                                ['as' => 'admin.index_route',                                                'uses' => 'Backend\AdminController@index']);
        Route::get('/index' ,                                            ['as' => 'admin.index',                                                      'uses' => 'Backend\AdminController@index']);

        Route::resource('posts' , 'Backend\PostsController' , ['as' => 'admin']);
        Route::resource('pages' , 'Backend\PagesController' , ['as' => 'admin']);
        Route::resource('post_comments' , 'Backend\PostCommentsController' , ['as' => 'admin']);
        Route::resource('post_categories' , 'Backend\PostCategoriesController' , ['as' => 'admin']);
        Route::resource('contact_us' , 'Backend\ContactUsController' , ['as' => 'admin']);
        Route::resource('users' , 'Backend\UsersController' , ['as' => 'admin']);
        Route::resource('supervisor' , 'Backend\SupervisorsController' , ['as' => 'admin']);
        Route::resource('settings' , 'Backend\SettingsController' , ['as' => 'admin']);


    });
});

Route::get('/post/show/{post}' ,                                        ['as' => 'post.show' ,                                                             'uses' => 'Frontend\IndexController@post_show' ]);
Route::get('/page/show/{page_slug}' ,                                   ['as' => 'page.show' ,                                                             'uses' => 'Frontend\IndexController@page_show' ]);
Route::post('/comment/add/{post_slug}' ,                                ['as' => 'comment.add' ,                                                              'uses' => 'Frontend\IndexController@comment_add' ]);
Route::get('/contact' ,                                                 ['as' => 'frontend.contact' ,                                                      'uses' => 'Frontend\ContactController@index' ]);
Route::post('/add/contact' ,                                            ['as' => 'do.contact' ,                                                            'uses' => 'Frontend\ContactController@do_contact' ]);
Route::get('/search' ,                                                  ['as' => 'frontend.search' ,                                                       'uses' => 'Frontend\IndexController@search' ]);


Route::get('/category/{category_slug}' ,                                ['as' => 'frontend.category.posts' ,                                               'uses' => 'Frontend\IndexController@category' ]);
Route::get('/archive/{date}' ,                                          ['as' => 'frontend.archive.posts' ,                                                'uses' => 'Frontend\IndexController@archive' ]);
Route::get('/author/{username}' ,                                       ['as' => 'frontend.author.posts' ,                                                 'uses' => 'Frontend\IndexController@author' ]);


