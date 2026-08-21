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
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
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
