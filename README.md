# php_CRUD_Profile

<!DOCTYPE html>
<html>
<head>
<!-- <title>Hsin-Yuan Wu (4e901913) - Profile database</title> -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Custom styles for this template -->
<link href="starter-template.css" rel="stylesheet">

</head>
<body>
<div class="container">
<h1>Welcome to Profile Database</h1>

<p>
<a href="login.php">Please log in</a>
</p>
<p>
Attempt to go to 
<a href="add.php">add data</a> without logging in - it should fail with an error message.
</p>


</div>
</body>


# Profile add

## Page to add profile:

<p align="center">
  <img src="Screen Shot 2020-07-22 at 3.11.05 PM.png" width="350" alt="accessibility text">
</p>

## file

<!DOCTYPE html>
<html>
<head>
<title>Hsin-Yuan Wu (4e901913) | Profile Add</title>
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
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

</head>
<body>
<div class="container">
<h1>Adding Profile for 
<?php
if ( isset($_SESSION['name']) ) {
    echo htmlentities($_SESSION['name']);
    echo "</p>\n";
}

// if input is not valid
if ( isset($_SESSION['error']) ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}

?>
</h1>


<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>

<p>Education:<input type="submit" id="addEdu" class="school" value= "+">
<div id="edu_fields"></div>

<p>Position:<input type="submit" id="addPos" value= "+">
<div id="position_fields"></div>
</p>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

<script>
countPos = 0;
countEdu = 0;

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
        // $('#edu_fields').append(source.replace(/@COUNT@/,countEdu));


        $('#edu_fields').append(
            '<div id="edu'+countEdu+'">\
            <p>Year:<input type="text" name="edu_year'+countEdu+'" value="" />\
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false; " ></p>    \
            <p>School:<input type="text" size=80 name="edu_school'+countEdu+'" class="school" value="" />  \
            </div>');
        $('.school').autocomplete({source: "school.php"});
    });
});



</script>
<script id = "edu-template" type="text">
    <div id="edu@COUNT@">
        <p>Year:<input type="text" name="edu_year@COUNT@" value="" />
        <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false; " ></p>
        <p>School:<input type="text" size=80 name="edu_school@COUNT@" class="school" value="" /></p></div>

</script>
</div>
