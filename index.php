
<?php
require_once "pdo.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Hsin-Yuan Wu (4e901913) - Profile database</title>
<?php require_once "bootstrap.php"; 
require_once "head.php";?>
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">
</head>
<body>
<div class="container">
<h1>Welcome to Profile Database</h1>
<?php

if (! isset ($_SESSION['success'])){

echo('
<p>
<a href="login.php">Please log in</a>
</p>
<p>
Attempt to go to 
<a href="add.php">add data</a> without logging in - it should fail with an error message.
</p>');
}



if (isset ($_SESSION['success'])){
    echo ('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
    
    echo('<table border="1">'."\n");
    $stmt = $pdo->query("SELECT first_name,last_name,email,headline,summary,profile_id FROM Profile");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ( $rows === false){
        echo("<p>No rows found</p>");
    };
    if ($rows !== false){

    echo("<tr><th>Name</th>");
    echo("<th>Headline</th>");
    echo("<th>Action</th></tr>");

    foreach ($rows as $row)
    {
        echo "<tr><td>";
        echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
        echo(htmlentities($row['first_name'].' '.$row['last_name']));
        echo("</td><td>");
        echo(htmlentities($row['headline']));
        echo("</td><td>");

        echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
        echo("</td></tr>\n");

};



    echo('</table>');
};
    echo('<a href="add.php">Add New Entry </a>| <a href="logout.php">Logout</a>');
};
?>



</div>
</body>
