<?php
$week = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
?>

<form>
<?php
foreach($week as $day){
echo "<label>$day</label>";
echo '<input type = "checkbox" name = "check">'."<br>";}
?>


</form>
