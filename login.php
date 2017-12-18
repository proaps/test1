<html>	
	<head>
		<title>Maciej Drążek</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="main.css">
	</head>
	<body>
		<?php include("config.php"); ?>
		<?php
			$nick = $_POST['nick'];
			$haslo = $_POST['haslo'];
			$haslo = addslashes($haslo);
			$nick = addslashes($nick);
			$nick = htmlspecialchars($nick);
			$ipaddress = $_SERVER["REMOTE_ADDR"];
			function ip_details($ip) {
				$json = file_get_contents ("http://ipinfo.io/{$ip}/geo");
				$details = json_decode ($json);
				return $details;
			}
			$details = ip_details($ipaddress);
			$lokacja = $details -> loc; echo '<BR />';
			$Ip = $details -> ip; echo '<BR />';


			$haslo = md5($haslo); //szyfrowanie hasla
			if (!$nick OR empty($nick)) {
				echo '<p class="alert">Wypełnij pole z nickem!</p>';
				exit;
			}
			if (!$haslo OR empty($haslo)) {
				echo '<p class="alert">Wypełnij pole z hasłem!</p>';
				exit;
			}
			$result = mysqli_fetch_array(mysqli_query($polaczenie, "SELECT * FROM `users` WHERE `Nick` = '$nick' AND `Haslo` = '$haslo'")); // sprawdzenie czy istnieje uzytkownik o takim nicku i hasle
			$user_id = mysqli_fetch_array(mysqli_query($polaczenie, "SELECT `Id` FROM `users` WHERE `Nick`='$nick'"));
			$user_id=$user_id[0];
			$blokada=mysqli_fetch_array(mysqli_query($polaczenie, "SELECT `Data_blokady` FROM `users` WHERE `Nick`='$nick' LIMIT 1"));
			$blokada=$blokada[0];
			$obecna_data = date("Y-m-d H:i:s");  
			$pozostalo = (strtotime($obecna_data) - strtotime($blokada));
			$pozostalo = 300-$pozostalo;
			if($pozostalo>0 ){
				echo "Twoje konto jest zablokowane <br /> ";
				echo "Do odblokowania konta pozostało: " .$pozostalo. " sekund";
			}else{
				if ($result[0] == 0) {
					echo 'Logowanie nieudane. ';
					setcookie("bledne_logowanie", "tak", time()+3600000);
					if ((mysqli_fetch_array(mysqli_query($polaczenie, "SELECT COUNT(*) FROM `users` WHERE `Nick`='$nick' LIMIT 1")))>=1){
						mysqli_query($polaczenie,"INSERT INTO `logi` (`Id`, `User_Id`, `Nick`, `Data_logowania`, `Status_logowania`, `Ip`) VALUES (NULL, '$user_id', '$nick', CURRENT_TIMESTAMP, 'Niepoprawne', '$Ip')");
						$ilosc_blednych_logowan = mysqli_fetch_array(mysqli_query($polaczenie,"SELECT `Ilosc_blednych_logowan` FROM `users` WHERE `Nick`='$nick'"));
						$ilosc_blednych_logowan=$ilosc_blednych_logowan[0];
						$ilosc_blednych_logowan+=1;
						echo "<nav>	<ul>";
						echo "<li>";
						echo "<a href='Logowanie.php'>Spróbuj ponownie</a>";
						echo "</li>";
						echo "	</ul></nav>";
						mysqli_query($polaczenie,"UPDATE `users` SET `Ilosc_blednych_logowan`='$ilosc_blednych_logowan' WHERE `Nick`='$nick'");
						if($ilosc_blednych_logowan>="3"){
							echo "Twoje konto zostało zablokowane na 5 minut";
							$czas=date("Y-m-d H:i:s");
							mysqli_query($polaczenie,"UPDATE `users` SET `Ilosc_blednych_logowan`='$ilosc_blednych_logowan',`Data_blokady`='$czas' WHERE `Nick`='$nick'");
						}
					}
				} else {	
					mysqli_query($polaczenie,"UPDATE `users` SET `Ilosc_blednych_logowan`='0' WHERE `Nick`='$nick'");
					$rezultat = mysqli_query($polaczenie, "SELECT * FROM `users` WHERE `Nick` = '$nick' AND `Haslo` = '$haslo'");
					mysqli_query($polaczenie,"INSERT INTO `logi` (`Id`, `User_Id`, `Nick`, `Data_logowania`, `Status_logowania`, `Ip`) VALUES (NULL, '$user_id', '$nick', CURRENT_TIMESTAMP, 'Poprawne', '$Ip')");
					setcookie("nick", $nick, time()+3600);
					setcookie("haslo", $haslo, time()+3600);
					echo "<meta http-equiv='REFRESH' content='0;URL=http://proaps.gbzl.pl/Lab7/indeks.php'>";
					
				}
			}
		?>
	</body>
</html>