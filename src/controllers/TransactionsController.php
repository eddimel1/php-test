<?php

namespace Controllers;

class TransactionsController extends Controller {

    public function index() {
        
        $userId = (int)$this->session->get('user_id');

        $transactions = $this->getTransactionsByUserId($userId);

        return $this->view->render('transactions', ['transactions' => json_encode($transactions)]);
    }

    private function getTransactionsByUserId($userId) {
        try {
            $query = "SELECT 
            t.id, 
            t.amount / 100 AS amount,
            t.created_at, 
            tt.name AS transaction_type, 
            c.name AS currency,
            us.login,
            b.event_id, 
            e.name AS event_name
            FROM transactions t
            LEFT JOIN users us ON us.id = t.user_id
            LEFT JOIN transaction_type tt ON t.transaction_type_id = tt.id
            LEFT JOIN currency c ON t.currency_id = c.id
            LEFT JOIN bets b ON t.bet_id = b.id
            LEFT JOIN events e ON b.event_id = e.id
            WHERE t.user_id = :user_id
            ORDER BY t.created_at DESC;";

            $stmt = $this->db->pdo->prepare($query);
            $stmt->execute([':user_id' => $userId]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            return [];
        }
    }
}
