<?

$GLOBAL_PROJECT_ID 	= 13;
$GLOBAL_CREATED_BY 	= 1;
$GLOBAL_PUBLISH 	= 't';
$GLOBAL_SOURCE_ID	= 2000;
$GLOBAL_TIMESTAMP	= date('Y-m-d H:i:s').'.0+1';

$ordner			=	'/Temp/weikinn';
$ordner_ausgabe	= 	$ordner.'/';

$nameID = 10000;
$locationID = 7000;

$weikinnSetupFile = "-- Weikinn Setup File\n";
$weikinnCleanupFile = "-- Weikinn Cleanup File\n\n-- Removes all data from THIS import session\n\n";

$quotesFile = 'COPY quote (id, source_id, project_id, doi_id, created_by, modified_by, text, page, file, text_vector, publish, comment, "timestamp") FROM stdin;'."\n";
$quoteTemplate = "%id	$GLOBAL_SOURCE_ID	$GLOBAL_PROJECT_ID	\\N	$GLOBAL_CREATED_BY	\\N	%text	%page	%file	\\N	t	%comment	$GLOBAL_TIMESTAMP\n";
$quoteVars = array('%id','%text','%page','%file','%comment');
$quoteID = 60000;

$eventsFile = 'COPY event (id, quote_id, project_id, doi_id, code_id, name_id, created_by, modified_by, measurement, time_begin, time_end, publish, time_description, hour_id_begin, hour_id_end, day_id_begin, day_id_end, month_id_begin, month_id_end, year_begin, year_end, comment, year_begin_certain, year_end_certain, month_begin_certain, month_end_certain, day_begin_certain, day_end_certain, hour_begin_certain, hour_end_certain, "timestamp") FROM stdin;'."\n";
$eventTemplate = "%id	%quote_id	$GLOBAL_PROJECT_ID	\\N	%code_id	%name_id	$GLOBAL_CREATED_BY	\\N	\\N	\\N	\\N	t	%time_description	%hour_id_begin	%hour_id_end	%day_id_begin	%day_id_end	%month_id_begin	%month_id_end	%year_id_begin	%year_id_end	%comment	%year_begin_certain	%year_end_certain	%month_begin_certain	%month_end_certain	%day_begin_certain	%day_end_certain	%hour_begin_certain	%hour_end_certain	$GLOBAL_TIMESTAMP\n";
$eventVars = array('%id','%quote_id','%code_id','%name_id','%time_description',	
					'%hour_id_begin','%hour_id_end','%day_id_begin','%day_id_end','%month_id_begin','%month_id_end',
					'%year_id_begin','%year_id_end','%comment',
					'%year_begin_certain','%year_end_certain','%month_begin_certain','%month_end_certain','%day_begin_certain','%day_end_certain','%hour_begin_certain','%hour_end_certain');
$eventID = 200000;
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
 

require_once( 'weikinn.php' );

echo "Erzeugung der Import-Dateien ============================\n\n";

echo "Erzeuge Weikinn-Quelle... \n\n";

$weikinnSetupFile .= "\n\n-- Weikinn Source\n\n";
$weikinnSetupFile .= "\nINSERT INTO source( id, project_id, created_by, dc_title, publish, \"timestamp\") "
		."VALUES ( $GLOBAL_SOURCE_ID, $GLOBAL_PROJECT_ID, $GLOBAL_CREATED_BY, 'Weikinn', TRUE, '$GLOBAL_TIMESTAMP' );";
$weikinnSetupFile .= "\nINSERT INTO taggroup( id, name, description, is_private) "
		."VALUES ( 10, 'Weikinn_Import', 'temporary item from import $GLOBAL_TIMESTAMP', TRUE );";
$weikinnSetupFile .= "\nINSERT INTO tag( id, taggroup_id, name, description, is_private) "
		."VALUES ( 1001, 10, 'ORT1', 'Weikinn Ortsangabe\\ntemporary item from import $GLOBAL_TIMESTAMP', TRUE );";
$weikinnSetupFile .= "\nINSERT INTO tag( id, taggroup_id, name, description, is_private) "
		."VALUES ( 1002, 10, 'ORT2', 'Kodierter Name durch Digitalisierungsprojekt\\ntemporary item from import $GLOBAL_TIMESTAMP', TRUE );";

$ORT2_TAG_ID = 1002;
		
$weikinnCleanupFile .= "\nDELETE FROM event WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM name WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM location WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM quote WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM source WHERE \"timestamp\"='$GLOBAL_TIMESTAMP';";
$weikinnCleanupFile .= "\nDELETE FROM taggroup WHERE \"description\" LIKE '%".$GLOBAL_TIMESTAMP."%';";
$weikinnCleanupFile .= "\nDELETE FROM tag WHERE \"description\" LIKE '%".$GLOBAL_TIMESTAMP."%';";
$weikinnCleanupFile .= "\nDELETE FROM name_tag WHERE name_id >= $nameID AND tag_id=$ORT2_TAG_ID;";

echo "Erzeuge Event-Codes...\n\n";

$weikinnSetupFile .= "\n\n-- Event Codes\n--uebersprungen\n\n";

echo "Hole Names und Locations aus DB... uebersprungen.\n\n";
echo "Erzeuge Names und Locations aus Ortstabelle...\n\n";


$weikinnSetupFile .= "\n\n-- Names and Locations\n\n";

$orte = new Ortstabelle();

$NameIDs = array();

echo "Fehler in der Ortstabelle:\n";
echo print_r( $orte->errors,true );



foreach ($orte->locations as $ortsname=>$ort) {
	$name = str_replace("'","''",$ortsname);
	if (!empty($name)) {
		$NameIDs[$ortsname] = $nameID;
		$weikinnSetupFile .= "INSERT INTO name( id, location_id, name, created_by, project_id, \"timestamp\" ) "
						."VALUES ($nameID, $locationID, '$name', $GLOBAL_CREATED_BY, $GLOBAL_PROJECT_ID, '$GLOBAL_TIMESTAMP');\n";
						
		$weikinnSetupFile .= "INSERT INTO name_tag( name_id, tag_id ) VALUES ($nameID, $ORT2_TAG_ID);\n";
		
		
		if (empty($ort['wkt'])) {
			//$weikinnSetupFile .= "INSERT INTO location( id, primary_name_id, location_type_id) VALUES ($locationID, $nameID);\n";
			$geo_col = "";
			$geo_val = "";
		} else {
			$geo_col = ", geometry";
			$geo_val = ", ST_GeomFromEWKT('{$ort['wkt']}')";
		}
		$weikinnSetupFile .= "INSERT INTO location( id, primary_name_id, location_type_id, created_by, project_id, \"timestamp\" $geo_col) "
					."VALUES ($locationID, $nameID, {$ort['type']}, $GLOBAL_CREATED_BY, $GLOBAL_PROJECT_ID, '$GLOBAL_TIMESTAMP' $geo_val);\n";
		
		$nameID++;
		$locationID++;
	}
}



echo "Starte Erzeugung der Import-Dateien...\n\n";

$weikinn = new Weikinn();

$bilder = new Bilder();

$protokoll = array();

$jahreszahlen = $weikinn->jahreszahlen() ;
//$jahreszahlen = array(1789,1790,1791,1890,1891,1794);

//$jahreszahlen = array(1906);

foreach($jahreszahlen as $jahreszahl) {
	if ($weikinn->istGeokodiert($jahreszahl)) {
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
			
			$nameID = $NameIDs[$zitat->ort]; //$nameIndex[$zitat->N]
			if (empty($nameID)) {
				$nameID = '\N';
				$comment .= "Importfehler Ort: Ort nicht in Ortstabellen enthalten. ";
				$protokoll[] = $page.": Importfehler Ort: Ort nicht in Ortstabellen enthalten.";
			}
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
				
				$replace = array(
					$eventID,	// %id
					$quoteID,	// %quote_id
					$eventCode,	// %code_id
					$nameID,	// %name_id
					
					$zitat->datum_description,	// %time_description
					
					'\N',	// %hour_id_begin
					'\N',	// %hour_id_end
					$zitat->datumA_code[0],	// %day_id_begin
					$zitat->datumB_code[0],	// %day_id_end
					$zitat->datumA_code[1],	// %month_id_begin
					$zitat->datumB_code[1],	// %month_id_end
					$zitat->datumA_code[2],	// %year_id_begin
					$zitat->datumB_code[2],	// %year_id_end
					$comment,	// %comment

					$zitat->datumA_certain[2],	// %year_begin_certain
					$zitat->datumB_certain[2],	// %year_end_certain
					$zitat->datumA_certain[1],	// %month_begin_certain
					$zitat->datumB_certain[1],	// %month_end_certain
					$zitat->datumA_certain[0],	// %day_begin_certain
					$zitat->datumB_certain[0],	// %day_end_certain
					'\N',	// %hour_begin_certain
					'\N'	// %hour_end_certain
					);
				
				$eventZeile = str_replace( $eventVars, $replace, $eventTemplate );
				$eventsFile .= $eventZeile;
				
				
				$eventID++;
			}
			
			$quoteID++;
		}
		echo "Quote $quoteID Event $eventID";
		echo "\n";
	}
}




$f = fopen($ordner_ausgabe."setup__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $weikinnSetupFile );
fclose($f);


$f = fopen($ordner_ausgabe."cleanup__".date('Y-m-d__H-i').".sql",'w');
fwrite( $f, $weikinnCleanupFile );
fclose($f);


$f = fopen($ordner_ausgabe."quotes__".date('Y-m-d__H-i').".sql",'w');
//$f = fopen($ordner_ausgabe."quotes__.sql",'w');
fwrite( $f, $quotesFile );
fclose($f);


$f = fopen($ordner_ausgabe."events__".date('Y-m-d__H-i').".sql",'w');
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