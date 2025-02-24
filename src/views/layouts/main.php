<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->section('title'); ?></title>


    <link rel="stylesheet" href="/styles/main.css" />

<script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>

</head>



<body>
    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) $this->include('header') ?>

    <main class="main">
        <?= $this->section('content'); ?>
    </main>

    <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) $this->include('footer') ?>
</body>





</html>