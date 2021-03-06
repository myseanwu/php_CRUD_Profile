<?php
require_once "bootstrap.php";
require_once "pdo.php";
session_start();

if (! isset($_SESSION['name'])){
    die('Not logged in');}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST['delete'] ) && isset($_POST['profile_id'])) {
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':zip'  => $_POST['profile_id']));
    $_SESSION['success'] = "Record DELETED";
    header("Location: index.php");
    return;
}
// Guardian: Make sure that auto_id is present
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
  }
  
  $stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ( $row === false ) {
      $_SESSION['error'] = 'Bad value for profile_id';
      header( 'Location: index.php' ) ;
      return;
  }


?>

<!DOCTYPE html>
<html>
<head>
<title>Hsin-Yuan Wu (4e901913) | Rock, Paper, Scissors Game</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Confirm: Deleting</h1>

<p>First Name:
     <?= htmlentities($row['first_name']) ?>
</p>
<p>Last Name:
     <?= htmlentities($row['last_name']) ?>
</p>


<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
</form>
