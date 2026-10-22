<?php
session_start();
session_destroy();

header('Location: '.$uri.'../');
?>

<?php
include 'bridge.php';
if (isset($_POST['logout'])) {
	$_SESSION['id_username'] = '';
	unset($_SESSION['id_username']);
	session_unset();
	session_destroy();
	echo "<meta http-equiv=refresh content=1;URL='../dsv/'>";
}
?>