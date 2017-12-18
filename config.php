<?php session_start();
	$dbhost="localhost"; $dbuser="maciej94d_proaps"; $dbpassword="proaps"; $dbname="maciej94d_lab7";
	$polaczenie = mysqli_connect ($dbhost, $dbuser, $dbpassword) or die(mysql_error()."Nie mozna polaczyc sie z baza danych. Prosze chwile odczekac i sprobowac ponownie.");
	mysqli_select_db ($polaczenie, $dbname)or die(mysql_error()."Nie mozna wybrac bazy danych.");
?>