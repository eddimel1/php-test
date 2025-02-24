<?php

use Controllers\{
    EventsController,
    ProfileController,
    BetsController,
    TransactionsController,
    AuthController
};

use Middlewares\AuthMiddleware;

$app->router->get('/login', [AuthController::class, 'loginForm']);

$app->router->post('/login', [AuthController::class, 'login']);

$app->router->get('/events', [EventsController::class, 'index'], [AuthMiddleware::class]);

$app->router->get('/events-ajax', [EventsController::class, 'fetchEvents'], [AuthMiddleware::class]);

$app->router->post('/bets', [BetsController::class, 'betCreateAjax'], [AuthMiddleware::class]);

$app->router->get('/bets', [BetsController::class, 'index'], [AuthMiddleware::class]);

$app->router->get('/transactions', [TransactionsController::class, 'index'], [AuthMiddleware::class]);

$app->router->get('/profile', [ProfileController::class, 'index'], [AuthMiddleware::class]);

$app->router->post('/profile/switch-active-balance',[ProfileController::class,'switchActiveBalance'] , [AuthMiddleware::class]);

$app->router->get('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
