<?php
// Login Routes...
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'UserController@logout')->middleware('auth');
Route::get('/auth/token', 'Auth\LoginController@token');
// Registration Route...
Route::post('/register', 'Auth\RegisterController@register');
Route::get('/register/verify/{token}', 'Auth\RegisterController@verify');
// Руты для социалок
Route::get('/redirect/{provider}', 'SocialAuthController@redirect');
Route::get('/callback/{provider}', 'SocialAuthController@callback');

// Получить информацию о ресет пароля (токен и емеил)
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@getResetInfo');
// Послать письмо
Route::post('/password/email', ['as' => 'password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');

Route::post('/create', 'LessonController@newLesson')->middleware('auth');
Route::get('/view/{id}', 'LessonController@viewLesson');
Route::get('/edit/{id}', 'LessonController@editLesson')->middleware('auth');
Route::post('/lesson', 'LessonController@saveLesson')->middleware('auth');
Route::post('/lesson/copy', 'LessonController@copyLesson')->middleware('auth');
Route::post('/reserve', 'LessonController@reserveLesson')->middleware('auth');
Route::post('/publish/{id}', 'LessonController@publishLesson')->middleware('auth');
Route::post('/image/add', 'LessonController@addImage')->middleware('auth');
Route::post('/image/delete', 'LessonController@deleteImage')->middleware('auth');

Route::get('/reservedLessons', 'LessonController@getReservedLessons')->middleware('auth');

Route::get('/getStates/{code}', 'HomeController@getStates');
Route::get('/getCities/{stateCode}', 'HomeController@getCities');

Route::post('/profile/data', 'ProfileController@saveProfileData')->middleware('auth');
Route::post('/profile/email', 'ProfileController@setEmail')->middleware('auth');
Route::post('/profile/emailConfirmation', 'ProfileController@sendEmail')->middleware('auth');
Route::post('/profile/changeNotify', 'ProfileController@changeNotify')->middleware('auth');
Route::post('/profile/avatar', 'ProfileController@saveNewAvatar')->middleware('auth');
Route::get('/api/profile/commonReserves', 'ProfileController@getLessonReserves')->middleware('auth');
Route::get('/api/profile/lessons', 'ProfileController@getLessons')->middleware('auth');
Route::post('/profile/actions/userNotCome', 'ReservesController@initUserNotCome')->middleware('auth');

Route::post('/lessonReview', 'LessonController@saveLessonReview')->middleware('auth');

Route::post('/lesson/recommend', 'LessonController@recommendLesson')->middleware('auth');
Route::post('/lesson/delete', 'LessonController@deleteLesson')->middleware('auth');
Route::post('/lesson/notRecommend', 'LessonController@notRecommendLesson')->middleware('auth');

Route::get('/user/{id}', 'HomeController@getUserInfo');

Route::get('/start', 'HomeController@initApp');
Route::get('/findlessons', 'HomeController@findLessons');

Route::post('/findCities', 'HomeController@findCities');

Route::post('/channel', 'MessagesController@createChannel')->middleware('auth');
Route::get('/messages', 'MessagesController@getUserMessages')->middleware('auth');
Route::post('/messages', 'MessagesController@sendMessage')->middleware('auth');
Route::get('/messages/{rcptId}', 'MessagesController@getChannelMessages')->middleware('auth');

Route::get('/administrator', 'AdminController@index')->middleware('moder');
Route::get('/administrator/users', 'AdminController@loadUsers')->middleware('moder');
Route::get('/administrator/lessons', 'AdminController@loadLessons')->middleware('moder');
Route::get('/administrator/reserves', 'AdminController@loadReserves')->middleware('moder');
Route::get('/administrator/withdrawals', 'AdminController@getWithdrawals')->middleware('moder');
Route::post('/administrator/processWithdrawal', 'AdminController@processWithdrawal')->middleware('moder');
Route::post('/administrator/grantRights', 'AdminController@grantRights')->middleware('admin');
Route::post('/administrator/revokeRights', 'AdminController@revokeRights')->middleware('admin');
Route::post('/administrator/setUnique', 'AdminController@setUnique')->middleware('moder');
Route::post('/administrator/addNewsRecord', 'NewsController@addNewsRecord')->middleware('moder');
Route::post('/administrator/deleteNewsRecord', 'NewsController@removeNewsRecord')->middleware('moder');

Route::post('/sms/sendVerifyCode', 'SmsController@sendSmsCode')->middleware('auth');
Route::post('/sms/verifyCode', 'SmsController@verifyCode')->middleware('auth');

Route::get('/sendNews', 'MailController@sendNews');

Route::get('/news', 'NewsController@getLatestNews');
Route::get('/faq', 'FaqController@getFaq');
Route::post('/faq/new', 'FaqController@addFaq')->middleware('moder');
Route::post('/faq/delete', 'FaqController@deleteFaq')->middleware('moder');

Route::get('/feedback', 'FeedbackController@getFeedback')->middleware('moder');
Route::post('/feedback', 'FeedbackController@addFeedback');
Route::patch('/feedback', 'FeedbackController@answerFeedback')->middleware('moder');

Route::get('/reserves/getHistory', 'ReservesController@getHistory')->middleware('moder');
Route::get('/reserves/last_reserves_conversation', 'ReservesController@getLastResConv')->middleware('auth');

Route::post('/reserves/adminAnswer', 'ReservesController@adminAnswer')->middleware('moder');
Route::post('/reserves/close', 'ReservesController@closeReserve')->middleware('moder');

Route::get('/reserves/canAskUser', 'ReservesController@canAskUser')->middleware('moder');
Route::post('/reserves/reserveNotPassed', 'ReservesController@reserveNotPassed')->middleware('moder');
Route::post('/reserves/confirmNotCome', 'ReservesController@confirmNotCome')->middleware('auth');
Route::post('/reserves/userNotAgree', 'ReservesController@userNotAgree')->middleware('auth');
Route::post('/reserves/cancelReserve', 'ReservesController@cancelReserve')->middleware('auth');
Route::post('/reserves/answerToAdministration', 'ReservesController@answerToAdministration')->middleware('auth');

Route::get('/{vue_capture?}', function () {
    return view('index');
})->where('vue_capture', '[\/\w\.-]*');