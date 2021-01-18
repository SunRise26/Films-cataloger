<?php

$alert_message = "No body template selected!";

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Webbylab-test</title>

    <link type="text/css" rel="stylesheet" href="/assets/css/app.css" />
    <script src="assets/js/app.js" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <?php if (!empty($this->body_template)) : ?>
        <?php include $this->body_template; ?>
    <?php else : ?>
        <span><?= $alert_message ?></span>
    <?php endif; ?>
</body>

</html>
