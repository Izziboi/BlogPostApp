<?php
#**************************************************************************************#

				
				#******************************************#
				#********** GLOBAL CONFIGURATION **********#
				#******************************************#
				
				/*
					Konstanten werden in PHP mittels der Funktion define (define('DEBUG', true))
					oder über das Schlüsselwort const (const DEBUG = true;) definiert. 
					Konstanten besitzen im Gegensatz zu Variablen kein $-Präfix.
					Üblicherweise werden Konstanten komplett GROSS geschrieben.
					
					Konstanten können in PHP mittels des Befehls 'const' oder mittels 'define()' deklariert
					und initialisiert werden. Im Prinzip verhalten sich beide Varianten gleich. Ein kleiner 
					Unterschied besteht jedoch im Gültigkeitsbereich bei der objektorientierten Programmierung:
					
					1. Verwendung von const:
					Werden mittels des const-Schlüsselwortes Konstanten innerhalb von Klassen definiert, ist
					die Verwendung von const auf Klassenebene beschränkt.
					Konstanten, die mit const definiert werden, sind implizit öffentlich (public) und können direkt
					über den Klassennamen aufgerufen werden, ohne eine Instanz der Klasse zu erstellen.
					Beispiel: const LOGIN_TYPE = email;			$loginType = User::LOGIN_TYPE;
					
					2. Verwendung von define():
					Die Funktion define() wird i.d.R. außerhalb von Klassen verwendet, oder wenn innerhalb einer 
					Klasse deklarierte Konstanten auch außerhalb der Klasse gültig sein sollen.
					define() kann Konstanten global in jedem Bereich des Codes definieren.
					Konstanten, die mit define() definiert werden, sind standardmäßig global und können überall 
					im Code verwendet werden.
					Beispiel: define('DEBUG', true);
				*/
				
				#********** DATABASE CONFIGURATION **********#
				define('DB_SYSTEM',							'mysql');
				define('DB_HOST',								'localhost');
				define('DB_NAME',								'market');
				define('DB_USER',								'root');
				define('DB_PWD',								'');
				
				
				#********** EXTERNAL STRING VALIDATION CONFIGURATION **********#
				define('INPUT_STRING_MANDATORY',			true);
				define('INPUT_STRING_MAX_LENGTH',		255);
				define('INPUT_STRING_MIN_LENGTH',		0);
				
				
				#********** IMAGE UPLOAD CONFIGURATION **********#
				define('IMAGE_MAX_WIDTH',					800);
				define('IMAGE_MAX_HEIGHT',					800);
				define('IMAGE_MIN_SIZE',					1024);
				define('IMAGE_MAX_SIZE',					128*1024);
				define('IMAGE_ALLOWED_MIME_TYPES',		array('image/jpg' => '.jpg', 'image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif') );
				
				
				#********** STANDARD PATHS CONFIGURATION **********#
				define('IMAGE_UPLOAD_PATH',				'./uploaded_images/');
				define('AVATAR_DUMMY_PATH',				'../css/images/avatar_dummy.png');

				
				#********** DEBUGGING CONFIGURATION **********#
				define('DEBUG',								true);		// DEBUGGING FOR MAIN DOCUMENTS
				define('DEBUG_V',								true);		// DEBUGGING FOR VALUES
				define('DEBUG_F',								true);		// DEBUGGING FOR FUNCTIONS
				define('DEBUG_DB',							true);		// DEBUGGING FOR DB-OPERATIONS


#**************************************************************************************#