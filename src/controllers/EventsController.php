<?php

namespace Controllers;

use PDO;

class EventsController extends Controller {

    private function getEvents($userId) {
        // Fetch system config
        $config = $this->db->pdo->query('SELECT * FROM public.system_config')->fetchAll(PDO::FETCH_ASSOC);
        $configValues = [];
        foreach ($config as $item) {
            $configValues[$item['key']] = $item['value'];
        }

        $minOdds = (float)$configValues['min_odds'];
        $maxOdds = (float)$configValues['max_odds'];
        $minBetAmount = (int)$configValues['min_bet_amount']; // in cents
        $maxBetAmount = (int)$configValues['max_bet_amount']; // in cents

        
        $query = $this->db->pdo->prepare('
            SELECT e.*
            FROM public.events e
            LEFT JOIN public.bets b ON e.id = b.event_id AND b.user_id = :user_id
            WHERE b.event_id IS NULL
        ');
        $query->execute(['user_id' => $userId]);
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        // Add random odds and min/max bet amounts
        return array_map(function ($event) use ($minOdds, $maxOdds, $minBetAmount, $maxBetAmount) {
            $event['team1_win'] = round(mt_rand($minOdds * 100, $maxOdds * 100) / 100, 2);
            $event['team2_win'] = round(mt_rand($minOdds * 100, $maxOdds * 100) / 100, 2);
            $event['draw'] = round(mt_rand($minOdds * 100, $maxOdds * 100) / 100, 2);

            $event['min_bet_amount'] = $minBetAmount;
            $event['max_bet_amount'] = $maxBetAmount;

            return $event;
        }, $events);
    }

    public function index() {
        $userId = $this->session->get('user_id');

        
        $balance = [
            'currency' => $this->session->get('currency'),
            'currency_id' => $this->session->get('currency_id')
        ];

       
        $eventsWithOdds = $this->getEvents($userId);
        
        $statuses = $this->db->pdo->query('SELECT * FROM public.bet_status')->fetchAll(PDO::FETCH_ASSOC);
        $oddsTypes = $this->db->pdo->query('SELECT * FROM public.odds_types')->fetchAll(PDO::FETCH_ASSOC);

        return $this->view->render('events', [
            'events' => json_encode($eventsWithOdds),
            'statuses' => json_encode($statuses),
            'odds_types' => json_encode($oddsTypes),
            'user_balance' => json_encode($balance)
        ]);
    }

    public function fetchEvents() {
        $userId = $this->session->get('user_id');
        $eventsWithOdds = $this->getEvents($userId);

        return $this->response->json($eventsWithOdds);
        
    }
}
