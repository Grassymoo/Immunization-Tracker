<?php
if (isset($_POST["s"])) {

    if (!isset($_COOKIE["value1"])) {
        setcookie("value1", $_POST["value"], time() + 600);

    } elseif (!isset($_COOKIE["value2"])) {
        setcookie("value2", $_POST["value"], time() + 600);
        
    } elseif (!isset($_COOKIE["value3"])) {
        setcookie("value3", $_POST["value"], time() + 600);
    }
 
}

/*for some reason, you need to press the submit button twice in order for the result to appear
i looked it up and found out that the result won’t show up in the list of cookies displayed 
on the page until the user reloads the page or submits the form again*/

foreach ($_COOKIE as $name => $val) {
    echo $name . ": " . $val . "<br>";
}
?>

<form method="post" action="">
    <input type="text" name="value">
    <input type="submit" name="s">
</form>
