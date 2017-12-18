<html>	
	<head>
		<title>Maciej Drążek</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="main.css">
	</head>
	<body>
		<?php include("config.php");

		$nick = $_COOKIE['nick'];
		$haslo = $_COOKIE['haslo'];
		if ((empty($nick)) AND (empty($haslo))) {
			echo '<br>Nie byłeś zalogowany albo zostałeś wylogowany<br><a href="Lab7.php">Strona Główna</a><br>';
			exit;
		}
		$user = mysqli_fetch_array(mysqli_query($polaczenie, "SELECT * FROM users WHERE `Nick`='$nick' AND `Haslo`='$haslo' LIMIT 1"));
		if (empty($user[Id]) OR !isset($user[Id])) {
			echo '<br>Nieprawidłowe logowanie.<br>';
			exit;
		}
		// tresc dla zalogowanego uzytkownika


		?>
		<nav>
			<ul>
				<li>
					<a href="http://proaps.gbzl.pl/Lab7/zarzadzaj.php">Zarządzaj plikami</a>
				</li>
				<li>
					<a href="http://proaps.gbzl.pl/Lab7/wyloguj.php">Wyloguj</a>
				</li>
				
			</ul>
		</nav>
		<h2><b>Twoja strona główna</b></h2>
		<h4><b>Zostałes zalogowany jako <h3><?php echo $nick ?></h3></b></h4>
		<h4>Ostatnie poprawne logowania</h4>
		<?php
			
			print "<TABLE CELLPADDING=5 BORDER=1>";
			print "<TR><TD>Data</TD><TD>Ip</TD></TR>\n";
			$result=mysqli_query($polaczenie,"SELECT * FROM `logi` WHERE `Nick`='$nick' AND `Status_logowania`='Poprawne' ORDER BY `logi`.`Data_logowania` DESC LIMIT 5");
			while ($wiersz = mysqli_fetch_array($result))
			{
				$Id = $wiersz [0];
				$nick = $wiersz [2];
				$data = $wiersz [3];
				$Ip = $wiersz [5];
				print "<TR><TD>$data</TD><TD>$Ip</TD></TR>\n";

			}
			print "</TABLE>";
		?>
		<h4>Ostatnie niepoprawne logowania</h4>
		<?php
			
			print "<TABLE CELLPADDING=5 BORDER=1>";
			print "<TR><TD>Data</TD><TD>Ip</TD></TR>\n";
			$result=mysqli_query($polaczenie,"SELECT * FROM `logi` WHERE `Nick`='$nick' AND `Status_logowania`='Niepoprawne' ORDER BY `logi`.`Data_logowania` DESC LIMIT 5");
			while ($wiersz = mysqli_fetch_array($result))
			{
				$Id = $wiersz [0];
				$nick = $wiersz [2];
				$data = $wiersz [3];
				$Ip = $wiersz [5];
				print "<TR><TD>$data</TD><TD>$Ip</TD></TR>\n";

			}
			print "</TABLE>";
			$data_b=mysqli_fetch_array(mysqli_query($polaczenie,"SELECT `Data_logowania` FROM `logi` WHERE `Nick`='$nick' AND `Status_logowania`='Niepoprawne' ORDER BY `logi`.`Data_logowania` DESC LIMIT 1"));
			$data_b=$data_b[0];
			if($_COOKIE['bledne_logowanie']=="tak"){
				$message = "Uwaga! " .$data_b. " Nastąpiło błędne logowanie" ;
				echo "<script type='text/javascript'>alert('$message');</script>";
				setcookie("bledne_logowanie", "nie", time()+3600000);
			}
		?>
		
	</body>
</html>