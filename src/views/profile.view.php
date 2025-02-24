<?php $this->startSection('title'); ?>
Home Page
<?php $this->endSection(); ?>

<?php $this->startSection('content'); ?>

<h1>Profile</h1>

<div class="profile-wrapper">
   


    <div style="border:none" class="card">

      <div style="display:flex;gap:1rem">

      <div  class="card profile-card">
    <div class="card-header">
      <h2>Personal Information</h2>
      </div>
        <div class="card-content">
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Login:</strong> <?= htmlspecialchars($user['login']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender_name']) ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['birth_date']) ?></p>
        </div>
    </div>
    
    <div class="card profile-card">

<div class="card-header">
<h2>Contact Information</h2>
</div>
    
<div class="card-content">
<?php if (!empty($contacts)): ?>
        <ul>
            <?php foreach ($contacts as $contact): ?>
                <li ><strong><?= htmlspecialchars($contact['contact_type']) ?>:</strong> <?= htmlspecialchars($contact['contact_value']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No contact information available.</p>
    <?php endif; ?>
</div>
   
</div>

    </div>

      </div>

      <div style="width:100%" class="card">
    <div class="card-header">
        <div style="display:flex;justify-content:space-between">
            <h2>Account Balances</h2>
            <div style="display:flex;gap:5px">

                <button id="deposit">Deposit</button>
                <button id="change-balance">Change Balance</button>

            </div>
        </div>
    </div>
    <div class="card-content">
        <?php if (!empty($balances)): ?>
            <ul>
                <?php foreach ($balances as $balance): ?>
                    <li class="<?= $balance['active'] ? 'active' : '' ?>">

                        <strong>Currency:</strong> <?= htmlspecialchars($balance['currency']) ?> |
                        <strong>Balance:</strong> $<?= number_format($balance['balance'] / 100, 2) ?>
                        <input type="hidden" class="balance-id" value='<?= $balance['id'] ?>'>
                        <input type="hidden" class="currency-id" value='<?= $balance['currency_id'] ?>'>

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No balance information available.</p>
        <?php endif; ?>
    </div>
</div>

</div>
<style>
   
   .profile-wrapper {
    display: flex;
    flex-direction: column;
    gap:3;
   }
   .profile-card {
    border:none;
    width:100%;
    box-shadow:none
   }

    .card h2 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .card p {
        font-size: 1rem;
        margin: 5px 0;
    }

    .card ul {
        list-style-type: none;
        padding: 0;
    }

    .card ul li {
        font-size: 1rem;
        margin-bottom: 5px;
    }

</style>



<script>
 $(document).ready(function() {
   
    $('#deposit').click(function() {
        
        const amount = prompt("Enter deposit amount in cents (e.g., 500 = $5.00):");

        if (amount && !isNaN(amount) && parseInt(amount) > 0) {
            $.ajax({
                url: '/profile/deposit', 
                method: 'POST',
                data: {
                    amount: amount
                },
                success: function(response) {
                    alert('Deposit successful!');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Failed to process deposit.');
                }
            });
        } else {
            alert('Invalid amount!');
        }
    });

    
    $('#change-balance').click(function() {
        const currencyToSwitchTo = prompt("Enter the currency you want to switch to (e.g., USD, EUR):");

        if (currencyToSwitchTo) {
            $.ajax({
                url: '/profile/switch-active-balance',  
                method: 'POST',
                data: {
                    currency: currencyToSwitchTo
                },
                success: function(response) {
                    if (response.success) {
                        alert('Balance switched successfully!');
                        location.reload(); 
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Failed to switch balance.');
                }
            });
        } else {
            alert('No currency entered!');
        }
    });
});

</script>
<?php $this->endSection(); ?>

<style>
 .active {
    color:green;
 }
</style>
