<?php $this->startSection('title'); ?>
Login
<?php $this->endSection(); ?>

<?php $this->startSection('content'); ?>

<div class="container">
    <div style="border:none" class="card">
        <h1 class="form-header">Login</h1>

        <form action="/login" method="POST">
            <div class="input-block">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username">
                
                <?php if(isset($_SESSION['error']) && !empty($_SESSION['error'])) : ?>
                    <span class="error"><?= $_SESSION['error'] ?></span>
                <?php endif; ?>
                <span></span>
                
            </div>

            <div class="input-block">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
                 
                <?php if(isset($_SESSION['error']) && !empty($_SESSION['error'])) : ?>
    <span class="error"><?= $_SESSION['error'] ?></span>
    <?php endif; ?>

            </div>

            <div class="input-block">
                <button type="submit" class="login-button">Login</button>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection(); ?>

<style>
    
    .form-header {
        margin-bottom: 1rem;
    }
    
    .input-block {
        margin-bottom: 15px;
    }

    .input-block label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .input-block input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .login-button {
        width: 100%;
        padding: 10px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .login-button:hover {
        background: #0056b3;
    }
    .error {
        color: red;
        margin-top:0.5rem
    }
</style>
