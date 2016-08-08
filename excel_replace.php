<?php
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once './vendors/PHPExcel/PHPExcel/IOFactory.php';

$ordner			=	'/Temp/weikinn';

$ortstabelle 	= 	$ordner."/120215_Ortsdatei.xls";

//$ersetzungstabelle 	= 	$ordner."/Ersetzungen-Ortsdatei und georefJahre_120221_nichtlateinisch.xls";
$ersetzungstabelle 	= 	$ordner."/120313_Quellen_3_NW_Blatt1.xls";
$spalte_suchen 	= 	"A";
$spalte_ersetz 	= 	"B";
$spalte_info 	= 	"E";

// Ortstabelle
//$spalte_suchenUndErsetzen 	= 	"B";
//$spalte_suchenUndErsetzen_info 	= 	"AA";

$spalte_suchenUndErsetzen 	= 	"AJ";
$spalte_suchenUndErsetzen_info 	= 	"AO";


$ordner_quelle	= 	$ordner.'/jahre/';
//$ordner_quelle	= 	$ordner.'/jahreOhneGeo/';
$ordner_ziele = 	$ordner.'/jahre_ersetzt/';
$ordner_ausgabe	= 	$ordner.'/';

$search = array();
$replace = array();
$searchIndex = array();

$protokoll = array();

$zaehler = 0;

$files = glob($ordner_quelle."*.xls");
//$files = array();
//$files[] = $ortstabelle;

/**************************************
 * ERSETZUNGSTABELLE LADEN
 **************************************/

// Wir versuchen es zuerst ohne Multibyte-Funktionen 
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
 
echo date('H:i:s') . " Starte...\n";
 
// Simples Oeffnen
//$objPHPExcel = PHPExcel_IOFactory::load( $ortstabelle );

$spalte_suchen 	= PHPExcel_Cell::columnIndexFromString($spalte_suchen)-1;
$spalte_ersetz 	= PHPExcel_Cell::columnIndexFromString($spalte_ersetz)-1;
$spalte_info 	= PHPExcel_Cell::columnIndexFromString($spalte_info)-1;

$objReader = PHPExcel_IOFactory::createReader('Excel5');
// Formate ignorieren - sollte Speicherplatz sparen
$objReader->setReadDataOnly(true);
$ersetungsXLS = $objReader->load( $ersetzungstabelle );


$ersetungsXLS->setActiveSheetIndex(0);
$worksheet = $ersetungsXLS->getActiveSheet();
$highestRow = $worksheet->getHighestRow(); // e.g. 10

for ($r=2; $r<=$highestRow; $r++) {
	$search[] = ($worksheet->getCellByColumnAndRow($spalte_suchen, $r)->getValue());
	$replace[] = ($worksheet->getCellByColumnAndRow($spalte_ersetz, $r)->getValue());
	$searchIndex[$search[(count($search)-1)]] = $r;
}

$ersetungsXLS->disconnectWorksheets();
unset($ersetungsXLS);

echo date('H:i:s') . " Ersetzungstabelle eingelesen\n";
//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, 'Some value');

/*
$objReader = PHPExcel_IOFactory::createReader('Excel5');
// Formate beibehalten 
$objReader->setReadDataOnly(false);
$objPHPExcel = $objReader->load( $ortstabelle );
*/


/**************************************
 * PROCESSING
 **************************************/
$spalte_suchenUndErsetzen 	= PHPExcel_Cell::columnIndexFromString($spalte_suchenUndErsetzen)-1;
$spalte_suchenUndErsetzen_info 	= PHPExcel_Cell::columnIndexFromString($spalte_suchenUndErsetzen_info)-1;

 
foreach ($files as $importfilename) {
	echo date('H:i:s') . " Bearbeite $importfilename\n";
	$protokoll[$importfilename] = array();
	
	$zaehler = 0;
	
	/**************************************
	* DATEI LADEN
	**************************************/
	$objReader->setReadDataOnly(false);
	$xls = $objReader->load( $importfilename );
	
	$xls->setActiveSheetIndex(0);
	$worksheet = $xls->getActiveSheet();
	$highestRow = $worksheet->getHighestRow(); // e.g. 10

	/**************************************
	* SUCHEN & ERSETZEN
	**************************************/
	for ($r=2; $r<=$highestRow; $r++) {
		$searchString = ($worksheet->getCellByColumnAndRow($spalte_suchenUndErsetzen, $r)->getValue());
		
		$replacedString = FALSE;
		
		
		// Ergebnis: null
		/*
		foreach ($search as $key=>$searchValue) {
			$replacedString = mb_ereg_replace( $searchValue, $replace[$key], $searchString);
			if ($replacedString!==FALSE) {
				break;
			}
		}
		*/
		
		
		
		$replacedString = str_replace( $search, $replace, $searchString );
		
		if ($searchString!=$replacedString) {
			$worksheet->setCellValueByColumnAndRow(
					$spalte_suchenUndErsetzen,
					$r,
					$replacedString);
			$worksheet->setCellValueByColumnAndRow(
					$spalte_suchenUndErsetzen_info,
					$r,
					date('Y-m-d H:i:s')."|".$searchIndex[$searchString]."|".$searchString." > ".$replacedString);
			@$protokoll[$importfilename][$searchString]++;
			$zaehler++;
		}
		
		
		
		
		
		/*
		foreach ($search as $key=>$searchValue) {
			if (strpos($searchValue, $searchString)!==false) {
				$worksheet->setCellValueByColumnAndRow(
						$spalte_suchenUndErsetzen_info,
						$r,
						"Treffer! ".$searchIndex[$searchString]);
			}
		}
		// Laufzeit 51s
		*/
		
		
		
		/*
		if (in_array($searchString,$search)) {
			$worksheet->setCellValueByColumnAndRow(
						$spalte_suchenUndErsetzen_info,
						$r,
						"Treffer! ".$searchIndex[$searchString]);
		}
		// Laufzeit 50s
		*/
	}
	
	/**************************************
	* SPEICHERN
	**************************************/
	if ($zaehler>0) {
		$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
		$objWriter->save($importfilename.".ersetzt.xls");
		unset($objWriter);
	}
	
	
	/**************************************
	* AUFRÄUMEN
	**************************************/

	$xls->disconnectWorksheets();
	unset($xls);
	 /*
	echo date('H:i:s') . " Iterate worksheets\n";
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		echo '- ' . $worksheet->getTitle() . "\r\n";

		foreach ($worksheet->getRowIterator() as $row) {
			echo '    - Row number: ' . $row->getRowIndex() . "\r\n";

			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
			foreach ($cellIterator as $cell) {
				if (!is_null($cell)) {
					echo '        - Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getCalculatedValue() . "\r\n";
				}
			}
		}
	}
	*/
	
	// Echo memory peak usage
	echo date('H:i:s') . " Peak memory import: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";
}

/**************************************
 * OUTPUT
 **************************************/

//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save($ortstabelle.".data.xls");


$f = fopen($ordner_ausgabe."protokoll__".date('Y-m-d__H-i').".txt",'w');
fwrite( $f, print_r($protokoll,true) );
fclose($f);


?>