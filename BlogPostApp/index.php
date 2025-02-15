<?php
#*******************************************************************************************#
				
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
				
				require_once('./include/dateTime.inc.php');
				require_once('./include/config.inc.php');
				require_once('./include/form.inc.php');
				require_once('./include/db.inc.php');
				
				
				#********** PREPARE SESSION **********#
				
				session_name('wwwphpprojektblogde');
				
				
				#********** START/CONTINUE SESSION **********#
				
				session_start();
				
//if(DEBUG_V)	echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION<br>". print_r($_SESSION, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				
				
				#********** CHECK FOR VALID LOGIN **********#
				
				$loggedIn = isset($_SESSION['ID']) && $_SESSION['IPAddress'] === $_SERVER['REMOTE_ADDR'];
				
				if( !$loggedIn ) {
					// Fehlerfall (Seitenaufrufer ist nicht eingeloggt)
					
if(DEBUG)	echo "<p class='debug auth hint'><b>Line " . __LINE__ . "</b>: Seitenaufrufer ist nicht eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				
					#********** DENY PAGE ACCESS **********#
					// 1. Leere Session Datei löschen
				
					session_destroy();
		
				} else {
					// Erfolgsfall (Seitenaufrufer ist eingeloggt)
					
if(DEBUG)	echo "<p class='debug auth hint'><b>Line " . __LINE__ . "</b>: Seitenaufrufer ist eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					session_regenerate_id(true);
			
				} // CONTINUE SESSION END
				
				
#*******************************************************************************************#			
				
				#*********************************************#
				#********** DECLARATON OF VARIABLES **********#
				#*********************************************#
				
				$errorLogin 				= NULL;
				$category					= NULL;
				$noAvailablePostInfo 	= NULL;
				$transRollCommError		= NULL;
				
#*******************************************************************************************#

				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				#************* PREVIEW GET ARRAY ************#
				
//if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_GET<br>". print_r($_GET, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				
				#********************************************#
				
				// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde				
				if( isset($_GET['action']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 URL: Parameterwert auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Parameterwert wird ausgelesen und entschärft <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$action = sanitizeString($_GET['action']);
					
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					// Schritt 3 URL: Je nach erlaubtem Parameterwert verzweigen
					#********** LOGOUT **********#
					
					if( $action === 'logout' ) {
						
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Logout wird durchgeführt <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						// Schritt 4 URL: Parameter verarbeiten
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Parameter wird verarbeitet <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						// 1. Session Datei löschen	
						session_destroy();
						
						// 2. User auf öffentliche Seite umleiten
						header('LOCATION: ./');
						
						// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
						exit();						
			
					} // BRANCHING END
	
				} // PROCESS URL PARAMETERS END


#*******************************************************************************************#
				
				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#
				
				#********** PREVIEW POST ARRAY **********#
//if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST<br>". print_r($_POST, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				#****************************************#
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['hiddenLoginForm']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'hiddenLoginForm' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 FORM: Werte auslesen, entschärfen und Debug-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft<i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$userEmail 		= sanitizeString($_POST['f1']);
					$userPassword 	= sanitizeString($_POST['f2']);
					
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userEmail: $userEmail <i>(" . basename(__FILE__) . ")</i></p>\n";

if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userPassword: $userPassword <i>(" . basename(__FILE__) . ")</i></p>\n";

//var_dump($userEmail, $userPassword);
					
					#***************************************************#
					#********** CHECK IF EMAIL OR FIELD EMPTY **********#
					#***************************************************#
					
					if($userEmail === NULL || $userPassword === NULL) {
						//Fehlerfall
						
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: Email Feld oder Passwort Feld ist leer. <i>(" . basename(__FILE__) . ")</i></p>\n";	
					
						//Fehlermeldung für User
						$errorLogin = 'Alle Felde müssen ausgefüllt werden!';
						
					} else {
						//Erfolgsfall

if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: All Felde sind ausgefüllt. <i>(" . basename(__FILE__) . ")</i></p>\n";						
					
					
					
						// Schritt 3 FORM: Feldvalidierung
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
						$errorUserEmail 		= validateEmail($userEmail);
						$errorUserPassword 	= validateInputString($userPassword, minLength:4);
					
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorUserEmail: $errorUserEmail <i>(" . basename(__FILE__) . ")</i></p>\n";

if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorUserPassword: $errorUserPassword <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
						#***************************************************************#
						#********** FINAL FORM VALIDATION (FIELDS VALIDATION) **********#
						#***************************************************************#
					
						if( $errorUserEmail !== NULL OR $errorUserPassword !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";
						
							//Fehlermeldung für User
							$errorLogin = 'Diese Logindaten sind ungültig!';
						
						} else {						
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
							// Schritt 4 FORM: Verarbeitung der Formularwerte
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden verarbeitet. <i>(" . basename(__FILE__) . ")</i></p>\n";


							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#
							
							// Schritt 1 DB: DB-Verbindung herstellen
							$PDO = dbConnect('blogprojekt');
							
							
							// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
							$sql 				= 'SELECT userID, userEmail, userPassword FROM users WHERE userEmail = :userEmail';
							
							$placeholders 	= array('userEmail' => $userEmail);
							
							// Schritt 3 DB: Prepared Statements
							try {
								// Prepare: SQL-Statement vorbereiten
								$PDOStatement = $PDO->prepare($sql);
								
								// Execute: SQL-Statement ausführen
								$PDOStatement->execute($placeholders);
								
							} catch(PDOException $error) {
if(DEBUG) 					echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							}
						
							// Schritt 4 DB: Ergebnis der DB-Operation auswerten
							$userDataSet = $PDOStatement->fetch(PDO::FETCH_ASSOC);
							
							// rowCount Rechnen
							$rowCount = $PDOStatement->rowCount();
							
if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount<br><i>(" . basename(__FILE__) . ")</i>:</pre>\n";
						
if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userDataSet<br>". print_r($userDataSet, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
						
							
							#********** 1. VALIDATE USER EMAIL **********#
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: 1. Validiere userEmail <i>(" . basename(__FILE__) . ")</i></p>\n";


							if( $userDataSet === false ) {
								// Fehlerfall (ungültiger userEmail)
							
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Der userEmail '$userEmail' wurde nicht in der Datenbank gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";
							
								// Fehlermeldung für User
								$errorLogin = 'Diese Email ist nicht anerkannt!';
						
							} else {
							
								// Erfolgsfall (gültiger userEmail)
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Der userEmail '$userEmail' wurde in der Datenbank gefunden. <i>(" . basename(__FILE__) . ")</i></p>\n";
							
							
								#********** 2. VALIDATE PASSWORD **********#
if(DEBUG)					echo "<p class='debug'><b>Line " . __LINE__ . "</b>: 2. Validiere Passwort... <i>(" . basename(__FILE__) . ")</i></p>\n";
							
							
								if( password_verify($userPassword, $userDataSet['userPassword']) === false ) {
									// Fehlerfall (ungültiges Passwort)
								
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Login-Formular stimmt nicht mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";
								
									// Fehlermeldung für User
									$errorLogin = 'Diese Passwort ist nicht anerkannt!';
				
								} else {
									// Erfolgsfall (gültiges Passwort)
								
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Login-Formular stimmt mit dem Passwort aus der DB überein. <i>(" . basename(__FILE__) . ")</i></p>\n";

									#********** 3. PROCESS LOGIN **********#
if(DEBUG)							echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: 4. Login wird durchgeführt. <i>(" . basename(__FILE__) . ")</i></p>\n";


									#********** 4a. PREPARE SESSION **********#
										session_name('wwwphpprojektblogde');
										
										
									#*********** 4b. START SESSION ***********#
									if( session_start() === false ) {
											// Fehlerfall
										
if(DEBUG)							echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
										
										$errorLogin = 'Der Login kann nicht durchgeführt werden!<br> Bitte überprüfen Sie, ob in Ihrem Browser die Annahme von Cookies aktiviert ist.';
										
									} else {
										// Erfolgsfall
									
if(DEBUG)							echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
										
										
											#********** 4c. SAVE ACCOUNT DATA INTO SESSION FILE **********#
											$_SESSION['ID'] 			= $userDataSet['userID'];
											
											// Auslesen der IP-Adresse des Users und Speichern in die Session
											$_SESSION['IPAddress'] 	= $_SERVER['REMOTE_ADDR'];
										
if(DEBUG_V)							echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION<br>". print_r($_SESSION, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";

										
											#********** REMAIN ON HOME PAGE **********#
											header('LOCATION: ./');
		
									} //PROCESS LOGIN END

								} //VALIDATE PASSWORD END
	
							} //userDataSet and VALID EMAIL END
				
						} // FINAL FORM VALIDATION END
						
					} // EMAIL UND PASSWORD FIELDS EMPTINESS CHECK END
					
					dbClose($PDO, $PDOStatement);
					
				} // PROCESS FORM LOGIN END
#*******************************************************************************************#

				#***********************************************#
				#********** FETCH ALL CATEGORY LABELS **********#
				#***********************************************#
				
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blogprojekt');
				
				// Schritt 2 DB: SQL-Statement
				$sql = 'SELECT catLabel FROM categories';
				
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen
					$PDOStatement->execute();
					
				} catch (PDOException $error){
					
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";					
					
				}
				
				// Schritt 4 DB: Ergebnis der DB-Operation auswerten
				$catNamesDataSet = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
	
				// Zähle Kategorienamen
				$rowCount = $PDOStatement->rowCount();
				
				if($rowCount === 0) {
					//Fehlerfall (Keine Daten geholt)
					
if(DEBUG)		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: FEHLER beim holen Kategorie Label! <i>(" . basename(__FILE__) . ")</i></p>\n";					
						
				} else {
					//Erfolgsfall 

if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Kategorie Label erfolgreich geholt. <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				}
		
				#********** FETCH ALL CATEGORY LABELS END **********#
				
				
				
				#************************************************************#
				#********* FETCH ALL USER, CATEGORIES AND BLOG DATA *********#
				#************************************************************#
				
							#********* VARIABLE DECLARATION *********#
							$blogs = []; //Array für alle Daten
							#****************************************#
				
				if( isset($_POST['selectedCategory'])) {
					
					$selectedCategory = $_POST['selectedCategory'];
					
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Button \"$selectedCategory\" clicked. <i>(" . basename(__FILE__) . ")</i></p>\n";	
				
						// Schritt 2 DB: SQL-Statement
						// Hole Daten mittels ausgewhälte Kategorie
						$sql = '
						SELECT 
						catLabel, 
						blogHeadline, 
						userFirstName, 
						userLastName, 
						userCity, 
						blogImagePath, 
						blogImageAlignment, 
						blogContent, 
						blogDate
						FROM blogs
						JOIN users USING (userID)
						JOIN categories USING (catID)
						WHERE catLabel = :selectedCategory
						ORDER BY blogDate DESC';
					
						try{
							// Prepare: SQL-Statement vorbereiten 
							$PDOStatement = $PDO->prepare($sql);
							
							$placeholders = array('selectedCategory' => $selectedCategory);
							
							// Execute: SQL-Statement ausführen
							$PDOStatement->execute($placeholders);
							
						} catch (PDOException $error){
						
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";						
						
						}
					
						$blogs = $PDOStatement->fetchAll();
						
						if(empty($blogs)) {
						//Fehlerfall 
if(DEBUG)				echo "<p class='debug info'><b>Line " . __LINE__ . "</b>: Keine Daten für das catLabel '$selectedCategory'! <i>(" . basename(__FILE__) . ")</i></p>\n";	

							$noAvailablePostInfo = "Keine Daten für die Kategorie '$selectedCategory'!";
					
						} else {
						//erfolgsfall
if(DEBUG)				echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Blogpost erfolgreich geholt <i>(" . basename(__FILE__) . ")</i></p>\n";

//var_dump($blogs);
					
						}

					
				} else {
					
					// Hole alle Daten, wenn keine Kategorie ausgewählt ist
					$sql = '
						SELECT 
						catLabel, 
						blogHeadline, 
						userFirstName, 
						userLastName, 
						userCity, 
						blogImagePath, 
						blogImageAlignment, 
						blogContent, 
						blogDate
						FROM blogs
						JOIN users USING (userID)
						JOIN categories USING (catID)
						ORDER BY blogDate DESC';
					
						try{
							// Prepare: SQL-Statement vorbereiten 
							$PDOStatement = $PDO->prepare($sql);
							
							$placeholders = array();
							
							// Execute: SQL-Statement ausführen
							$PDOStatement->execute($placeholders);
							
						} catch (PDOException $error){
						
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";						
						
						}
					
						$blogs = $PDOStatement->fetchAll();
						
						if(empty($blogs)) {
						//Fehlerfall 
if(DEBUG)				echo "<p class='debug info'><b>Line " . __LINE__ . "</b>: Keine Daten in der Datenbank! <i>(" . basename(__FILE__) . ")</i></p>\n";	

							$noAvailablePostInfo = "Keine Daten in der Datenbank!";
					
						} else {
						//erfolgsfall
if(DEBUG)				echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Blogpost erfolgreich geholt <i>(" . basename(__FILE__) . ")</i></p>\n";

//var_dump($blogs);
					
						}
					
					
							#******* CLOSE DATABASE *********#
								dbClose($PDO, $PDOStatement);
				}

?>


<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">

		<style>
			.cat-array-div {
				display: flex; 
				flex-direction: column;
				width: 350px; 
				height: auto; 
				border: 1px solid #ccc; 
				border-radius: 5px; 
				margin-left: auto; 
				text-align: center; 
				padding: 25px 0px;
			}
			
			.cat-array-div input {
				border: none; 
				background: white; 
				cursor: pointer; 
				padding: 10px;
				font-size: 1.2em;
			}
			
			.cat-array-div input:hover {
				border: 1px brown solid;
				color: brown;
			}
	
			
			.main-container {
				 display: flex;
				 align-items: flex-start; 
				 justify-content: space-between;
				 width: 100%;
				 padding: 20px;
				 box-sizing: border-box;
			}

			
			.blog-container {
				 width: 50%; 
				 background-color: white;
				 padding: 20px;
				 border: 1px solid grey;
				 border-radius: 10px;
			}

			
			.blog-post {
				 position: relative;
				 /*border-bottom: 1px solid #ddd;*/
				 padding: 20px;
				 margin-bottom: 20px;
				 background-color: white;
			}

			
			.blog-category {
				 position: absolute;
				 top: 10px;
				 right: 15px;
				 font-weight: bold;
				 color: brown;
			}

			
			.blog-headline {
				 margin-top: 0;
				 color: brown;
			}

			
			.blog-author {
				 color: grey;
				 font-size: 14px;
			}

			
			.blog-content-container {
				 overflow: hidden;
			}

			
			.blog-image {
				 max-width: 300px;
				 height: auto;
				 margin: 10px;
				 border-radius: 5px;
			}

			
			.left-align {
				 float: left;
				 margin-right: 15px;
			}

			.right-align {
				 float: right;
				 margin-left: 15px;
			}

			
			.blog-text {
				 text-align: justify;
			}

			
			.clearfix::after {
				 content: "";
				 display: block;
				 clear: both;
			}

			
			.dashed-separator {
				 border: none;
				 border-top: 1px dashed #ccc;
				 border-bottom: 1px dashed #ccc;
				 width: 100%;
				 margin-top: 40px;
				 margin-bottom: 40px;
			}

			
			.cat-array-div {
				 width: 20%;
				 background-color: white;
				 padding: 20px;
				 border-radius: 5px;
				 text-align: center;
			}

			
			.no-post {
				 color: orange;
				 font-size: 16px;
				 font-weight: bold;
				 text-align: center;
				 margin-top: 20px;
			}

			
		</style>
		
		<title>PHP-Projekt Blog</title>
	</head>
	
	<body>
	<br>
	
		<?php if( $loggedIn === false ): ?>
		<!-- -------- LOGIN FORM START -------- -->
		<form action="" method="POST">
			<input type="hidden" name="hiddenLoginForm">
			<div style="text-align: right;">				
				<span class='error'><?= $errorLogin ?></span><br>
				<input class="short" type="text" name="f1" placeholder="Email">
				<input class="short" type="password" name="f2" placeholder="Password">
				<input class="short" type="submit" value="Login">
			</div>
		</form>
		<!-- -------- LOGIN FORM END -------- -->	
		
		<!-- -------- NAVIGATION LINKS -------- -->	
		<?php else: ?>
			<p style="margin-right: 85px; text-align: right;"><a style="text-align: right; text-decoration: none; color: brown;" href="?action=logout">Logout</a></p>
			<p style="text-align: right;"><a style="text-align: right; text-decoration: none; color: brown;" href="dashboard.php">zum Dashboard>></a></p>
		<?php endif ?>
		<!-- -------- NAVIGATION LINKS END -------- -->
		
		<hr style="border: none; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc; width: 100%; margin-top: 40px; margin-bottom: 40px;">
		
		<!---- DISPLAY AREA ---->
		<h1 style="margin-left: 20px; color: brown; font-family: times; font-size: 2em;">PHP - Projekt Blog</h1>
		<h3 style="margin-left: 20px; color: brown;">Alle Einträge anzeigen</h3>
		<span><?= $transRollCommError ?></span><br>
		
		
	<!---- BLOG POST DISPLAY AREA ---->
	<div class="main-container">
    <!-- BLOG POSTS AREA -->
    <div class="blog-container">
        <?php if (!empty($blogs)): ?>      
            <?php foreach ($blogs AS $blog): ?>
                <div class="blog-post">            
                    <div class="blog-category"><?= 'Kategorie: ' . $blog['catLabel'] ?></div>
                    <h3 class="blog-headline"><?= $blog['blogHeadline'] ?></h3>      
                    <p class="blog-author"><?= $blog['userFirstName'] . ' ' . $blog['userLastName'] . ' (' . $blog['userCity'] . ') ' . 'schrieb am ' . isoToEuDateTime($blog['blogDate'])['date'] . ' um ' . isoToEuDateTime($blog['blogDate'])['time'] .' Uhr:' ?></p>                         
                    <div class="blog-content-container clearfix">
                        <?php if (!empty($blog['blogImagePath'])): ?>
                            <?php $imageAlignmentClass = (trim($blog['blogImageAlignment']) === 'align left') ? 'left-align' : 'right-align'; ?>
                            <img src="<?= $blog['blogImagePath'] ?>" class="blog-image <?= $imageAlignmentClass ?>" alt="Blog Image">
                        <?php endif; ?>      
                        <div class="blog-text">
                            <p><?= nl2br($blog['blogContent']) ?></p>
                        </div>
                    </div>
                    <hr class="dashed-separator">
                </div>
            <?php endforeach; ?>
        <?php elseif (isset($_POST['selectedCategory'])): ?>
            <p class="no-post"><?= $noAvailablePostInfo ?></p>
        <?php endif; ?>
    </div> 

    <!-- CATEGORY SELECTION FORM -->
    <div class="cat-array-div">
        <span style="color: orange;"><?= $noAvailablePostInfo ?></span><br>
        <form action="" method="post">
            <?php foreach ($catNamesDataSet AS $category): ?>
                <input class="short" type="submit" name="selectedCategory" value="<?= $category['catLabel'] ?>"><br><br>
            <?php endforeach; ?>
        </form>
    </div>
</div>

				
			

	</body>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
</html>