<?php

// Public routes
Route::get('me', 'User\MeController@getMe');

// Get designs
Route::get('designs', 'Designs\DesignController@index');
Route::get('designs/{id}', 'Designs\DesignController@findDesign');

// Get users
Route::get('users', 'User\UserController@index');

// Teams
Route::get('teams/{id}', 'Teams\TeamController@findBySlug');

// Route group for authenticated users only
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');

    // Upload Designs
    Route::post('designs', 'Designs\UploadController@upload');
    Route::put('designs/{id}', 'Designs\DesignController@update');
    Route::delete('designs/{id}', 'Designs\DesignController@destroy');

    // Likes
    Route::post('designs/{id}/like', 'Designs\DesignController@like');
    Route::get('designs/{id}/liked', 'Designs\DesignController@checkIfUserHasLiked');

    // Comments
    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{id}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');

    // Teams
    Route::get('teams', 'Teams\TeamController@index');
    Route::post('teams', 'Teams\TeamController@store');
    Route::get('teams/{id}', 'Teams\TeamController@findById');
    Route::get('users/teams', 'Teams\TeamController@fetchUserTeams');
    Route::put('teams/{id}', 'Teams\TeamController@update');
    Route::delete('teams/{id}', 'Teams\TeamController@destroy');
    Route::delete('teams/{id}/user/{user_id}', 'Teams\TeamController@removeFromTeam');

    // Invitations
    Route::post('invitations/{teamId}', 'Teams\InvitationController@invite');
    Route::post('invitations/{id}/resend', 'Teams\InvitationController@resend');
    Route::post('invitations/{id}/respond', 'Teams\InvitationController@respond');
    Route::delete('invitations/{id}', 'Teams\InvitationController@destroy');

});

// Route group for guests only
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});
