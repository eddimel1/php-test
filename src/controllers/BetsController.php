<?php

namespace Controllers;

use Exception;
use PDO;

class BetsController extends Controller {

    public function index(){

    
        $data = $this->getBetsByUserId($this->session->get('user_id'));

        return $this->view->render('bets',['bets' => json_encode($data)]);
    }

    public function getBetsByUserId($userId) {
        try {
           
            $query = "SELECT b.odds, b.amount / 100 AS amount, u.login, e.name AS event_name, e.league, od.name AS odds_name, bs.name AS bet_status , cur.name as currency
            FROM bets b 
            LEFT JOIN users u ON b.user_id = u.id
            LEFT JOIN events e ON b.event_id = e.id
            LEFT JOIN currency cur ON cur.id = b.currency_id
            LEFT JOIN odds_types od ON b.odds_type_id = od.id
            LEFT JOIN bet_status bs ON b.status_id = bs.id
            WHERE b.user_id = :user_id";

    
            $stmt = $this->db->pdo->prepare($query);
    
            $stmt->execute([':user_id' => $userId]);

            $bets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $bets;
           
        } catch (Exception $e) {
            
            return false;
        }
    }
    

    

    public function betCreateAjax() {
        try {
            
            $query = $this->db->pdo->query('SELECT * FROM public.system_config');
            $config = $query->fetchAll(PDO::FETCH_ASSOC);
    
            $configValues = [];
            foreach ($config as $item) {
                $configValues[$item['key']] = $item['value'];
            }
    
            $data = $this->request->json();
    
           
            if (!isset($data['odds'], $data['odds_type_id'], $data['amount'], $data['currency_id'], $data['event_id'])) {
                return $this->response->json(['success' => false, 'error' => 'Missing required fields']);
            }
    
           
            $amountInCents = intval($data['amount'] * 100);
    
           
            if ($amountInCents > $configValues['max_bet_amount'] || $amountInCents < $configValues['min_bet_amount']) {
                $message = 'Valid amount is between ' . ($configValues['min_bet_amount'] / 100) . ' and ' . ($configValues['max_bet_amount'] / 100);
                return $this->response->json(['success' => false, 'error' => $message]);
            }
    
            
            $userId = (int)$this->session->get('user_id');
            $currencyId = (int)$this->session->get('currency_id');

           
    

            $this->db->pdo->beginTransaction();
    
           
            $balanceQuery = "SELECT balance FROM user_balances WHERE user_id = :user_id AND currency_id = :currency_id FOR UPDATE";
            $stmt = $this->db->pdo->prepare($balanceQuery);
            $stmt->execute([':user_id' => $userId, ':currency_id' => $currencyId]);
            $userBalance = $stmt->fetch(PDO::FETCH_ASSOC);
    
           
            if (!$userBalance || $userBalance['balance'] < $amountInCents) {
                $this->db->pdo->rollBack();
                return $this->response->json(['success' => false, 'error' => 'Insufficient balance']);
            }
    
         
            $betQuery = "INSERT INTO bets (user_id, event_id, odds, odds_type_id, amount, currency_id, status_id) 
                         VALUES (:user_id, :event_id, :odds, :odds_type_id, :amount, :currency_id, :status_id)";
            $betParams = [
                ':user_id' => $userId,
                ':event_id' => (int)$data['event_id'], 
                ':odds' => $data['odds'],
                ':odds_type_id' => (int)$data['odds_type_id'],
                ':amount' => $amountInCents, 
                ':currency_id' => $currencyId,
                ':status_id' => 1
            ];
    
            $stmt = $this->db->pdo->prepare($betQuery);
            $stmt->execute($betParams);
    
            $betId = $this->db->pdo->lastInsertId();
    
            
            $transactionQuery = "INSERT INTO transactions (user_id, bet_id, transaction_type_id, amount, currency_id) 
                                 VALUES (:user_id, :bet_id, :transaction_type_id, :amount, :currency_id)";
            $transactionParams = [
                ':user_id' => $userId,
                ':bet_id' => $betId,
                ':transaction_type_id' => 2, 
                ':amount' => -$amountInCents, 
                ':currency_id' => $currencyId
            ];
    
            $stmt = $this->db->pdo->prepare($transactionQuery);
            $stmt->execute($transactionParams);
    
            $updateBalanceQuery = "UPDATE user_balances SET balance = balance - :amount WHERE user_id = :user_id AND currency_id = :currency_id";
            $updateBalanceParams = [
                ':amount' => $amountInCents,
                ':user_id' => $userId,
                ':currency_id' => $currencyId
            ];
    
            $stmt = $this->db->pdo->prepare($updateBalanceQuery);
            $stmt->execute($updateBalanceParams);

            $this->session->set('balance', $userBalance['balance'] - $amountInCents);

            $this->db->pdo->commit();

           
    
            return $this->response->json(['success' => true, 'message' => 'Bet placed successfully']);
    
        } catch (Exception $e) {
           
            $this->db->pdo->rollBack();
            return $this->response->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    
}