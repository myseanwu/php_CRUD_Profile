<?php
// sleep(2);
header('Content-Type: application/json; charset=utf-8');

// include_once("../config.php");
if (! isset($_COOKIE[session_name()])){
    die("Must be log in");
}

session_start();
if (! isset($_SESSION['user_id'])){
    die("ACCESS DENIED");
}

require_once "pdo.php";
$term = $_GET['term'];
error_log("Looking up typeahead term=".$term);
$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $term."%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));


?>