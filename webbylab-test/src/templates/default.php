<?php

$alert_message = "No body template selected!";

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Webbylab-test</title>
</head>

<body>
    <?php if (!empty($this->body_template)) : ?>
        <?php include $this->body_template; ?>
    <?php else : ?>
        <span><?= $alert_message ?></span>
    <?php endif; ?>
</body>

</html>
