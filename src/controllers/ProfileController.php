<?php

namespace Controllers;

use PDO;
use Exception;

class ProfileController extends Controller {
    public function index() {
       
        $userId = $this->session->get('user_id');  

        $query = $this->db->pdo->prepare('
            SELECT 
                u.id, u.login, u.name, u.gender_id, u.birth_date, 
                g.name AS gender_name, 
                cu.name AS currency_name, ub.balance , ub.active
            FROM users u
            LEFT JOIN gender g ON u.gender_id = g.id
            LEFT JOIN user_balances ub ON u.id = ub.user_id
            LEFT JOIN currency cu ON ub.currency_id = cu.id
            WHERE u.id = :user_id
        ');
        

        $query->execute(['user_id' => $userId]);
        $userData = $query->fetchAll();

        if (empty($userData)) {
            http_response_code(404);
            echo "User not found.";
            return;
        }

        $user = [
            'id' => $userData[0]['id'],
            'login' => $userData[0]['login'],
            'name' => $userData[0]['name'],
            'gender_id' => $userData[0]['gender_id'],
            'birth_date' => $userData[0]['birth_date'],
            'gender_name' => $userData[0]['gender_name'],
        ];

   
        $balances = [];
        foreach ($userData as $data) {
            if ($data['currency_name']) {
                $balances[$data['currency_name']] = [
                    'currency' => $data['currency_name'],
                    'balance' => $data['balance'],
                    'active' => $data['active']
                ];
            }
        }
        $balances = array_values($balances); 

       
        $query = $this->db->pdo->prepare('
            SELECT ct.name AS contact_type, uc.contact_value
            FROM user_contacts uc
            LEFT JOIN contact_type ct ON uc.contact_type_id = ct.id
            WHERE uc.user_id = :user_id
        ');
        $query->execute(['user_id' => $userId]);
        $contacts = $query->fetchAll(PDO::FETCH_ASSOC);

        
        return $this->view->render('profile', [
            'user' => $user,
            'contacts' => $contacts,
            'balances' => $balances
        ]);
    }

    public function switchActiveBalance() {
        $userId = $this->session->get('user_id');
        $input = $this->request->all();
        $currency = isset($input['currency']) ? $input['currency'] : '';
    
        if ($currency) {
            $this->db->pdo->beginTransaction();
    
            try {
                
                $query = $this->db->pdo->prepare('
                    SELECT ub.id 
                    FROM user_balances ub
                    JOIN currency c ON ub.currency_id = c.id
                    WHERE ub.user_id = :user_id AND c.name = :currency
                ');
                $query->execute(['user_id' => $userId, 'currency' => $currency]);
                $balance = $query->fetch();
    
                if ($balance) {
                   
                    $query = $this->db->pdo->prepare('
                        UPDATE user_balances 
                        SET active = FALSE 
                        WHERE user_id = :user_id
                    ');
                    $query->execute(['user_id' => $userId]);
    
                    $query = $this->db->pdo->prepare('
                        UPDATE user_balances 
                        SET active = TRUE 
                        WHERE id = :balance_id AND user_id = :user_id
                    ');
                    $query->execute([
                        'balance_id' => $balance['id'],
                        'user_id' => $userId
                    ]);
    
                    
                    $this->db->pdo->commit();
    
                 
                    $query = $this->db->pdo->prepare('
                        SELECT ub.balance, c.name AS currency, c.id AS currency_id
                        FROM user_balances ub
                        JOIN currency c ON ub.currency_id = c.id
                        WHERE ub.user_id = :user_id AND ub.active = TRUE
                    ');
                    $query->execute(['user_id' => $userId]);
                    $activeBalance = $query->fetch();
    
                   
                    if ($activeBalance) {
                        $this->session->set('balance', $activeBalance['balance']);
                        $this->session->set('currency', $activeBalance['currency']);
                        $this->session->set('currency_id', $activeBalance['currency_id']);
                    }
    
                   
                    return $this->response->json(['success' => true]);
                } else {
                    return $this->response->json(['success' => false, 'message' => 'Currency not found in your balances.']);
                }
            } catch (Exception $e) {
                
                $this->db->pdo->rollBack();
                return $this->response->json(['success' => false, 'message' => 'Failed to switch balance.']);
            }
        } else {
            return $this->response->json(['success' => false, 'message' => 'Invalid data.']);
        }
    }
    

    
}


