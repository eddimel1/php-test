<?php $this->startSection('title'); ?>


Events
<?php $this->endSection(); ?>

<?php $this->startSection('content'); ?>
<div id='userData' data-user-balance='<?=  $user_balance ?>'></div>
<h2 style="margin-bottom:1rem;">Events</h2>

<div class="card" >

<simple-table  
    headers='[
        {"value": "name", "text": "Event Name"},
        {"value": "event_date", "text": "Date"},
        {"value": "team1_win", "text": "Team 1 Win"},
        {"value": "draw", "text": "Draw"},
        {"value": "team2_win", "text": "Team 2 Win"},
        {"value": "oddsTypes", "text": "Outcome"},
        {"value": "amount", "text": "Amount"}
    ]' 
    data='<?= htmlspecialchars($events) ?>' 
    unique-column="id"
    slot-amount="<input {id} type='number' value='0'>"
    slot-oddsTypes="<custom-select {id}  label-key='name'
    value-key='id' items='<?= htmlspecialchars($odds_types) ?>'></custom-select>"

    action="true"

    action-text="Bet!"  >

</simple-table>

 
</div>

<script src="/components/table.js"></script>
<script src="/components/custom-select.js"></script>


<script>

  document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('simple-table');

    table.addEventListener('action-clicked', function(event) {
        const rowData = event.detail;

        const userBalance = JSON.parse(document.getElementById('userData').getAttribute('data-user-balance'))
       
        const odds = rowData.oddsTypes === '1' ? rowData.team1_win : rowData.oddsTypes === '2' ? rowData.draw : rowData.team2_win
    
        const betData = {
            event_id: rowData.id,            
            odds: odds,       
            odds_type_id: rowData.oddsTypes, 
            amount: rowData.amount,          
            currency_id: userBalance.currency_id,                                       
        };

        fetch('/bets', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(betData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
              
                const data = fetch('/events-ajax').then(response => response.json()).then((data)=>{

                  document.querySelector('simple-table').dispatchEvent(new CustomEvent('update-data', {detail: data}));

                }).then(()=>{
                  
                  alert('Bet placed successfully!');
                })
            } else {
                alert('Failed to place bet: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});

</script>
<?php $this->endSection(); ?>

