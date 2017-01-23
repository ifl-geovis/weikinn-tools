<?php
error_reporting(E_ALL);
require_once( 'weikinn_1571.php' );

$GLOBAL_PROJECT_ID 	= 1753;
$GLOBAL_SOURCE_ID	= 10664;
$GLOBAL_CREATED_BY 	= 1;
$GLOBAL_PUBLISH 	= 't';
$GLOBAL_TIMESTAMP	= date('Y-m-d H:i:s').'.0+1';
$GLOBAL_CALENDAR_ID = 1;
$GLOBAL_POSITION_ID = 314210; //unknown position

$ordner			=	'./temp/weikinn';
$ordner_ausgabe	= 	$ordner.'/sql/';

$quoteID = 140002;
$eventID = 353515;
$momentID = 628409;
$periodID = 314205;
$codingsetID = 314219;
$codingitemID = 480539;

function MakeTime($time, $yearBegin, $monthBegin, $dayBegin, $yearEnd, $monthEnd, $dayEnd) {
	$yearBegin = intval($yearBegin);
	$yearEnd = intval($yearEnd);
	$monthBegin = intval($monthBegin);
	$monthEnd = intval($monthEnd);
	$dayBegin = intval($dayBegin);
	$dayEnd = intval($dayEnd);

	if ($yearBegin == 0) {
		$yearBegin = 9999;
	}

	if ($yearEnd == 0) {
		$yearEnd = $yearBegin;
		if ($monthBegin == 0 && $monthEnd == 0) {
			$monthEnd = 12;			
		}
	}

	if ($monthBegin == 0) {
		if ($monthEnd == 0) {
			$monthBegin = 1;
		} else {
			switch ($monthEnd) {
				case 13:
					$monthBegin = 12;
					$monthEnd = 2;										
					break;
				case 14:
					$monthBegin = 3;
					$monthEnd = 5;				
					break;
				case 15:						
					$monthBegin = 6;										
					$monthEnd = 8;															
					break;
				case 16:
					$monthBegin = 9;
					$monthEnd = 11;				
					break;
				case 17:
					$monthBegin = 1;
					$monthEnd = 4;				
					break;
				case 18:
					$monthBegin = 5;
					$monthEnd = 8;				
					break;
				case 19:
					$monthBegin = 9;
					$monthEnd = 12;
					break;
				case 20:
					$monthBegin = 1;
					$monthEnd = 6;
					break;
				case 21:
					$monthBegin = 7;
					$monthEnd = 12;
					break;		
			}
		}		
	} else {
		if ($monthEnd == 0) {
			switch ($monthBegin) {
				case 13:
					$monthBegin = 12;
					$monthEnd = 2;
					$dayEnd = 28;
					$yearEnd = $yearEnd+1;						
					break;
				case 14:
					$monthBegin = 3;
					$monthEnd = 5;				
					break;
				case 15:						
					$monthBegin = 6;										
					$monthEnd = 8;															
					break;
				case 16:
					$monthBegin = 9;
					$monthEnd = 11;				
					break;
				case 17:
					$monthBegin = 1;
					$monthEnd = 4;				
					break;
				case 18:
					$monthBegin = 5;
					$monthEnd = 8;				
					break;
				case 19:
					$monthBegin = 9;
					$monthEnd = 12;
					break;
				case 20:
					$monthBegin = 1;
					$monthEnd = 6;
					break;
				case 21:
					$monthBegin = 7;
					$monthEnd = 12;
					break;			
				default:
					$monthEnd = $monthBegin;
					break;
			}
		} else {
			switch ($monthBegin) {
				case 13:
					$monthBegin = 12;			
					break;
				case 14:
					$monthBegin = 3;
					break;
				case 15:
					$monthBegin = 6;
					break;
				case 16:
					$monthBegin = 9;
					break;
				case 17:
					$monthBegin = 1;
					break;
				case 18:
					$monthBegin = 5;
					break;
				case 19:
					$monthBegin = 9;
					break;
				case 20:
					$monthBegin = 1;
					break;
				case 21:
					$monthBegin = 7;
					break;			
				default:
					$monthBegin = $monthBegin;
					break;
			}
			switch ($monthEnd) {
				case 13:
					$monthEnd = 2;
					$yearEnd = $yearEnd+1;				
					break;
				case 14:
					$monthEnd = 5;				
					break;
				case 15:				
					$monthEnd = 8;				
					break;
				case 16:
					$monthEnd = 11;				
					break;
				case 17:
					$monthEnd = 4;				
					break;
				case 18:
					$monthEnd = 8;				
					break;
				case 19:
					$monthEnd = 12;
					break;
				case 20:
					$monthEnd = 6;
					break;
				case 21:
					$monthEnd = 12;
					break;			
				default:
					$monthEnd = $monthEnd;
					break;
			}
		}
		
	}

	if ($dayBegin == 0) {
		$dayBegin = 1;
		if ($dayEnd == 0) {
			switch ($monthBegin) {
				case 1:
					$dayEnd = 31;
					break;				
				case 2:
					$dayEnd = 28;
					break;
				case 3:
					$dayEnd = 31;
					break;
				case 5:
					$dayEnd = 31;
					break;
				case 7:
					$dayEnd = 31;
					break;
				case 8:
					$dayEnd = 31;
					break;				
				case 10:
					$dayEnd = 31;
					break;
				case 12:
					$dayEnd = 31;
					break;
				default:
					$dayEnd = 30;
					break;
			}
			
		}
	} 
	if ($dayEnd == 0) {
		switch ($dayBegin) {
			case 28:
				if ($monthBegin == 2) {
					$monthEnd = $monthBegin+1;
					$dayEnd = 1;
				} else {
					$dayEnd = $dayBegin+1;
				}				
				break;
			case 31:
				if ($monthBegin == 12) {
					$yearEnd = $yearBegin+1;
					$monthEnd = 1;
					$dayEnd = 1;	
				} else {
					$monthEnd = $monthBegin+1;
					$dayEnd = 1;	
				}	
				break;	
			case 32:
				$dayBegin = 1;
				$dayEnd = 5;
				break;
			case 33:
				$dayBegin = 6;
				$dayEnd = 10;
				break;
			case 34:
				$dayBegin = 11;
				$dayEnd = 15;
				break;
			case 35:
				$dayBegin = 16;
				$dayEnd = 20;
				break;
			case 36:
				$dayBegin = 21;
				$dayEnd = 25;
				break;
			case 37:
				$dayBegin = 26;
				$dayEnd = 30;
				break;
			case 38:
				$dayBegin = 1;
				$dayEnd = 10;
				break;
			case 39:
				$dayBegin = 11;
				$dayEnd = 20;
				break;
			case 40:
				$dayBegin = 21;
				$dayEnd = 30;
				break;
			case 41:
				$dayBegin = 1;
				$dayEnd = 10;
				break;
			case 42:
				$dayBegin = 11;
				$dayEnd = 20;
				break;
			case 43:
				$dayBegin = 21;
				$dayEnd = 30;
				break;
			case 44:
				$dayBegin = 1;
				$dayEnd = 15;
				break;
			case 45:
				$dayBegin = 16;
				$dayEnd = 30;
				break;							
			default:
				if ($dayBegin != 31) {
					$dayEnd = $dayBegin+1;
					break;	
				}					
		}
	} else {
		switch ($dayBegin) {
			case 32:
				$dayBegin = 1;
				break;
			case 33:
				$dayBegin = 6;
				break;
			case 34:
				$dayBegin = 11;
				break;
			case 35:
				$dayBegin = 16;
				break;
			case 36:
				$dayBegin = 21;
				break;
			case 37:
				$dayBegin = 26;
				break;
			case 38:
				$dayBegin = 1;
				break;
			case 39:
				$dayBegin = 11;
				break;
			case 40:
				$dayBegin = 21;
				break;
			case 41:
				$dayBegin = 1;
				break;
			case 42:
				$dayBegin = 11;
				break;
			case 43:
				$dayBegin = 21;
				break;
			case 44:
				$dayBegin = 1;
				break;
			case 45:
				$dayBegin = 16;
				break;							
			default:
				$dayBegin = $dayBegin;
				break;	
		}					
		switch ($dayEnd) {
			case 32:
				$dayEnd = 1;
				break;
			case 33:
				$dayEnd = 6;
				break;
			case 34:
				$dayEnd = 11;
				break;
			case 35:
				$dayEnd = 16;
				break;
			case 36:
				$dayEnd = 21;
				break;
			case 37:
				$dayEnd = 26;
				break;
			case 38:
				$dayEnd = 1;
				break;
			case 39:
				$dayEnd = 11;
				break;
			case 40:
				$dayEnd = 21;
				break;
			case 41:
				$dayEnd = 1;
				break;
			case 42:
				$dayEnd = 11;
				break;
			case 43:
				$dayEnd = 21;
				break;
			case 44:
				$dayEnd = 1;
				break;
			case 45:
				$dayEnd = 16;
				break;							
			default:
				$dayEnd = $dayEnd;
				break;					
		}
	}

	if ($time == 2) {
		$date = new DateTime($yearEnd.'-'.$monthEnd.'-'.$dayEnd);
		$result = $date->format('Y-m-d H:i:s');		
		return $result;
	} else {
		$date = new DateTime($yearBegin.'-'.$monthBegin.'-'.$dayBegin);
		$result = $date->format('Y-m-d H:i:s');			
		return $result;
	}
}

$weikinnSetupFile = "-- Weikinn Setup File\n";
$weikinnCleanupFile = "-- Weikinn Cleanup File\n\n-- Removes all data from THIS import session\n\n";

$quotesFile = 'COPY grouping.quote (id, source_id, project_id, created_by, modified_by, text, page, file, text_vector, public, comment) FROM stdin;'."\n";
$quoteTemplate = "%id	$GLOBAL_SOURCE_ID	$GLOBAL_PROJECT_ID	$GLOBAL_CREATED_BY	\\N	%text	%page	%file	\\N	true	%comment\n";
$quoteVars = array('%id','%text','%page','%file','%comment');


$eventsFile = 'COPY grouping.event (id, quote_id, project_id, created_by, modified_by, public, comment, period_id, position_id, codingset_id, source_id, license_id, doi, valid) FROM stdin;'."\n";
$eventTemplate = "%id	%quote_id	$GLOBAL_PROJECT_ID	$GLOBAL_CREATED_BY	\\N	true	%comment	%period_id	$GLOBAL_POSITION_ID	%codingset_id	$GLOBAL_SOURCE_ID	\\N	\\N	\\N\n";
$eventVars = array('%id','%quote_id','%comment','%period_id','%codingset_id');

$eventCodes = array(
	'R'=>array(217,'Temperatur'),
	'S'=>array(94,'Niederschlag'),
	'T'=>array(366,'Luftdruck'),
	'U'=>array(383,'Luftfeuchtigkeit'),
	'V'=>array(23,'Wind'),
	'W'=>array(307,'Gewitter'),
	'X'=>array(113,'Hagel'),
	'Y'=>array(565,'Sonstiges')
);

$momentsFile = 'COPY timing.moment (id, type, "time", calendar_id, hour_id, day_id, month_id, year, created_by, modified_by) FROM stdin;'."\n";
$momentTemplate = "%id	%type	%timestamp	$GLOBAL_CALENDAR_ID	\\N	%day_id	%month_id	%year	$GLOBAL_CREATED_BY	\\N\n";
$momentVars = array('%id', '%type', '%timestamp', '%day_id', '%month_id', '%year');


$periodsFile = 'COPY timing.period (id, description, begin_moment_id, end_moment_id,  created_by, modified_by) FROM stdin;'."\n";
$periodTemplate = "%id	\\N	%begin_moment_id	%end_moment_id	$GLOBAL_CREATED_BY	\\N\n";
$periodVars = array('%id', '%begin_moment_id', '%end_moment_id');


$codingsetsFile = 'COPY coding.codingset (id, created_by) FROM stdin;'."\n";
$codingsetTemplate = "%id	$GLOBAL_CREATED_BY\n";
$codingsetVars = array('%id');


$codingitemsFile = 'COPY coding.codingitems (id, codingset_id, node_id, created_by) FROM stdin;'."\n";
$codingitemTemplate = "%id	%codingset_id	%node_id	$GLOBAL_CREATED_BY\n";
$codingitemVars = array('%id',	'%codingset_id',	'%node_id');
 


echo "Erzeugung der Import-Dateien ============================\n\n";

echo "Erzeuge Weikinn-Quelle... \n\n";

$weikinnSetupFile .= "\n\n-- Weikinn Source\n\n";
$weikinnSetupFile .= "\nINSERT INTO source( id, project_id, created_by, title, publish, \"timestamp\") "
		."VALUES ( $GLOBAL_SOURCE_ID, $GLOBAL_PROJECT_ID, $GLOBAL_CREATED_BY, 'Weikinn OG', TRUE, '$GLOBAL_TIMESTAMP' );";
$weikinnSetupFile .= "\n-- INSERT INTO taggroup( id, name, description, is_private) "
		."VALUES ( 10, 'Weikinn_Import', 'temporary item from import $GLOBAL_TIMESTAMP', TRUE );";
$weikinnSetupFile .= "\n-- INSERT INTO tag( id, taggroup_id, name, description, is_private) "
		."VALUES ( 1001, 10, 'ORT1', 'Weikinn Ortsangabe\\ntemporary item from import $GLOBAL_TIMESTAMP', TRUE );";
$weikinnSetupFile .= "\n-- INSERT INTO tag( id, taggroup_id, name, description, is_private) "
		."VALUES ( 1002, 10, 'ORT2', 'Kodierter Name durch Digitalisierungsprojekt\\ntemporary item from import $GLOBAL_TIMESTAMP', TRUE );";

$ORT2_TAG_ID = 1002;
		
$weikinnCleanupFile .= "\nDELETE FROM grouping.event WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM grouping.quote WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";


echo "Erzeuge Event-Codes...\n\n";

$weikinnSetupFile .= "\n\n-- Event Codes\n--uebersprungen\n\n";


echo "Starte Erzeugung der Import-Dateien...\n\n";

$weikinn = new Weikinn();

$bilder = new Bilder();

$protokoll = array();

$jahreszahlen = $weikinn->jahreszahlen() ;


foreach($jahreszahlen as $jahreszahl) {
	if (!$weikinn->istGeokodiert($jahreszahl)) {
		echo $jahreszahl.": Quote $quoteID Event $eventID > ";
		
		$jahr = $weikinn->jahr( $jahreszahl );
		
		while ($jahr->hatWeitereZitate()) {		
			$zitat = $jahr->weiteresZitat();
			
			if (!empty($zitat->fallFehler)) {
				$protokoll[] = $page.": ".$zitat->fall.": ".$zitat->fallFehler;
			}
			// Speichere Zitat
			
			//"Jahr" + Jahr aus Tabellenname + "Zettel" + Spalte "Zett. / Formel" + Spalte "KZ" + "(Laufende Nr." + Spalte "lNr" + ")"
			// Zu Lang: Feld is varchar(16)
			$CD = array();
			foreach($zitat->C as $key=>$val) {
				@$CD[$key] = $val.$zitat->D[$key];
			}
			$page_comment = "Jahr $jahreszahl Zettel "
					//.implode(',',$zitat->C)
					//." "
					//.implode(',',$zitat->D)
					.implode(', ',$CD)
					." (Laufende Nr. "
					.implode(', ',$zitat->A)
					.")";
					
			$min = min($zitat->C);
			$max = max($zitat->C);
			if ($min<$max) {
				$C = $min.'-'.$max;
			} else {
				$C = $min;
			}
			$page = "$jahreszahl/$C";
			
			$text = "";  // schon geändert: ein Leerzeichen am Anfang
			// Ort 1 einarbeiten
			if (!empty($zitat->M)) {
				$text .= $zitat->M. " /// ";
			} else {
				//$text .= "[Keine Ortsangabe.] /// ";
				$text .= " /// ";
			}
			
			// Datum
			
			$text .= $zitat->datum." /// ";
			
			// Text
			//$text .= str_replace( array("'",'"'), array("''",'""'), $zitat->P );
			$text .= $zitat->P;
			
			// Übersetzung
			if (!empty($zitat->Q)) {
				$text .= " /// (Übersetzung: "
						.$zitat->Q
						.")";
			}
			
			$escape_search  = array("'",'"',"\r\n","\r");
			$escape_replace = array('’','”','\n','\n');//array("\\'",'\"')
			
			$namen_comment = "M::{$zitat->ort_weikinn};;N::{$zitat->ort};;O::{$zitat->O}\\n";
			$namen_comment = str_replace( $escape_search, $escape_replace, $namen_comment );
			
			$text = str_replace( $escape_search, $escape_replace, $text );
			
			// Quelle
			$quelle = str_replace( $escape_search, $escape_replace, $zitat->AJ );
			
			$quelle_comment = str_replace( $escape_search, $escape_replace,
								implode(";;SOURCE::",$zitat->AJ_comment) );
			
			$filenames = array();
			foreach ($zitat->C as $c) {
				$filenames[] = $bilder->findImage($jahreszahl, $c);
			}	
			$filenames = implode(";;",array_unique($filenames));
			
			$replace = 	array(
				$quoteID,													//'%id',
				$text,		//addslashes($zitat->P),										//'%text',
				$page, //$jahreszahl.'::'.join(",",$zitat->C).'::'.join(",",$zitat->D),		//'%page',
				'\N',//$jahr->dateiname_kurz.'::'.join(",",$zitat->rows),			//'%file', Datei und Dateizeile(n)
				
				// öffentlicher Kommentar
				"Herkunft: Weikinn, $page_comment"
				."\\n\\nQuellen:\\n".implode("\\n",$quelle)
				
				// privater Kommentar
				."\\n\\n=== NO EDIT BELOW ===\\n"
				."WEIKINN;;PAGE::$page_comment\\n"
				."SOURCES;;SOURCE::".$quelle_comment."\\n"
				."PLACE;;$namen_comment"
				."HAND;;{$zitat->AK}\\n"
				."\\n"
				."IMAGEFILES;;".$filenames."\\n"
				,													//'%comment'
				);
			$zitatZeile = str_replace( $quoteVars, $replace, $quoteTemplate );
			$quotesFile .= $zitatZeile;
			
			// Speichere Event
			
			$comment = '';
				
			if (!empty($zitat->datum_error)) {
				$comment .= "Importfehler Datum: ".$zitat->datum_error;
				$protokoll[] = $page."Importfehler Datum: ".$zitat->datum_error;
			}
			if (empty($comment)) {
				$comment = '\N';
			}
			
			//if (1===2) {
			foreach( $zitat->events as $event_col) {

			// tambora coding
				//node_id
				$eventCode = $eventCodes[$event_col][0];
			
				//coding.codingset
				$codingsetVars = array('%id');
				$replace = array(
					$codingsetID,	//codingset_id
				);
				$codingsetZeile = str_replace( $codingsetVars, $replace, $codingsetTemplate );
				$codingsetsFile .= $codingsetZeile;

				//coding.codingitems
				$codingitemVars = array('%id','%codingset_id','%node_id');
				$replace = array(
					$codingitemID,	//codingitems_id
					$codingsetID,	//codingset_id
					$eventCode,		//node_id
				);
				$codingitemsZeile = str_replace( $codingitemVars, $replace, $codingitemTemplate );
				$codingitemsFile .= $codingitemsZeile;

				
			// tambora event
								
				$eventVars = array('%id','%quote_id','%comment','%period_id','%codingset_id');
				$replace = array(
					$eventID,		// %id
					$quoteID,		// %quote_id
					$comment,		// %comment
					$periodID, 		// %period_id
					$codingsetID,	// %codingset_id	
				);
				
				$eventZeile = str_replace( $eventVars, $replace, $eventTemplate );
				$eventsFile .= $eventZeile;

			// tambora timing 
				foreach ($zitat->events as $event) {
				
				// timing.period
				$periodVars = array('%id', '%begin_moment_id', '%end_moment_id');
				$replace = array(
					$periodID, 		// %id
					$momentID, 	 	// %begin_moment_id
					$momentID+1,	// %end_moment_id					
				);
				
				$periodZeile = str_replace( $periodVars, $replace, $periodTemplate );
				$periodsFile .= $periodZeile;

				$periodID++;

			// timing.moment 				
				
				// %year_id_begin)
				if (!empty($zitat->datumA_code[2])) {
					$replace = array(
						$momentID, 				// %id
						'begin', 				// %type
						//MakeTimeBegin($zitat->datumA_code[2], $zitat->datumA_code[1], $zitat->datumA_code[0]),	// %timestamp'
						MakeTime(1, $zitat->datumA_code[2], $zitat->datumA_code[1], $zitat->datumA_code[0], $zitat->datumB_code[2], $zitat->datumB_code[1], $zitat->datumB_code[0]),
						$zitat->datumA_code[0],	// %day_id
						$zitat->datumA_code[1],	// %month_id
						$zitat->datumA_code[2],	// %year
					);
					
					$momentZeile = str_replace( $momentVars, $replace, $momentTemplate );
					$momentsFile .= $momentZeile;

					$momentID++;
				}

				// %year_id_end)
				if (!empty($zitat->datumB_code[2])) {
					MakeTime(2, $zitat->datumA_code[2], $zitat->datumA_code[1], $zitat->datumA_code[0], $zitat->datumB_code[2], $zitat->datumB_code[1], $zitat->datumB_code[0]);

					$replace = array(
						$momentID, 				// %id
						'end', 					// %type
						//MakeTimeEnd($zitat->datumA_code[2], $zitat->datumA_code[1], $zitat->datumA_code[0], $zitat->datumB_code[2], $zitat->datumB_code[1], $zitat->datumB_code[0]),	// %timestamp'
						MakeTime(2, $zitat->datumA_code[2], $zitat->datumA_code[1], $zitat->datumA_code[0], $zitat->datumB_code[2], $zitat->datumB_code[1], $zitat->datumB_code[0]),
						$zitat->datumB_code[0],	// %day_id
						$zitat->datumB_code[1],	// %month_id
						$zitat->datumB_code[2],	// %year
					);
					
					$momentZeile = str_replace( $momentVars, $replace, $momentTemplate );
					$momentsFile .= $momentZeile;

					$momentID++;
				}
					
				}				
				
				$eventID++;
				$codingsetID++;
				$codingitemID++;
			}
			
			$quoteID++;
		}
		echo "Quote $quoteID Event $eventID";
		echo "\n";
	}
}


$f = fopen($ordner_ausgabe."og_setup__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $weikinnSetupFile );
fclose($f);


$f = fopen($ordner_ausgabe."og_cleanup__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $weikinnCleanupFile );
fclose($f);


$f = fopen($ordner_ausgabe."05_og_quotes__".date('Y-m-d__H-i').".sql",'w');
//$f = fopen($ordner_ausgabe."quotes__.sql",'w');
fwrite( $f, $quotesFile );
fclose($f);

$f = fopen($ordner_ausgabe."01_og_codingsets__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $codingsetsFile );
fclose($f);

$f = fopen($ordner_ausgabe."02_og_codingitems__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $codingitemsFile );
fclose($f);

$f = fopen($ordner_ausgabe."03_og_timing_moments__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $momentsFile );
fclose($f);

$f = fopen($ordner_ausgabe."04_og_periods__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $periodsFile );
fclose($f);

$f = fopen($ordner_ausgabe."06_og_events__".date('Y-m-d__H-i').".sql",'w');
//$f = fopen($ordner_ausgabe."events__.sql",'w');
fwrite( $f, $eventsFile );
fclose($f);

// Protokoll speichern
$f = fopen($ordner_ausgabe."protokoll__".date('Y-m-d__H-i').".txt",'w');

fwrite( $f, "Benutzte Dateinamen\n" );
fwrite( $f, print_r( $weikinn->dateinamen(), true) );

fwrite( $f, "\n\nBenutzte Orstabelle\n" );
fwrite( $f, $ortstabelle  );

fwrite( $f, "\n\nFehlerhafte Bilder-Dateinamen:\n" );
fwrite( $f, print_r( $bilder->fehler, true) );

fwrite( $f, "\n\nDiese Dateinamen wurden nicht abgerufen:\n" );
fwrite( $f, print_r( $bilder->report(), true) );

fwrite( $f, "\n\nImportprotokoll:\n" );
fwrite( $f, print_r( $protokoll, true) );


fclose($f);

?>