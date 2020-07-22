<?php
require_once "bootstrap.php";
require_once "pdo.php";
session_start();


if (! isset($_SESSION['name'])){
    die('Not logged in');}

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to index if press cancel
    header("Location: index.php");
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
        && isset($_POST['headline']) && isset($_POST['summary'])) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 
    || strlen($_POST['email']) < 1 || strlen($_POST['headline']) <1 || strlen($_POST['summary']) <1  ){
        $_SESSION['error'] = "All values are required";
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }
    if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Bad data';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }
    // update Profile
    $sql = "UPDATE Profile SET first_name = :first_name, last_name = :last_name, email=:email,
            headline = :headline, summary = :summary
            WHERE profile_id = :profile_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']));

    //validate position if present
    for ($i =1; $i<=9;$i++){
    if (isset($_POST['year'.$i]) && isset($_POST['desc'.$i])){
        if (strlen($_POST['year'.$i]) <1 || strlen($_POST['desc'.$i]) <1){
            $_SESSION['error'] = "All fields are required";
            header("Location: edit.php?profile_id=".$_POST['profile_id']);
            return;
        }
        if (!is_numeric($_POST['year'.$i])){
            $_SESSION['error'] = 'Year must be numeric';
            header("Location: edit.php?profile_id=".$_POST['profile_id']);
            return;
        }
     }
    }
    // update Position
    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));


    // Insert the position entries
    $rank = 1;
    for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');

    $stmt->execute(array(
    ':pid' => $_REQUEST['profile_id'],
    ':rank' => $rank,
    ':year' => $year,
    ':desc' => $desc)
    );
    $rank++;
    }


    // validate education if present
    for ($i =1; $i<=9;$i++){
        if (isset($_POST['edu_year'.$i]) && isset($_POST['edu_school'.$i])){
            if (strlen($_POST['edu_year'.$i]) <1 || strlen($_POST['edu_school'.$i]) <1){
                $_SESSION['error'] = "All fields are required";
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return;
            }
            if (!is_numeric($_POST['edu_year'.$i])){
                $_SESSION['error'] = 'Year must be numeric';
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return;
            }
        }
    }

    // Clear out the old education entries
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

    // Insert the school entries
    $rank = 1;
    for ($i = 1; $i <= 9; $i ++){
        if (! isset($_POST['edu_year'.$i])) continue;
        if (! isset($_POST['edu_school'.$i])) continue;
        $year = $_POST['edu_year'.$i];
        $school = $_POST['edu_school'.$i];

        // school to school_id
            $stmt = $pdo->prepare('SELECT * FROM Institution WHERE name=:ss ');
            $stmt->execute(array( ':ss'=> $school));
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $school_id = $row['institution_id'];
                
            } else {
                $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:school)');
                $stmt->execute(array(
                    ':school'=> $school));
                $school_id = $pdo->lastInsertId();
            }

        // Insert Education
        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id,rank, year) 
                                VALUES (:pid, :school_id,:rank, :year)');
        $stmt->execute(array(
            ':pid'=> $_REQUEST['profile_id'],
            ':school_id'=>$school_id,
            ':rank' => $rank,
            ':year'=>$year
        ));
        $rank++;
        }




    $_SESSION['success'] = 'Record updated';
    header("Location: index.php") ;
    return;
    }

// Guardian: Make sure that profile_id is present
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


$f = htmlentities($row['first_name']);
$l = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);
$profile_id = $row['profile_id'];


//for position
function loadPos($pdo,$profile_id){
    $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id=:xxx ORDER BY rank');
    $stmt -> execute(array(':xxx'=>$profile_id));
    $positions = array();
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        $positions[] = $row;
    };
    return $positions;
}

$positions = loadPos($pdo,$_REQUEST['profile_id']);


//education
$stmt = $pdo->prepare('SELECT * FROM Education as E JOIN Institution as I ON E.institution_id=I.institution_id WHERE profile_id=:xxx ORDER BY rank');
$stmt -> execute(array(':xxx'=>$_REQUEST['profile_id']));
$education = array();
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
    $education[]=$row;
};



?>






<!DOCTYPE html>
<html>
<head>
<title>Hsin-Yuan Wu (4e901913) - Automobiles database</title>
<?php require_once "bootstrap.php"; 
require_once "head.php";
?>
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
<div class="container">
<h1>Editing Profile for
<?php
if ( isset($_SESSION['name']) ) {
    echo htmlentities($_SESSION['name']);
    echo "</p>\n";
}
?>
</h1>

<?php
// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>

<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $f ?>" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $l ?>" size="60"/></p>
<p>Email:
<input type="text" name="email" value="<?= $e ?>" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" value="<?= $h ?>" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?= $s ?></textarea>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
</p>

<p>Education:<input type="submit" id="addEdu" value= "+">
<div id="edu_fields"></div></p>

<?php
if ($education){
    $number = sizeof($education);
    foreach ($education as $edu){
        $n = $edu['rank'];
        $edu_pp = 'edu'.$n;
        $school = 'edu_school'.$n;
        echo ('<div id="'.$edu_pp.'">');
        echo('<p>Year: <input type="text" name="edu_year'.$n.'" value="'.$edu['year'].'">');
        echo('<input type="button" value="-"');
        echo(' onclick="$(\'#'.$edu_pp.'\')'.'.remove();return false;"></p>');
        echo('<p>School:<input type="text" name="'.$school.'"'.' size="80" class="school" ');
        echo('value="'.htmlentities($edu['name']).'"></div>');
    };
} else {$number=0;}
?>

<p>Position:<input type="submit" id="addPos" value= "+">
<div id="position_fields"></div></p>

<?php
if ($positions){
    $pos = sizeof($positions);
    foreach ($positions as $row)
    {
        $n = $row['rank'];
        $pp = 'position'.$n;
        $desc_name = 'desc'.$n;
        echo ('<div id="'.$pp.'">');
        echo('<p>Year: <input type="text" name="year'.$n.'" value="'.$row['year'].'">');
        echo('<input type="button" value="-"');
        echo(' onclick="$(\'#'.$pp.'\')'.'.remove();return false;"></p>');
        echo('<textarea name="'.$desc_name.'"'.' rows="8" cols="80">');
        echo($row['description'].'</textarea></div>');
        };

} else {$pos=0;}

?>


</p>
<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

<script>
countPos = <?=$pos ?>;
countEdu = <?=$number ?>;


$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if (countPos >=9){
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'">\
            <p>Year:<input type="text" name="year'+countPos+'" value="" />\
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false; " ></p>    \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea> \
            </div>');
    });
});

$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addEdu').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if (countEdu >=9){
            alert("Maximum of nine Education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);
        // var source = $("#edu-template").html();
        // $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'">\
            <p>Year:<input type="text" name="edu_year'+countEdu+'" value="" />\
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false; " ></p>    \
            <p>School:<input type="text" size=80 name="edu_school'+countEdu+'" class="school" value="" ></div>');

        $('.school').autocomplete({source: "school.php"});
    });
});

</script>
<!-- <script id = "edu-template" type="text">
    <div id="edu@COUNT@">
        <p>Year:<input type="text" name="edu_year@COUNT@" value="" />
        <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false; " ><br>
        <p>School:<input type="text" size=80 name="edu_school@COUNT@" class="school" value="" /></p></div>

</script> -->
</html>

