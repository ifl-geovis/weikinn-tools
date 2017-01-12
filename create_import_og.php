<?php
error_reporting(E_ALL);
require_once( 'weikinn.php' );

$GLOBAL_PROJECT_ID 	= 38;
$GLOBAL_SOURCE_ID	= 3000;
$GLOBAL_CREATED_BY 	= 1;
$GLOBAL_PUBLISH 	= 't';
$GLOBAL_TIMESTAMP	= date('Y-m-d H:i:s').'.0+1';
$GLOBAL_CALENDAR_ID = 1;

$ordner			=	'./temp/weikinn';
$ordner_ausgabe	= 	$ordner.'/sql/';

$nameID = 12000;
$positionID = 314210;

function MakeTimeBegin($year, $month, $day) {
	$year = intval($year);
	$month = intval($month);
	$day = intval($day);

	if ($year == 0) {
		$year = 9999;
	};

	if ($month == 0) {
		$month = 01;
	} else {
		switch ($month) {
			case 13:
				$month = 12;			
				break;
			case 14:
				$month = 03;
				break;
			case 15:
				$month = 06;
				break;
			case 16:
				$month = 09;
				break;
			case 17:
				$month = 01;
				break;
			case 18:
				$month = 05;
				break;
			case 19:
				$month = 09;
				break;
			case 20:
				$month = 01;
				break;
			case 21:
				$month = 07;
				break;			
			default:
				$month = $month;
				break;
		}
	}

	if ($day == 0) {
		$day = 01;
	} else {
		switch ($day) {
			case 32:
				$day = 01;
				break;
			case 33:
				$day = 06;
				break;
			case 34:
				$day = 11;
				break;
			case 35:
				$day = 16;
				break;
			case 36:
				$day = 21;
				break;
			case 37:
				$day = 26;
				break;
			case 38:
				$day = 01;
				break;
			case 39:
				$day = 11;
				break;
			case 40:
				$day = 21;
				break;
			case 41:
				$day = 01;
				break;
			case 42:
				$day = 11;
				break;
			case 43:
				$day = 21;
				break;
			case 44:
				$day = 01;
				break;
			case 45:
				$day = 16;
				break;		
			default:
				$day = $day;
				break;
		}
	}	
	

	$date = new DateTime($year.'-'.$month.'-'.$day);
	$result = $date->format('Y-m-d H:i:s');
	return $result;

}

function MakeTimeEnd($yearBegin, $monthBegin, $dayBegin, $yearEnd, $monthEnd, $dayEnd) {
	$yearBegin = intval($yearBegin);
	$yearEnd = intval($yearEnd);
	$monthBegin = intval($monthBegin);
	$monthEnd = intval($monthEnd);
	$dayBegin = intval($dayBegin);
	$dayEnd = intval($dayEnd);
	
	if ($yearEnd == 0) {
		$yearEnd = $yearBegin;
	}

	if ($dayEnd == 0) {
		if ($dayBegin == 0) {
			if ($monthBegin == 0) {
				$monthEnd = 12;
				$dayEnd = 31;
			} else {
				$monthEnd = $monthBegin;
				if ($monthEnd == 02) {
					$dayEnd = 28;
				} else {
					$dayEnd = 31;	
				}				
			}
		} else {
			switch ($dayBegin) {
				case 32:
					$dayEnd = 05;
					break;
				case 33:
					$dayEnd = 10;
					break;
				case 34:
					$dayEnd = 15;
					break;
				case 35:
					$dayEnd = 20;
					break;
				case 36:
					$dayEnd = 25;
					break;
				case 37:
					$dayEnd = 30;
					break;
				case 38:
					$dayEnd = 10;
					break;
				case 39:
					$dayEnd = 20;
					break;
				case 40:
					$dayEnd = 30;
					break;
				case 41:
					$dayEnd = 10;
					break;
				case 42:
					$dayEnd = 20;
					break;
				case 43:
					$dayEnd = 30;
					break;
				case 44:
					$dayEnd = 15;
					break;
				case 45:
					$dayEnd = 30;
					break;
				case 28:
					$monthEnd = $monthBegin+1;
					$dayBegin = 01;
				case 31:
					if ($monthBegin == 12) {
						$yearEnd = $yearBegin+1;
						$monthEnd = 01;
						$dayEnd = 01;	
					} else {
						$monthEnd = $monthBegin+1;
						$dayEnd = 01;	
					}					
				default:
					if ($dayBegin != 31) {
						$dayEnd = $dayBegin+1;
						break;	
					}					
			}
		}		
	} else {
		switch ($dayEnd) {
			case 32:
				$dayEnd = 05;
				break;
			case 33:
				$dayEnd = 10;
				break;
			case 34:
				$dayEnd = 15;
				break;
			case 35:
				$dayEnd = 20;
				break;
			case 36:
				$dayEnd = 25;
				break;
			case 37:
				$dayEnd = 30;
				break;
			case 38:
				$dayEnd = 10;
				break;
			case 39:
				$dayEnd = 20;
				break;
			case 40:
				$dayEnd = 30;
				break;
			case 41:
				$dayEnd = 10;
				break;
			case 42:
				$dayEnd = 20;
				break;
			case 43:
				$dayEnd = 30;
				break;
			case 44:
				$dayEnd = 15;
				break;
			case 45:
				$dayEnd = 30;
				break;		
			default:
				$dayEnd = $dayEnd;
				break;
		}
	}

	if ($monthEnd == 0) {
		switch ($monthBegin) {
			case 13:
				$monthEnd = 02;
				$yearEnd = $yearEnd+1;
				$dayEnd = 28;
				break;
			case 14:
				$monthEnd = 05;
				$dayEnd = 31;
				break;
			case 15:
				$monthEnd = 08;
				$dayEnd = 31;
				$yearEnd = $yearEnd+1;
				break;
			case 16:
				$monthEnd = 11;
				$dayEnd = 30;
				break;
			case 17:
				$monthEnd = 04;
				$dayEnd = 30;
				break;
			case 18:
				$monthEnd = 08;
				$dayEnd = 31;
				break;
			case 19:
				$monthEnd = 12;
				$dayEnd = 31;
				break;
			case 20:
				$monthEnd = 06;
				$dayEnd = 30;
				break;
			case 21:
				$monthEnd = 12;
				$dayEnd = 31;
				break;			
			default:
				$monthEnd = $monthBegin;
				break;
		}			
	} else {
		switch ($monthEnd) {
			case 13:
				$monthEnd = 02;
				$yearEnd = $yearEnd+1;
				$dayEnd = 28;
				break;
			case 14:
				$monthEnd = 05;
				$dayEnd = 31;
				break;
			case 15:
				$monthEnd = 08;
				$dayEnd = 31;
				break;
			case 16:
				$monthEnd = 11;
				$dayEnd = 30;
				break;
			case 17:
				$monthEnd = 04;
				break;
			case 18:
				$monthEnd = 08;
				$dayEnd = 31;
				break;
			case 19:
				$monthEnd = 12;
				$dayEnd = 31;
				break;
			case 20:
				$monthEnd = 06;
				$dayEnd = 30;
				break;
			case 21:
				$monthEnd = 12;
				$dayEnd = 31;
				break;			
			default:
				$monthEnd = $monthEnd;
				break;
		}
	}
	
	$date = new DateTime($yearEnd.'-'.$monthEnd.'-'.$dayEnd);
	$result = $date->format('Y-m-d H:i:s');

	echo "\n";
	echo "$yearEnd - $monthEnd - $dayEnd \n";
	echo $result;


	return $result;

}

$weikinnSetupFile = "-- Weikinn Setup File\n";
$weikinnCleanupFile = "-- Weikinn Cleanup File\n\n-- Removes all data from THIS import session\n\n";

$quotesFile = 'COPY grouping.quote (id, source_id, project_id, created_by, modified_by, text, page, file, text_vector, public, comment) FROM stdin;'."\n";
$quoteTemplate = "%id	$GLOBAL_SOURCE_ID	$GLOBAL_PROJECT_ID	$GLOBAL_CREATED_BY	\\N	%text	%page	%file	\\N	true	%comment\n";
$quoteVars = array('%id','%text','%page','%file','%comment');
$quoteID = 140000;

$eventsFile = 'COPY event (id, quote_id, project_id, created_by, modified_by, public, comment, period_id, position_id, code_id, source_id, license_id, doi, valid) FROM stdin;'."\n";
$eventTemplate = "%id	%quote_id	$GLOBAL_PROJECT_ID	$GLOBAL_CREATED_BY	\\N	true	%comment	%period_id	%position_id	%code_id	$GLOBAL_SOURCE_ID	\\N	\\N\n";
$eventVars = array('%id','%quote_id','%comment','%period_id','%position_id','%code_id');
$eventID = 220000;
/*
209;"precipitation intensity";999;"information available";0;"Information vorhanden"
204;"temperature intensity";999;"information available";0;"Information vorhanden"
203;"cloud cover";999;"information available";0;"Information vorhanden"
252;"flood intensity";999;"information available";0;"Information vorhanden"
214;"wind force";999;"information available";0;"Information vorhanden"
213;"wind direction";999;"information available";0;"Information vorhanden"
253;"snow intensity";999;"information available";0;"Information vorhanden"
202;"measurement";99;"Barometrische Meßdaten in der Quelle vorhande";1;"Barometrische Meßdaten in der Quelle vorhande"
155;"temperature intensity";99;"Instrumentenmessdaten vorhanden";1;"Instrumentenmessdaten vorhanden"
*/

$eventCodes = array(
	'R'=>array(204,'Temperatur'),
	'S'=>array(209,'Niederschlag'),
	'T'=>array(318,'Luftdruck'),
	'U'=>array(316,'Luftfeuchtigkeit'),
	'V'=>array(314,'Wind'),
	'W'=>array(90,'Gewitter'),
	'X'=>array(116,'Hagel'),
	'Y'=>array(319,'Sonstiges')
	);

$momentsFile = 'COPY timing.moment (id, type, "time", calendar_id, hour_id, day_id, month_id, year, hour_certain, day_certain, month_certain, year_certain, created_by, modified_by) FROM stdin;'."\n";
$momentTemplate = "%id	%type	%timestamp	$GLOBAL_CALENDAR_ID	\\N	%day_id	%month_id	%year	\\N	\\N	\\N \\N $GLOBAL_CREATED_BY	\\N\n";
$momentVars = array('%id', '%type', '%timestamp', '%day_id', '%month_id', '%year');
$momentID = 650000;


$periodsFile = 'COPY timing.period (id, description, begin_moment_id, end_moment_id,  created_by, modified_by) FROM stdin;'."\n";
$periodTemplate = "%id	\\N	%begin_moment_id	%end_moment_id	$GLOBAL_CREATED_BY	\\N\n";
$periodVars = array('%id', '%begin_moment_id', '%end_moment_id');
$periodID = 316000;
 


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
		
$weikinnCleanupFile .= "\nDELETE FROM event WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM name WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM location WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM quote WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM source WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";

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
				$eventCode = $eventCodes[$event_col][0];
				
				$eventVars = array('%id','%quote_id','%comment','%period_id','%position_id','%code_id');
				$replace = array(
					$eventID,	// %id
					$quoteID,	// %quote_id
					$comment,	// %comment
					$periodID, 	// %period_id
					$eventCode,	// %code_id
					$positionID,// %name_id							
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

				// $zitat->datum_description,	// %time_description					
				// $zitat->datumA_code[0],	// %day_id_begin
				// $zitat->datumB_code[0],	// %day_id_end
				// $zitat->datumA_code[1],	// %month_id_begin
				// $zitat->datumB_code[1],	// %month_id_end
				// $zitat->datumA_code[2],	// %year_id_begin
				// $zitat->datumB_code[2],	// %year_id_end
				// $zitat->datumA_certain[2],	// %year_begin_certain
				// $zitat->datumB_certain[2],	// %year_end_certain
				// $zitat->datumA_certain[1],	// %month_begin_certain
				// $zitat->datumB_certain[1],	// %month_end_certain
				// $zitat->datumA_certain[0],	// %day_begin_certain
				// $zitat->datumB_certain[0],	// %day_end_certain			
				
				// %year_id_begin)
				if (!empty($zitat->datumA_code[2])) {

					$replace = array(
						$momentID, 				// %id
						'begin', 				// %type
						MakeTimeBegin($zitat->datumA_code[2], $zitat->datumA_code[1], $zitat->datumA_code[0]),	// %timestamp'
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

					$replace = array(
						$momentID, 				// %id
						'end', 					// %type
						MakeTimeEnd($zitat->datumA_code[2], $zitat->datumA_code[1], $zitat->datumA_code[0], $zitat->datumB_code[2], $zitat->datumB_code[1], $zitat->datumB_code[0]),	// %timestamp'
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


$f = fopen($ordner_ausgabe."og_quotes__".date('Y-m-d__H-i').".sql",'w');
//$f = fopen($ordner_ausgabe."quotes__.sql",'w');
fwrite( $f, $quotesFile );
fclose($f);


$f = fopen($ordner_ausgabe."og_events__".date('Y-m-d__H-i').".sql",'w');
//$f = fopen($ordner_ausgabe."events__.sql",'w');
fwrite( $f, $eventsFile );
fclose($f);

$f = fopen($ordner_ausgabe."og_periods__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $periodsFile );
fclose($f);

$f = fopen($ordner_ausgabe."og_timing_moments__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $momentsFile );
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