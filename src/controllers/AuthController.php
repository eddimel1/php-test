<?php

namespace Controllers;

use PDO;

class AuthController extends Controller {

    public function loginForm(){
        return $this->view->render('login');
    }

    public function login()
    {    
        $input = $this->request->all(); 
    
        $query = $this->db->pdo->prepare('
            SELECT us.id, us.login, us.password_hash, cur.name AS currency_name, cur.id AS currency_id, ub.balance 
            FROM public.users us
            LEFT JOIN user_balances ub ON us.id = ub.user_id
            LEFT JOIN currency cur ON ub.currency_id = cur.id
            WHERE us.login = :login AND ub.active = TRUE
            LIMIT 1
        ');
    
        $query->bindParam(':login', $input['username'], PDO::PARAM_STR);
        $query->execute();
    
        $user = $query->fetch(PDO::FETCH_ASSOC);
    
        $_SESSION['error'] = "";
    
        if ($user && $input['password'] === $user['password_hash']) {
            
           
            $this->session->set('logged_in', true);
            $this->session->set('user_id', $user['id']);
            $this->session->set('login', $user['login']);
            $this->session->set('currency', $user['currency_name']);
            $this->session->set('currency_id', $user['currency_id']);
            $this->session->set('balance', $user['balance']);

            $this->response->redirect('/profile');
        } else {
            
            $_SESSION['error'] = "Invalid username or password";
            $this->response->redirect('/login');
        }
    }
    

    public function logout(){

        session_destroy();
        $this->response->redirect('/login');
    }
}