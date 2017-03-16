<?php 

$app->get('/', ['\SlimStarter\Controllers\HomeController', 'index'])->setName('home');

$app->get('/signup', ['\SlimStarter\Controllers\Auth\RegistrationController', 'getSignUp'])->setName('auth.signup');
$app->post('/signup', ['\SlimStarter\Controllers\Auth\RegistrationController', 'postSignUp'])->setName('auth.signup');

$app->post('/resend-token', ['\SlimStarter\Controllers\Auth\EmailVerificationController', 'resendToken'])->setName('auth.resend.token');
$app->get('/s', ['\SlimStarter\Controllers\Auth\EmailVerificationController', 's']);

$app->get('/email-verify', ['\SlimStarter\Controllers\Auth\EmailVerificationController', 'emailVerify'])->setName('auth.verify');

$app->get('/signin', ['\SlimStarter\Controllers\Auth\AuthController', 'getSignIn'])->setName('auth.signin');
