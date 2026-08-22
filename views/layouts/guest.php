<?php
use App\Core\CSRF;
use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'ASR FORM') ?></title>
    <meta name="description" content="ASR FORM - Platform Form Builder & Document Generator">
    <?= CSRF::meta() ?>
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo-icon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <?= $content ?? '' ?>

    <?php if (Session::hasFlash('error')): ?>
        <div id="flash-toast" data-type="error" data-message="<?= e(Session::getFlash('error')) ?>"></div>
    <?php endif; ?>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
