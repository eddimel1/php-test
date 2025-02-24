<?php

namespace Middlewares;

class AuthMiddleware 
{
    public function handle($request)
    {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        return false;
    }
}