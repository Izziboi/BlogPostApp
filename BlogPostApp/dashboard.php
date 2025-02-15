<?php
#*******************************************************************************************#

				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
				
				require_once('./include/config.inc.php');
				require_once('./include/form.inc.php');
				require_once('./include/db.inc.php');
							
				
				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#
				
				#********** PREPARE SESSION **********#
				session_name('wwwphpprojektblogde');
				
				#********** START/CONTINUE SESSION **********#
				
				session_start();

//if(DEBUG_V)	echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION<br>". print_r($_SESSION, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				
				#*******************************************#
				#********** CHECK FOR VALID LOGIN **********#
				#*******************************************#
				
				if( isset( $_SESSION['ID'] ) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
					// Fehlerfall (Seitenaufrufer ist nicht eingeloggt)
					
if(DEBUG)	echo "<p class='debug auth hint'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";
				
				
					#********** DENY PAGE ACCESS **********#
					// 1. Leere Session Datei löschen
				
					session_destroy();
					
					header('LOCATION: ./');

					exit();
					
				} else {
					// Erfolgsfall (Seitenaufrufer ist eingeloggt)
					
if(DEBUG)		echo "<p class='debug auth hint'><b>Line " . __LINE__ . "</b>: Login erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					session_regenerate_id(true);
					
					// Auslesen der ID des Seitenaufrufers aus der Session
					$userID = $_SESSION['ID'];
					
if(DEBUG_V)		echo "<p class='debug auth value'><b>Line " . __LINE__ . "</b>: \$userID: $userID <i>(" . basename(__FILE__) . ")</i></p>\n";
					
				} // SECURE PAGE ACCESS END
				
				#*********************************************#
				#********** DECLARATON OF VARIABLES **********#
				#*********************************************#
				
				$catID 					= NULL;
				$catLabel				= NULL;
				
				$blogHeadline			= NULL;
				$blogImagePath			= NULL;
				$blogImageAlignment	= NULL;
				$blogContent			= NULL;
				$uploadedImagePath 	= NULL;				
				
				$userEmail				= NULL;

				$error 					= NULL;
				$errorCatLabel 		= NULL;
				$success 				= NULL;
				$errorPost				= NULL;
				$transRollCommError  = NULL;
				
				$showModal 				= NULL;
				
				$imgAlignArray			= ['align left', 'align right'];
				
#*******************************************************************************************#

				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#

				#********** PREVIEW GET ARRAY **********#

//if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_GET<br>". print_r($_GET, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				
				#****************************************#
				
				// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde				
				if( isset($_GET['action']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 URL: Parameterwert auslesen, entschärfen
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
						
						// 3. Fallback
						
						exit();						
						
						
					} // BRANCHING END
	
				} // PROCESS URL PARAMETERS END	

#*******************************************************************************************#

				#***********************************************************#
				#********** PROCESS FORM ADD CATEGORY LABEL TO DB **********#
				#***********************************************************#
				
				#********** PREVIEW POST ARRAY **********#
//if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST<br>". print_r($_POST, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";
				#****************************************#
				
				// Schritt 1 FORM: Prüfe ob, Formular abgeschickt wurde
				if( isset($_POST['hiddenCatForm']) === true ) {
					
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'hiddenCatForm' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 FORM: Werte auslesen, entschärfen und Debug-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft<i>(" . basename(__FILE__) . ")</i></p>\n";
				
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Eine neue Kategorie wird eingefügt... <i>(" . basename(__FILE__) . ")</i></p>\n";

				$catLabel = sanitizeString($_POST['f8']);

if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catLabel: $catLabel <i>(" . basename(__FILE__) . ")</i></p>\n";

//var_dump($catLabel);


					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert <i>(" . basename(__FILE__) . ")</i></p>\n";

				$errorCatLabel 	= validateInputString($catLabel);
				
				
						#***************************************************************#
						#********** FINAL FORM VALIDATION (FIELDS VALIDATION) **********#
						#***************************************************************#
				
					if( $errorCatLabel !== NULL) {
						
						// Fehlerfall

if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";
						
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorCatLabel: $errorCatLabel <i>(" . basename(__FILE__) . ")</i></p>\n";

												
						//Fehlermeldung für User
						$errorCatLabel = 'Dieses Feld muss nicht leer sein!';
						
					} else {
						
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						// Schritt 4 FORM: Verarbeitung der Formularwerte
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden verarbeitet. <i>(" . basename(__FILE__) . ")</i></p>\n";


						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect('blogprojekt');
						
						
						#******* PRÜFE OB, EINGEGEBENE KATEGORIE SCHON EXISTIERT *******#
						
						$sql 				= 'SELECT catID FROM categories WHERE catLabel = :catLabel';
						
						$placeholders 	= array('catLabel' => $catLabel);
						
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen
							$PDOStatement->execute($placeholders);
							
						} catch(PDOException $error) {
							
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";	
									
						}
						
						//Hole Kategoriedatenarray
						$categoryDataSet = $PDOStatement->fetch(PDO::FETCH_ASSOC);
						
//var_dump($categoryDataSet);
						
						if( $categoryDataSet !== false ) {
							// Fehlerfall 
							
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Der Kategoriename '$catLabel' existiert bereits in der Datenbank! <i>(" . basename(__FILE__) . ")</i></p>\n";
							
							// Fehlermeldung für User
							
							$errorCatLabel = 'Es existiert bereits eine Kategorie mit diesem Namen!';
							
						} else {
							// Erfolgsfall 
							
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Der Kategoriename '$catLabel' existiert noch nicht in der Datenbank. <i>(" . basename(__FILE__) . ")</i></p>\n";
						
							
							// Schritt 2 DB: Kategoriename Einfügen 
							$sql 				= 'INSERT INTO categories (catLabel) VALUES (:catLabel)';
							
							$placeholders 	= array('catLabel' => $catLabel);
							
							// Schritt 3 DB: Prepared Statements
							try {
								// Prepare: SQL-Statement vorbereiten
								$PDOStatement = $PDO->prepare($sql);
								
								// Execute: SQL-Statement ausführen
								$PDOStatement->execute($placeholders);
								
							} catch(PDOException $error) {
							
if(DEBUG) 					echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";	
									
							}
							
							// rowCount Rechnen
							$rowCount = $PDOStatement->rowCount();
							
							// lastInsertID Prüfen
							$newCatID = $PDO->lastInsertID();

if(DEBUG_V)					echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount<br><i>(" . basename(__FILE__) . ")</i>:</p>\n";
							
							
							// Prüfe ob, Kategoriename erfolgreich eingefügt werden							
							if($rowCount !== 1) {
							//Fehlerfall
							
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Neue Kategorie Einfügung nicht erfolgreich! <i>(" . basename(__FILE__) . ")</i></p>\n";

								$errorCatLabel = 'Neue Kategorie Einfügung nicht erfolgreich!';

							} else {
							
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Userdatensatz erfolgreich unter ID $newCatID in der DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";
				
								#******* Modal display *******#
								$closeModal = NULL;
								$showModal = true;
								
								#***** Close Modal display *****#
								if (isset($_POST['closeModal'])) {
									 $showModal = false; 
								}
								#***** Modal display ends *****#
								
								$success = 'Die neue Kategorie mit dem Namen <b>' . $catLabel . '</b> wurde erfolgreich gespeichert.';					
							
								
							} // KATEGORIENAME EINFÜGEN END 
		
						} // EINGEGEBENE KATEGORIE SCHON EXISTIERT PRÜFUNG END
				
					} // FINAL FORM VALIDATION END

							#***** CLOSE DATABASE ******#
							dbClose($PDO, $PDOStatement);

				} // PROCESS FORM ADD CATEGORY LABEL TO DB END
				
				
#**********************************************************************************************#							
				
#********** FETCH USER FIRST AND LAST NAMES AND ALL CATEGORIES **********#

				#***********************************#
				#********** DB OPERATIONS **********#
				#***********************************#
				
				
				#********** FETCH USER NAMES **********#
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blogprojekt');
				
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Lade Benutzername... <i>(" . basename(__FILE__) . ")</i></p>\n";	
			
				$sql 				= 'SELECT userFirstName, userLastName FROM users WHERE userID = :userID';
				
				$placeholders 	= array('userID' => $userID);
				
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen
					$PDOStatement->execute($placeholders);
					
				} catch(PDOException $error) {
							
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";	
									
				}
						
				//Hole Benutzernamen
				$userNameDataSet = $PDOStatement->fetch(PDO::FETCH_ASSOC);
				
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Benutzername $userNameDataSet[userFirstName] wurde abgerufen. <i>(" . basename(__FILE__) . ")</i>:</p>\n";
						
//var_dump($userNameDataSet);

				#***** CLOSE DATABASE ******#
				dbClose($PDO, $PDOStatement);
				
#**********************************************************************************************#				
				
				#***********************************#
				#********** DB OPERATIONS **********#
				#***********************************#
				
				
				#********** FETCH CATEGORIES **********#
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect('blogprojekt');

if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Lade Kategorienamen... <i>(" . basename(__FILE__) . ")</i></p>\n";				
				
				$sql 				= 'SELECT catLabel FROM categories';
				
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen
					$PDOStatement->execute();
					
				} catch(PDOException $error) {
							
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";	
									
				}
						
				//Hole Benutzernamen
				$catDataSet = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
				
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Kategorienamen wurde abgerufen. <i>(" . basename(__FILE__) . ")</i>:</p>\n";
						
/*var_dump($catDataSet);
echo "<br><br>";
foreach ($catDataSet as $category) {
    var_dump($category['catLabel']);
}*/

				#***** CLOSE DATABASE ******#
				dbClose($PDO, $PDOStatement);

#*******************************************************************************************#
	
			#*********************************************************#
			#********************* UPLOAD POSTS **********************#
			#*********************************************************#
				
				// Schritt 1 FORM: Prüfe ob, Formular abgeschickt wurde
				if( isset($_POST['hiddenPubForm']) === true ) {
					
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'hiddenPubForm' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 FORM: Werte auslesen, entschärfen und Debug-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft<i>(" . basename(__FILE__) . ")</i></p>\n";
				
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Eine neue Post wird eingefügt... <i>(" . basename(__FILE__) . ")</i></p>\n";

					
					$catLabel 				= sanitizeString($_POST['f3']);
					$blogHeadline			= sanitizeString($_POST['f4']);
					$blogImageAlignment	= sanitizeString($_POST['f6']);
					$blogContent			= sanitizeString($_POST['f7']);

if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catLabel: $catLabel <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogHeadline: $blogHeadline <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogImageAlignment: $blogImageAlignment <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$blogContent: $blogContent <i>(" . basename(__FILE__) . ")</i></p>\n";

					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					$errorCatOption			= validateInputString($catLabel);
					$errorBlogHeadline 		= validateInputString($blogHeadline);
					$errorBlogImageAlign 	= validateInputString($blogImageAlignment);
					$errorBlogContent 		= validateInputString($blogContent, maxLength:5000);
				
				
						#***************************************************************#
						#********** FINAL FORM VALIDATION (FIELDS VALIDATION) **********#
						#***************************************************************#
				
					if( $errorBlogHeadline !== NULL || $errorBlogImageAlign !== NULL || $errorBlogContent !== NULL) {
						
						// Fehlerfall

if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";
										
						//Fehlermeldung für User
						$error = 'Dieses Feld muss nicht leer sein!';
						
					} else {
						
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						// Schritt 4 FORM: Verarbeitung der Formularwerte
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden verarbeitet. <i>(" . basename(__FILE__) . ")</i></p>\n";


						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect('blogprojekt');	
						
						//Zuerst das ID des ausgewählten catLabels aufrufen
						
						$sql = 'SELECT catID, catLabel FROM categories WHERE catLabel = :catLabel';
						
						$placeholders = array('catLabel' => $catLabel);
						
						
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen
							$PDOStatement->execute($placeholders);
				
						} catch(PDOException $error) {
							
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";	
									
						}
						
							//Hole Kategorie ID
							$catIDDataSet 	= $PDOStatement->fetch(PDO::FETCH_ASSOC);
							$catID 			= $catIDDataSet['catID'];
				
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Kategorie ID wurde abgerufen. <i>(" . basename(__FILE__) . ")</i>:</p>\n";

if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$catID: $catID <i>(" . basename(__FILE__) . ")</i>:</p>\n";

if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$catLabel: $catIDDataSet[catLabel] <i>(" . basename(__FILE__) . ")</i>:</p>\n";

//var_dump($catIDDataSet);



											#**************************************
											#*********** IMAGE UPLOAD ************#
											#**************************************
						
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_FILES<br>". print_r($_FILES, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";	

//var_dump($_FILES['f5']['tmp_name']);			
						
						if (empty($_FILES['f5']['tmp_name'])) {
							//INFO: (keine Datei beigefügt und Hochladen ist nicht aktiv)

if(DEBUG)				echo "<p class='debug info'><b>Line " . __LINE__ . "</b>: Keine Datei angehängt! <i>(" . basename(__FILE__) . ")</i>:</p>\n";							
							
							
						} else {
							//Erfolgsfall (Datei wurde angehängt und Hochladen ist aktiv)
							
if(DEBUG)				echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Bilddatei wurde angehängt! <i>(" . basename(__FILE__) . ")</i></p>\n";				
															
							$validateUploadedImageArray = validateImageUpload( $_FILES['f5']['tmp_name'] );
								
if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$validateUploadedImageArray<br>". print_r($validateUploadedImageArray, true) . "<i>(" . basename(__FILE__) . ")</i>:</pre>\n";							
							
							#********** VALIDATE IMAGE UPLOAD **********#
							if( $validateUploadedImageArray['imageError'] !== NULL ) {
									// Fehlerfall
									
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Bildupload: $validateUploadedImageArray[imageError] <i>(" . basename(__FILE__) . ")</i></p>\n";


								$imageError = $validateUploadedImageArray['imageError'];
									
							} else {
							// Erfolgsfall
									
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Datei erfolgreich nach '<i>$validateUploadedImageArray[imagePath]</i>' auf den Server geladen. <i>(" . basename(__FILE__) . ")</i></p>\n";

								$uploadedImagePath = $validateUploadedImageArray['imagePath'];
							
							} //VALIDATE IMAGE UPLOAD END
						
						} //IMAGE UPLOAD END

						
						#***************************************#
						#********** TRANSACTION START **********#
						#***************************************#
						
						
						if( $PDO->beginTransaction() === false ) {
							// Fehlerfall
							
if(DEBUG)				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Transaction! <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
							// Fehlermeldung für User
							$transRollCommError = 'Es ist ein Fehler aufgetreten! Bitte kontaktieren Sie unseren Support.';
							
						} else {
							// Erfolgsfall
					
							//Nun einfüge in die blog Tabele neuen Daten  
						
							$sql = 'INSERT INTO blogs (blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID) VALUES (:blogHeadline, :blogImagePath, :blogImageAlignment, :blogContent, :catID, :userID)';
							
							$placeholders 			= array(
							'blogHeadline' 		=> $blogHeadline, 
							'blogImagePath' 		=> $uploadedImagePath, 
							'blogImageAlignment' => $blogImageAlignment, 
							'blogContent' 			=> $blogContent, 
							'catID' 					=> $catID, 
							'userID' 				=> $userID);
						
							try {
							
								// Prepare: SQL-Statement vorbereiten
								$PDOStatement = $PDO->prepare($sql);
								
								// Execute: SQL-Statement ausführen
								$PDOStatement->execute($placeholders);
							
							} catch (PDOException $error){
							
if(DEBUG) 					echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: ERROR: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";							
							
						}
						
							//Zeile zählen
							$rowCountPost = $PDOStatement->rowCount();
							
							//Neues ID
							$newBlogID = $PDO->lastInsertID();
							
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$rowCountPost: $rowCountPost <i>(" . basename(__FILE__) . ")</i>:</p>\n";

if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$newBlogID: $newBlogID <i>(" . basename(__FILE__) . ")</i>:</p>\n";
							
							$closeModal		= NULL;
							$showModal 		= true;
							$errorPost 		= NULL;
						
							if($rowCountPost === 0) {
								//Fehlerfall 

if(DEBUG)					echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Fehler: Beitrag nicht gespeichert! <i>(" . basename(__FILE__) . ")</i>:</p>\n";


								#********** ROLLBACK DB CHANGES **********#
								if( $PDO->rollback() === false ) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Durchführen des Rollbacks! <i>(" . basename(__FILE__) . ")</i></p>\n";				
											
									// Fehlermeldung für User
									$transRollCommError = 'Es ist ein Fehler aufgetreten! Bitte kontaktieren Sie unseren Support.';
										
								} else {
									// Erfolgsfall
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Rollback erfolgreich durchgeführt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								}
								#******** ROLLBACK DB CHANGES END ********#
								
								$errorPost = 'Fehler: Beitrag nicht gespeichert!';
							
							} else {
								//Erfolgsfall
								
if(DEBUG)					echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Der Beitrag wurde erfolgreich gespeichert. <i>(" . basename(__FILE__) . ")</i>:</p>\n";

								
					#********** COMMIT DB CHANGES **********#
								if( $PDO->commit() === false ) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Durchführen des Commits! <i>(" . basename(__FILE__) . ")</i></p>\n";				
												
									$transRollCommError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
																				
								} else {
									// Erfolgsfall
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Commit erfolgreich durchgeführt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
				
									$success = 'Der Beitrag wurde erfolgreich gespeichert.';
									
									#***** Close Modal display *****#
									if (isset($_POST['closeModal'])) {
										$showModal = false; 
									} // Close modal display
												
								} //COMMIT END
					
							} //ROW COUNT END
							
						} // TRANSACTION END
						
							#********* CLOSE DATABASE *********#
							dbClose($PDO, $PDOStatement);
					
					} //FINAL FORM VALIDATION END
					
				} //UPLOAD POSTS END

?>


<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">	

		<style>
			* {
			  box-sizing: border-box;
			}
			
			.modal {
            display: <?php echo $showModal ? 'block' : 'none'; ?>;
            background: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
		  
        .modal-dialog {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #cccccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
		  
		  .post-area {
			  width: 100%; 
			  height: 400px; 
			  margin: 10px 0px; 
			  border: 1px #ccc solid; 
			  border-radius: 5px; 
			  padding: 10px;"
		  }
		  
		  .post-area .post-img{
			  display: none;
		  }
		</style>
		
		<title>PHP-Projekt Blog</title>
	</head>
	
	<body>	
		<!-- NAVIGATION LINKS START -->
		<p style="margin-right: 76px; text-align: right;"><a style="text-decoration: none; color: brown;" href="?action=logout">Logout</a></p>
		<p style="text-align: right;"><a style="text-decoration: none; color: brown;" href="./"><< zum Frontend</a></p>
		<hr style="border: none; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc; width: 100%; margin-top: 40px; margin-bottom: 40px;">
		<!-- NAVIGATION LINKS END -->
		
		<!-- HEADER STARTS -->
		<h1 style="margin-left: 8px; color: brown; font-family: times; font-size: 2em;">PHP - Projekt Blog - Dashboard</h1>
		<!-- HEADER ENDS -->
		
		<p style="margin-left: 7px; color: brown;">Aktiver Benutzer: <?= $userNameDataSet['userFirstName'] . " " . $userNameDataSet['userLastName'] ?> </p>
		
		
		
		<!-- FORM STARTS -->
		<div style="display: flex; flex-direction: row; gap: 20px;">
			<div style="width: 700px; padding: 10px;">
			
			<!-- POST FORM BEGINS -->
				<h3 style="color: grey;">Neuen Blog-Eintrag verfassen</h3>
				<span><?= $transRollCommError ?></span><br>
				<form action="" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="hiddenPubForm">
					
					<span><?= $errorPost ?></span><br>
					<select style="width: 100%; padding: 0px 5px; margin: 15px 0px; height: 30px; line-height: 30px;" name="f3">
						<?php foreach($catDataSet AS $category): ?>
						<option 
						<?= ($catLabel === $category['catLabel']) ? 'selected' : '' ?>>
						<?= $category['catLabel'] ?></option>
						<?php endforeach; ?>
					</select>
					<span class='error'><?= $error ?></span><br>
					<input style="width: 100%; margin: 10px 0px;" type="text" placeholder="Überschrift" name="f4">
					<p>Bild hochladen:</p>
					<div style="display: flex; flex-direction: row; justify-content: space-between;">
						<input name="f5" type="file">
						<select name="f6" style="width: 200px; padding: 0px 5px; height: 30px; line-height: 30px;">
							<?php foreach($imgAlignArray AS $align): ?>
							<option <?= ($blogImagePath === $align) ? 'selected' : '' ?>>
							<?= $align ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<span class='error'><?= $error ?></span><br>
					<textarea class="post-area"  name="f7"></textarea>
					<input style="padding: 15px;" type="submit" value="Veröffentlichen">
				</form>
				<!-- POST FORM ENDS -->
				
			</div>
			
			<!-- CATEGORY FORM BEGINS -->
			<div style="width: 500px; margin-left: auto; padding: 10px;">
				<h3 style="color: grey;">Neue Kategorie anlagen</h3>
				<form action="" method="POST">
					<input type="hidden" name="hiddenCatForm">
					
					<span class="error"><?= $errorCatLabel ?></span><br>
					<input style="width: 100%;" type="text" placeholder="Name der Kategorie" name="f8">
					<input style="padding: 15px;" type="Submit" value="Neue Kategorie anlegen">
				</form>
			</div>
		</div>
		<!-- FORMS END -->
		
		<!--- MODAL MESSAGE STARTS --->
		<?php if ($showModal): ?>
		 <div class="modal" style="display: flex; justify-content: center; align-items: center; padding: 20px;">
			  <div class="modal-dialog" style="border: 2px solid orange;">
					<p style="color: green;"><?= $success ?></p>
					<form method="POST" style="display: flex; justify-content: center; align-items: center;">  
						<button type="submit" name="closeModal" class="btn btn-primary">Schließen</button>  
					</form>
			  </div>
		 </div>		 
		<?php endif; ?>
		<!--- MODAL MESSAGE ENDS --->
		
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