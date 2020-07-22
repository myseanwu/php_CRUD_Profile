<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['done'] ) ) {
    // Redirect the browser to index if press cancel
    header("Location: index.php");
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
        && isset($_POST['headline']) && isset($_POST['summary']) ) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 
        || strlen($_POST['email']) < 1 || strlen($_POST['headline']) <1 || strlen($_POST['summary']) <1  ){
        $_SESSION['error'] = "All values are required";
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    };

    }

// Guardian: Make sure that auto_id is present
if ( ! isset($_REQUEST['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}


?>

<!DOCTYPE html>
<html>
<head>
<title>Hsin-Yuan Wu (4e901913) - Automobiles database</title>
<?php require_once "bootstrap.php";
require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Profile information</h1>



<?php
    $stmt = $pdo->prepare("SELECT first_name,last_name,email,headline,summary FROM Profile WHERE profile_id=:nam");
    $stmt -> execute(array(':nam'=>$_REQUEST['profile_id']));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($rows === false){
        echo("No rows found");
    };

    foreach($rows as $row) {
    echo("<p>First Name: " );
    echo(htmlentities($row['first_name']));
    echo("</p><p>");
    echo("Last Name: ");
    echo(htmlentities($row['last_name']));
    echo("</p><p>");
    echo("Email: ");
    echo(htmlentities($row['email']));
    echo("</p><p>");
    echo("Headline: ");
    echo(htmlentities($row['headline']));
    echo("</p><p>");
    echo("Summary: ");
    echo(htmlentities($row['summary']));
    echo("</p>");

    $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id=:xxx ORDER BY rank');
    $stmt -> execute(array(':xxx'=>$_REQUEST['profile_id']));
    $positions = array();
    $positions=$stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($positions !== false){
        echo("Position:</p><ul>");
        foreach ($positions as $row){

        echo("<li>".htmlentities($row['year']).":".htmlentities($row['description'])."</li>");
    };
        echo("</ul>");
    };

    $stmt = $pdo->prepare("SELECT * FROM Education as E JOIN Institution as I ON E.institution_id=I.institution_id WHERE profile_id=:nam");
    $stmt -> execute(array(':nam'=>$_REQUEST['profile_id']));
    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($educations !== false){
        echo("Education:</p><ul>");
        foreach ($educations as $row){

        echo("<li>".htmlentities($row['year']).":".htmlentities($row['name'])."</li>");
    };
        echo("</ul>");
    }
};


?>

<a href="index.php">Done</a>
</form>
</html>

