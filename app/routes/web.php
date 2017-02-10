<?php 

$app->get('/', ['\SlimStarter\Controllers\HomeController', 'index'])->setName('home');

$app->get('/signup', ['\SlimStarter\Controllers\Auth\AuthController', 'getSignUp'])->setName('auth.signup');
$app->post('/signup', ['\SlimStarter\Controllers\Auth\AuthController', 'postSignUp'])->setName('auth.signup');

$app->get('/signin', ['\SlimStarter\Controllers\Auth\AuthController', 'getSignIn'])->setName('auth.signin');
