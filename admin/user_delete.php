<?php

require '../config/config.php';
$stmt = $pdo->prepare("DELETE FROM users WHERE id=".$_GET['id']);
$stmt->execute();

//also delete relate comments
$stmt2 = $pdo->prepare("DELETE FROM comments WHERE author_id=".$_GET['id']);
$stmt2->execute();


header('Location: user_list.php');
?>
