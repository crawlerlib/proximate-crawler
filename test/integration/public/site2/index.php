<?php
$page = isset($_GET['page']) ? $_GET['page'] + 1 : 1;
$showLink = $page < 4;

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Query string demo</title>
        <meta charset="UTF-8">
    </head>
    <body>
        <?php if ($showLink): ?>
            <p>Linky to <a href="/site2/index.php?page=<?php echo $page ?>">another page</a>.</p>
        <?php else: ?>
            <p>No linky here!</p>
        <?php endif ?>
    </body>
</html>
