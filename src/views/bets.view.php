<?php $this->startSection('title'); ?>


Bets
<?php $this->endSection(); ?>

<?php $this->startSection('content'); ?>

<h2 style="margin-bottom:1rem;">Bets</h2>

<div class="card" >

<simple-table  
    headers='[
        {"value": "event_name", "text": "Bet"},
         {"value": "league", "text": "League"},
        {"value": "login", "text": "User"},
        {"value": "odds_name", "text": "Outcome"},
        {"value": "odds", "text": "Coefficent"},
        {"value": "amount", "text": "Amount"},
        {"value": "currency", "text": "Currency"},
        {"value": "bet_status", "text": "Status"}
    ]' 
    data='<?= htmlspecialchars($bets) ?>' 
    unique-column="id"  >

</simple-table>

<script src="/components/table.js"></script>
<script src="/components/custom-select.js"></script>
</div>

<?php $this->endSection(); ?>