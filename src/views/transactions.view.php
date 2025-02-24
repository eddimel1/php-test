<?php $this->startSection('title'); ?>


Transactions
<?php $this->endSection(); ?>

<?php $this->startSection('content'); ?>

<h2 style="margin-bottom:1rem;">Transactions</h2>

<div class="card" >

<simple-table  
    headers='[
        {"value": "id", "text": "Transaction ID"},
         {"value": "created_at", "text": "Date"},
        {"value": "login", "text": "User"},
        {"value": "transaction_type", "text": "Type"},
        {"value": "amount", "text": "Amount"},
        {"value": "currency", "text": "currency"}
    ]' 
    data='<?= htmlspecialchars($transactions) ?>' 
    unique-column="id"  >

</simple-table>

<script src="/components/table.js"></script>
<script src="/components/custom-select.js"></script>
</div>

<?php $this->endSection(); ?>