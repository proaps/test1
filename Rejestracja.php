<html>	
	<head>
		<title>Maciej Drążek</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="main.css">
	</head>
	<body>
		<?php include("config.php");
						
			echo '<h2>Rejestracja</h2>';
			 
			echo '<form method="POST">
			Nick: <br />
			<input type="text" name="nick"><br />
			Hasło: <br />
			<input type="password" name="haslo"><br />
			Powtórz hasło: <br />
			<input type="password" name="haslo2"><br />
			E-mail: <br />
			<input type="text" name="email"><br />
			Powtórz e-mail: <br />
			<input type="text" name="email2"><br />
			<input type="submit" name="ok" value="Rejestruj">
			</form>';    
			 
			// jeśli zostanie naciśnięty przycisk "Rejestruj"
			if(isset($_POST['ok']))
			{
				$nick = substr(addslashes(htmlspecialchars($_POST['nick'])),0,32);
				$haslo = substr(addslashes($_POST['haslo']),0,32);
				$haslo2 = substr($_POST['haslo2'],0,32);
				$email = substr($_POST['email'],0,32);
				$email2 = substr($_POST['email2'],0,32);
				$nick = trim($nick);
				
				//kilka sprawdzen co do nicku i maila
				$spr1 = mysqli_fetch_array(mysqli_query($polaczenie, "SELECT COUNT(*) FROM `users` WHERE `Nick`='$nick' LIMIT 1")); //czy user o takim nicku istnieje
				$spr2 = mysqli_fetch_array(mysqli_query($polaczenie, "SELECT COUNT(*) FROM `users` WHERE `Email`='$email' LIMIT 1")); // czy user o takim emailu istnieje
				$spr3 = strlen($nick);
				$spr4 = strlen($haslo);
				$pos = strpos($email, "@");
				$pos2 = strpos($email, ".");
				$komunikaty = '';
				// sprawdzamy czy wszystkie dane zostały podane
				if (!$nick || !$email || !$haslo || !$haslo2 || !$email2 ){
				$komunikaty .= "Musisz wypełnić wszystkie pola!<br>"; }
				if ($spr3 < 4) {
				$komunikaty .= "Login musi mieć przynajmniej 4 znaki<br>"; }
				if ($spr4 < 4) {
				$komunikaty .= "Hasło musi mieć przynajmniej 4 znaki<br>"; }
				if ($spr1[0] >= 1) {
				$komunikaty .= "Ten login jest zajęty!<br>"; }
				if ($spr2[0] >= 1) {
				$komunikaty .= "Ten e-mail jest już używany!<br>"; }
				if ($email != $email2) {
				$komunikaty .= "E-maile się nie zgadzają ...<br>";}
				if ($haslo != $haslo2) {
				$komunikaty .= "Hasła się nie zgadzają ...<br>";}
				if ($pos == false OR $pos2 == false) {
				$komunikaty .= "Nieprawidłowy adres e-mail<br>"; }
				
				//jesli cos jest nie tak to blokuje rejestracje i wyswietla bledy
				if ($komunikaty) {
				echo '
				<b>Rejestracja nie powiodła się, popraw następujące błędy:</b><br>
				'.$komunikaty.'<br>';
				} else {
				//jesli wszystko jest ok dodaje uzytkownika i wyswietla informacje
				$nick = str_replace ( ' ','', $nick );
				$haslo = md5($haslo); //szyfrowanie hasla

				mysqli_query($polaczenie, "INSERT INTO `users` (`Id`, `Nick`, `Haslo`, `Email`, `Data_rejestracji`) VALUES (NULL, '$nick', '$haslo', '$email', CURRENT_TIMESTAMP)") or die("Nie mogłem Cie zarejestrować!");
				mkdir("pliki_uzytkownikow/$nick", 0777);
				echo '<br><span style="color: green; font-weight: bold;">Zostałeś zarejestrowany '.$nick.'. Teraz możesz się zalogować</span><br>';
				echo '<br><a href="Logowanie.php">Logowanie</a>';
				}
			}
			 
		?>
	</body>
</html>