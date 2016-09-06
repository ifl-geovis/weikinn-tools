<?php

/**

FORMAT DER TABELLEN

1	A	lNr
2	B	Zettel-Nr.
3	C	Zett. / Formel
4	D	KZ
5	E	
6	F	Forts.
7	G	Tag A
8	H	Monat A
9	I	Jahr A
10	J	Tag B
11	K	Monat B
12	L	Jahr B
13	M	Ort
14	N	Ort 2 (Datenbank)
15	O	Ort 3
16	P	Ereignis
17	Q	Übersetzung
18	R	Temperatur
19	S	Niederschlag
20	T	Luftdruck
21	U	Luftfeuchtigkeit
22	V	Wind
23	W	Gewitter
24	X	Hagel
25	Y	Sonstiges
26	Z	Vorname
27	AA	Name
28	AB	Zweitautor
29	AC	Jahr
30	AD	Zusatz
31	AE	Referenz
32	AF	Band
33	AG	Seite
34	AH	Urquelle
35	AI	Anmerkung
36	AJ	Titel
37	AK	vermerkt von



*/

require_once( './vendors/php-excel-reader-2.21/excel_reader2.php');

$ordner			=	'./temp/weikinn';

$ortstabelle 	= 	$ordner."/120425_Ortsdatei.xls";

$ordner_jahre	= 	$ordner.'/jahre/';
$ordner_jahre_og = 	$ordner.'/jahreOhneGeo/';
$ordner_ausgabe	= 	$ordner.'/';

/*
$GLOBAL_PROJECT_ID 	= 2;
$GLOBAL_CREATED_BY 	= 1;
$GLOBAL_PUBLISH 	= true;
$GLOBAL_SOURCE_ID	= 1;
$GLOBAL_TIMESTAMP	= 1234;
*/

mb_regex_encoding("UTF-8");
mb_internal_encoding("UTF-8");

function mytrim( $string ) {
	return trim ($string);
}

class Weikinn {
	private $_jahreszahlen;
	
	private $_dateinamen;
	private $_dateinamen_jahreo;
	private $_dateinamen_jahregeo;
	
	function __construct() {
	}
	
	public function jahreszahlen() {
		if ($this->_jahreszahlen == null) {
			$this->dateinamen();
		}
		return $this->_jahreszahlen;
	}
	
	public function dateinamen() {
		global $ordner_jahre, $ordner_jahre_og;
		
		if ($this->_dateinamen == null) {
			$this->_jahreszahlen = array();
			$this->_dateinamen = array();
			$this->_dateinamen_jahreo = array();
			$this->_dateinamen_jahregeo = array();
			
			$files = glob($ordner_jahre."*.xls");
			foreach ($files as $importfilename) {				
				$year = explode("_",$importfilename);
				$year = $year[1];
				
				$this->_jahreszahlen[] = $year;
				$this->_dateinamen[$year] = $importfilename;
				$this->_dateinamen_jahregeo[$year] = $importfilename;
			}
			
			$files = glob($ordner_jahre_og."*.xls");
			foreach ($files as $importfilename) {				
				$year = explode("_",$importfilename);
				$year = $year[1];
				
				$this->_jahreszahlen[] = $year;
				$this->_dateinamen[$year] = $importfilename;
				$this->_dateinamen_jahreo[$year] = $importfilename;
			}
			
			//$this->testeDateien();
		}
		return $this->_dateinamen;
	}
	
	private function testeDateien() {
		$error = array();
		foreach( $this->_dateinamen as $dateiname) {
			$xls = new Spreadsheet_Excel_Reader($dateiname,true,'UTF-8');
			$xls->setOutputEncoding('UTF-8');//ISO-8859-1');
			$xls->setUTFEncoder('mb');
			if ($xls->rowcount()<10) {
				$error[] = $dateiname;
			}
			unset($xls);
		}
		if (!empty($error)) {
			die( "Jahre fehlerhaft:\n".implode("\n",$error));
		}
	}
	
	public function jahr( $jahreszahl ) {
		$jahr = new Jahr( $this->_dateinamen[$jahreszahl], $jahreszahl );
		$jahr->istGeokodiert = $this->istGeokodiert($jahreszahl);
		return $jahr;
	}
	
	public function istGeokodiert( $jahreszahl ) {
		return array_key_exists ( $jahreszahl, $this->_dateinamen_jahregeo );
	}
}


require_once './vendors/PHPExcel/PHPExcel/IOFactory.php';
class XLS {
	private $xls;
	private $worksheet;
	//private $reader;
	
	function __construct($dateiname, $reader=null) {
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");
		
		if ($reader==null) {
			$reader = PHPExcel_IOFactory::createReader('Excel5');
		}
		//$this->reader = $reader;
		
		$reader->setReadDataOnly(true);
		$this->xls = $reader->load( $dateiname );
		
		$this->xls->setActiveSheetIndex(0);
		$this->worksheet = $this->xls->getActiveSheet();
		
		unset( $reader );
	}
	
	function val ($row, $col) {
		return $this->worksheet->getCellByColumnAndRow($col-1, $row)->getCalculatedValue();
		//return $this->worksheet->getCellByColumnAndRow($col-1, $row)->getValue();
	}
	
	function rowcount() {
		return $this->worksheet->getHighestRow();
	}
	
	function __destruct() {
		$this->xls->disconnectWorksheets();
		unset ($this->xls);
	}
}


class Jahr {
	public $dateiname;
	public $dateiname_kurz;
	public $istGeokodiert;
	public $maxZettelNummer = 0;
	
	private $_jahreszahl;
	private $xls;
	
	private $_rowCounter;
	private $_rowsEstimated;
	
	private $zettelNummer;
	
	function __construct($dateiname, $jahreszahl) {
		$this->_jahreszahl = $jahreszahl;
		$this->dateiname = $dateiname;
		$this->dateiname_kurz = explode( '/Map', $dateiname );
		$this->dateiname_kurz = 'Map'.$this->dateiname_kurz[1];
		
		/*
		$this->xls = new Spreadsheet_Excel_Reader($dateiname,true,'UTF-8');
		$this->xls->setOutputEncoding('UTF-8');//ISO-8859-1');
		$this->xls->setUTFEncoder('mb');
		*/
		
		$this->xls = new XLS($dateiname);
		
		$this->reset();
	}
	
	function __destruct() {
		unset ($this->xls);
	}
	
	public function reset() {
		$this->_rowsEstimated = $this->xls->rowcount();
		$this->_rowCounter = 2;
		
		if ($this->_rowsEstimated<10) {
			die( $this->dateiname . " ist fehlerhaft." );
		}
		
		$this->zettelNummer = 0;
		
		
		$c = $this->xls->val( $this->_rowsEstimated, 3);
		if ($c > 0 && $c < 3000) {
			$search = true;
			while ($search) {
				$c = $this->xls->val( $this->_rowsEstimated+1, 3);
				if ($c > $this->maxZettelNummer) {
					$this->maxZettelNummer = $c;
					$this->_rowsEstimated++;
				} else {
					$search = false;
				}
			}
		}
		if (empty($c)) {
			while( empty($c)) {
				$c = $this->xls->val( --$this->_rowsEstimated, 3);
			}
			$this->maxZettelNummer = $c;
		}
		
	}
	
	public function jahreszahl() {
		return $this->_jahreszahl;
	}
	
	public function hatWeitereZitate() {
		$nextRowNumber = $this->_rowCounter;
		$nextLine = //mytrim( 	$this->xls->val( $nextRowNumber, 1) ).    //greift bei 1907 nicht
					mytrim( 	$this->xls->val( $nextRowNumber, 9) ).
					mytrim( 	$this->xls->val( $nextRowNumber, 16) );
		return !empty($nextLine);
		//return ( ($this->_rowCounter+1) < $this->_rowsEstimated) ;//&& (!empty( $nextLine ));
	}
	
	public function weiteresZitat() {
		
		// code
		$zettel = new Zettel( $this->xls, $this->_rowCounter, $this->_jahreszahl, $this->istGeokodiert, $this->zettelNummer );
		
		$this->_rowCounter += $zettel->rowOffset;
		
		return $zettel;
	}
}


define("SQL_Null", '\N');
define("SQL_True", 't');
define("SQL_False", 'f');


class Zettel  {

	

	public $rowOffset = 1;
	//public $row;
	
	public $rows;
	
	public $istGeokodiert;
	
	public $regelFall;
	public $fall;
	public $fallFehler;
	
	public $datumA;	//array
	public $datumB; //array
	public $datumA_code;	//array
	public $datumB_code; //array
	
	public $datumA_OK;
	public $datumB_OK;
	
	public $datumA_certain;
	public $datumB_certain;
	
	public $datum_description =SQL_Null;
	public $datum_error;
	
	public $datum;
	
	public $C_hoeher;
	public $C = array();
	public $C_identisch;
	
	public $D_leer;
	public $D = array();
	public $DLeer = array();
	public $D_type;
	
	public $F_leer = true;
	
	
	public $GO_identisch;
	public $O_identisch;
	
	public $M;
	public $N;
	public $O;
	public $ort;
	public $ort_weikinn;
	
	public $P_identisch;
	public $P_leer;
	public $P;
	
	public $Q;
	
	public $RY_identisch;
	public $RY_leer;
	public $events = array();
	
	public $ZAJ_identisch;
	public $AJ = array();
	public $AJ_comment = array();
	
	public $AK;
	
	private $xls;
	private $jahr;

	function __construct( $xls, $row, $jahr, $istGeokodiert, &$vorigeZettelNummer) {
		$this->rows = array();
		$this->row = $row;
		$this->xls = $xls;
		$this->jahr = $jahr;
		$this->istGeokodiert = $istGeokodiert;
		
		/*
		for ($col = 1; $col <= $xls->colcount(); $col++) {
			$this->rawdata[mytrim($xls->val( 1, $col))] = mytrim($xls->val( $row, $col));
		}
		*/
		
		/*
		if ($this->istRegelFall($xls, $row)) {
			$this->rowOffset = 1;
		} else {
			if (count($this->D)==0) {
				$this->rowOffset = 1;
			} else {
				$this->rowOffset = count($this->D);
			}
		}
		*/
		/*
		do {
			$this->findeFall($xls, $row, $vorigeZettelNummer);
			$this->rows[] = $row;
			$row++;
		} while( $this->weitereZeilen( $row ) );
		*/
		$this->fall = "0";
		$this->rows[] = $row;
		while ($this->findeFall($xls, $row, $vorigeZettelNummer)) {
			$row++;
			$this->rows[] = $row;
			
		}
		
		$this->rowOffset = count($this->rows);
		$this->leseZeit();
		$this->leseSpalten();
	}
	
	
	/** Gehört die nächste Zeile noch zum Zitat?
	
		TODO: Zusätzliche Logik für zusammenhängende Seiten? (Testen, über nächstes hinaus)
	
		true 	Ja
		false 	Nein
		
		*/
	private function weitereZeilen( $row ) {
		$weitere = false;
		
		if ($this->ist_GO_identisch($row)
			&& $this->ist_P_identisch($row)
			&& $this->ist_RY_identisch($row)
			&& !$this->ist_ZAJ_identisch($row)
			) 
		{
			$weitere = true;
		}
		
		if ($this->ist_GO_identisch($row)
			&& !$this->ist_P_identisch($row)
			&& $this->ist_RY_identisch($row)
			&& $this->ist_ZAJ_identisch($row)
			)
		{
			$weitere = true;
		}
		
		return $weitere;
	}
	
	
	/* Zeit und Ort auswerten
		7	G	Tag A
		8	H	Monat A
		9	I	Jahr A
		10	J	Tag B
		11	K	Monat B
		12	L	Jahr B
		13	M	Ort
		14	N	Ort 2 (Datenbank)
		15	O	Ort 3
		*/
	private function ist_GO_identisch($row) {
		$rowNext = $row+1;
		$xls = $this->xls;
		
		$datum = mytrim($xls->val( $row, 7))==mytrim($xls->val( $rowNext, 7))
				&& mytrim($xls->val( $row, 8))==mytrim($xls->val( $rowNext, 8))
				&& mytrim($xls->val( $row, 9))==mytrim($xls->val( $rowNext, 9))
				&& mytrim($xls->val( $row, 10))==mytrim($xls->val( $rowNext, 10))
				&& mytrim($xls->val( $row, 11))==mytrim($xls->val( $rowNext, 11))
				&& mytrim($xls->val( $row, 12))==mytrim($xls->val( $rowNext, 12));
		
		if ($this->istGeokodiert) {
			return $datum
					//&& mytrim($xls->val( $row, 13))==mytrim($xls->val( $rowNext, 13)) //Weikinn-Name oft nur auf Vorderseite
					&& mytrim($xls->val( $row, 14))==mytrim($xls->val( $rowNext, 14))
					&& mytrim($xls->val( $row, 15))==mytrim($xls->val( $rowNext, 15));
		} else {
		/*
			Fazit: die unten erwähnte
			2. Möglichkeit sollte die folgende zusätzliche Bedingung bekommen "Ort 1
			(2. Zeile) ist leer".
			 
			Für die nicht-georeferenzierten Jahre sollen also zwei Zeilen als "Ort
			identisch" gewertet werden, wenn eine der beiden folgenden Möglichkeiten
			erfüllt ist:
			 
			1. Möglichkeit:
			Ort 1 (1. Zeile) = Ort 1 (2. Zeile) und
			Ort 2 (1. Zeile) = Ort 2 (2. Zeile) und
			Ort 3 (1. Zeile) = Ort 3 (2. Zeile)
			 
			2. Möglichkeit:
			Ort 1 (1. Zeile) = Ort 2 (2. Zeile) und
			Ort 2 (1. Zeile) ist leer und
			Ort 3 (1. Zeile) = Ort 3 (2. Zeile) und
			Ort 1 (2. Zeile) ist leer
			*/
			$case1 = mytrim($xls->val( $row, 13))==mytrim($xls->val( $rowNext, 13))
					&& mytrim($xls->val( $row, 14))==mytrim($xls->val( $rowNext, 14))
					&& mytrim($xls->val( $row, 15))==mytrim($xls->val( $rowNext, 15));
			$case2 = mytrim($xls->val( $row, 14)).mytrim($xls->val( $rowNext, 13));
			$case2 = empty($case2);
			$case2 = $case2 
					&& mytrim($xls->val( $row, 13))==mytrim($xls->val( $rowNext, 14))
					&& mytrim($xls->val( $row, 15))==mytrim($xls->val( $rowNext, 15));
			return $datum
				&& ($case1 || $case2)
				//&& (mytrim($xls->val( $row, 13))==mytrim($xls->val( $rowNext, 13))
				//	|| mytrim($xls->val( $row, 13))==mytrim($xls->val( $rowNext, 14)))
				//&& mytrim($xls->val( $row, 14))==mytrim($xls->val( $rowNext, 14))
				//&& mytrim($xls->val( $row, 15))==mytrim($xls->val( $rowNext, 15));
				;
		}
	}
	
	/* Ereignistext auswerten
		*/
	private function ist_P_identisch($row) {
		$rowNext = $row+1;
		$xls = $this->xls;
		return mytrim($xls->val( $row, 16))==mytrim($xls->val( $rowNext, 16));
	}
	
	
	
	/* Ereigniskodierung auswerten
		18	R	Temperatur
		19	S	Niederschlag
		20	T	Luftdruck
		21	U	Luftfeuchtigkeit
		22	V	Wind
		23	W	Gewitter
		24	X	Hagel
		25	Y	Sonstiges
		*/	
	private function ist_RY_identisch($row) {
		$rowNext = $row+1;
		$xls = $this->xls;
		return mytrim($xls->val( $row, 18))==mytrim($xls->val( $rowNext, 18))
						&& mytrim($xls->val( $row, 19))==mytrim($xls->val( $rowNext, 19))
						&& mytrim($xls->val( $row, 20))==mytrim($xls->val( $rowNext, 20))
						&& mytrim($xls->val( $row, 21))==mytrim($xls->val( $rowNext, 21))
						&& mytrim($xls->val( $row, 22))==mytrim($xls->val( $rowNext, 22))
						&& mytrim($xls->val( $row, 23))==mytrim($xls->val( $rowNext, 23))
						&& mytrim($xls->val( $row, 24))==mytrim($xls->val( $rowNext, 24))
						&& mytrim($xls->val( $row, 25))==mytrim($xls->val( $rowNext, 25));
	}
	
	
	
	/* Quelle Auswerten
		26	Z	Vorname
		27	AA	Name
		28	AB	Zweitautor
		29	AC	Jahr
		30	AD	Zusatz
		31	AE	Referenz
		32	AF	Band
		33	AG	Seite
		34	AH	Urquelle	=> raus
		35	AI	Anmerkung   => raus
		36	AJ	Titel
		37	AK	vermerkt von
		*/
	private function ist_ZAJ_identisch($row) {
		$rowNext = $row+1;
		$xls = $this->xls;
		return ($xls->val( $row, 36))==($xls->val( $rowNext, 36));
		/*
		return 			mytrim($xls->val( $row, 26))==mytrim($xls->val( $rowNext, 26))
						&& mytrim($xls->val( $row, 27))==mytrim($xls->val( $rowNext, 27))
						&& mytrim($xls->val( $row, 28))==mytrim($xls->val( $rowNext, 28))
						&& mytrim($xls->val( $row, 29))==mytrim($xls->val( $rowNext, 29))
						&& mytrim($xls->val( $row, 30))==mytrim($xls->val( $rowNext, 30))
						&& mytrim($xls->val( $row, 31))==mytrim($xls->val( $rowNext, 31))
						&& mytrim($xls->val( $row, 32))==mytrim($xls->val( $rowNext, 32))
						&& mytrim($xls->val( $row, 33))==mytrim($xls->val( $rowNext, 33))
						//&& mytrim($xls->val( $row, 34))==mytrim($xls->val( $rowNext, 34)) // Urquelle
						//&& mytrim($xls->val( $row, 35))==mytrim($xls->val( $rowNext, 35)) // Anmerkung
						&& mytrim($xls->val( $row, 36))==mytrim($xls->val( $rowNext, 36))
						&& mytrim($xls->val( $row, 37))==mytrim($xls->val( $rowNext, 37));*/
	}
	
	
	private function findeFall( $xls, $row, &$zettelNummer ) {
		//echo ":$row:";
		$rowNext = $row+1;
		
		$this->D_leer = ($xls->val( $row, 4));
		$this->D_leer = empty($this->D_leer);
		$D_istA = ($xls->val( $row, 4)=="a");
		
		$D_leer_next = $xls->val( $rowNext, 4);
		$D_leer_next = empty($D_leer_next);
		$D_istA_next = ($xls->val( $rowNext, 4)=="a");
		
		// =WENN(ODER($D3="";$D3="a");C2+1;C2)
		if ($this->D_leer || $D_istA) {
			$c = ++$zettelNummer;
		} else {
			$c = $zettelNummer;
		}
		
		if ($this->istLetzteZeile($xls, $row)) {
			$this->C_identisch = false;
		} else {
			if ($D_leer_next || $D_istA_next) {
				$this->C_identisch = false;
			} else {
				$this->C_identisch = true;
			}
			//$this->C_identisch = ($xls->val( $rowNext, 3)) == ($xls->val( $row, 3));
		}
		//$this->C[] = ($xls->val( $row, 3));
		$this->C[] = $c;
		
		
		
		
		
		
		// Fehlzuordnungen bei mehreren Quellen über mehrere Zettel hinweg
		//if (!$this->D_leer) {
			$this->D[] = ($xls->val( $row, 4));
		//}
		if (!$this->D_leer) {
			$this->DLeer[] = ($xls->val( $row, 4));
		}
		
		$this->F_leer = $this->F_leer && (strtolower(mytrim($xls->val( $row, 6))) != 'x');
		
				
		
		$fallFehler = "";
		
		
		
				
		$this->GO_identisch = $GO_identisch = $this->ist_GO_identisch($row);
		
		/* Ereignistext auswerten
		*/
		$this->P_identisch = $P_identisch = $this->ist_P_identisch($row);;
		
		$this->P_leer = mytrim($xls->val( $row, 16));
		if (empty($this->P_leer) || $this->P_leer=="[]" 
			|| strtolower($this->P_leer)=="[keine angabe.]"
			|| strtolower($this->P_leer)=="[keine angaben.]"
			|| strtolower($this->P_leer)=="[keine angabe]") {
			$this->P_leer = true;
		} else {
			$this->P_leer = false;
		}
		
		
		$this->RY_identisch = $RY_identisch = $this->ist_RY_identisch($row);
		
		$this->RY_leer = mytrim($xls->val( $row, 18)).mytrim($xls->val( $row, 19)).mytrim($xls->val( $row, 20)).
						 mytrim($xls->val( $row, 21)).mytrim($xls->val( $row, 22)).mytrim($xls->val( $row, 23)).
						 mytrim($xls->val( $row, 24)).mytrim($xls->val( $row, 25));
		$this->RY_leer = empty($this->RY_leer);
		
		
		$this->ZAJ_identisch = $ZAJ_identisch = $this->ist_ZAJ_identisch($row);
		
		
		if ($this->C_identisch && $this->D_leer) {
			$fallFehler .= "C identisch aber D leer. ";
		}
		
		if ($this->RY_leer && !$this->P_leer) {
			$fallFehler .= "Ereignisfehler. ";
		}
		
		$continue = false;
		
		
		if (
			$GO_identisch 
			&& $P_identisch
			&& $RY_identisch 
			&& !$ZAJ_identisch
			) {
			
			if ($this->fall!="0") {
				$fallFehler .= "Ambiguitaet mit Fall {$this->fall}";
			}
			
			$this->fall = "Zitat mit mehreren Quellen";
			$this->P = $xls->val( $row, 16);
			
			$continue = true;
			
		} else if (
			$GO_identisch 
			&& !$P_identisch
			&& $RY_identisch 
			&& $ZAJ_identisch
			&& !$this->F_leer
			) {
			
			if ($this->fall!="0") {
				$fallFehler .= "Ambiguitaet mit Fall {$this->fall}";
			}
			
			$this->fall = "Fortgesetztes Zitat";
			$this->P .= $xls->val( $row, 16);
			
			$continue = true;
			
		} else {
			// Wenn schon besetzt, dann ist das hier nur ein Test
			if ($this->fall == "0") {
				$this->fall = "Normal";
				$this->P = $xls->val( $row, 16);
				if ($GO_identisch
					&& $ZAJ_identisch) {
					//$this->fall = "NormalKritisch";
					$fallFehler .= " Normal, aber Ort, Zeit und Quelle ident. ";
				} else if (!$this->F_leer) {
					//$this->fall = "NormalX";
					$fallFehler .= " X gesetzt, kein Folgefall? ";
				}
			} 
		}
		
		$this->fallFehler = $fallFehler;
		
		return $continue;
	}
	
	private function istLetzteZeile($xls, $row) {
		$nextRowNumber = $row+1;
		$nextLine = // mytrim( $xls->val( $nextRowNumber, 1) ) .  // greift bei 1907 nicht
					mytrim( $xls->val( $nextRowNumber, 9) ).
					mytrim( $xls->val( $nextRowNumber, 16) );
		return empty($nextLine);
	}
	
	private function leseSpalten() {
		$xls = $this->xls;
		$rowOne = $this->rows[0];
		
		
		
		foreach ($this->rows as $row) {
			$this->A[] = $xls->val( $row, 1);
			//$this->C[] = $xls->val( $row, 3);
			//$this->D[] = $xls->val( $row, 4);
			
			
		}
		
		$this->ort = $xls->val( $rowOne, 14);
		$this->N = $this->ort;
		
		$this->ort_weikinn = $xls->val( $rowOne, 13);
		$this->M = $this->ort_weikinn;
		
		$this->O = $xls->val( $rowOne, 15);
		
		$Qs = array();
		$Ps = array();
		$AJs = array();
		
		switch ($this->fall) {
			case "Normal":
			case "Zitat mit mehreren Quellen":
				$this->P = $xls->val( $rowOne, 16);
				$this->Q = $xls->val( $rowOne, 17);
				foreach( $this->rows as $row ) {
					$thisAJ = $xls->val( $row, 36);
					$AFAG = $xls->val( $row, 32).$xls->val( $row, 33);
					if (!empty($AFAG)) {
					
						$thisAJ .= " // Zitat";
						if ($xls->val( $row, 32)!=null) $thisAJ .= " in ".$xls->val( $row, 32);
						if ($xls->val( $row, 33)!=null) $thisAJ .= " auf S. ".$xls->val( $row, 33);
						
					}
					
					if ($xls->val( $row, 34)!=null) $thisAJ .= " // Urquelle: ".$xls->val( $row, 34);
					if ($xls->val( $row, 35)!=null) $thisAJ .= " // Anmerkung: ".$xls->val( $row, 35);
					
					
					$this->AJ[] = $thisAJ;
					
					$this->AJ_comment[] = "AJ::".$xls->val( $row, 36).";;"
										 ."AF::".$xls->val( $row, 32).";;"
										 ."AG::".$xls->val( $row, 33).";;"
										 ."AH::".$xls->val( $row, 34).";;"
										 ."AI::".$xls->val( $row, 35).""
										;
				}
			break;
			
			case "Fortgesetztes Zitat":
				$storeQ = false;
				$aj = array();
				$af = array();
				$ag = array();
				$ah = array();
				$ai = array();
				$AFAG = "";
				$AFs = "";
				$AGs = "";
				$AHs = "";
				$AIs = "";
				foreach( $this->rows as $row ) {
					$Ps[] = $xls->val( $row, 16);
					
					$temp = $xls->val( $row, 17);
					$Qs[] = $temp;
					if (!empty($temp)) {
						$storeQ = TRUE;
					}
					
					$temp = $xls->val( $row, 36);
					if (!empty($temp)) {
						$aj[] = $temp;
					}
					
					$temp = $xls->val( $row, 32);
					if (!empty($temp)) {
						$af[] = $temp;
					}
					$AFAG .= $temp;
					$AFs .= $temp;
					
					$temp = $xls->val( $row, 33);
					if (!empty($temp)) {
						$ag[] = $temp;
					}
					$AFAG .= $temp;
					$AGs .= $temp;
					
					$temp = $xls->val( $row, 34);
					if (!empty($temp)) {
						$ah[] = $temp;
					}
					$AHs .= $temp;
					
					$temp = $xls->val( $row, 35);
					if (!empty($temp)) {
						$ai[] = $temp;
					}
					$AIs .= $temp;
				}
				$this->P = implode(" //// ",$Ps);
				if ($storeQ) {
					$this->Q = implode(" //// ",$Qs);
				} else {
					$this->Q = "";
				}
				
				$thisAJ = $xls->val( $row, 36);
				
				
				if (!empty($AFAG)) {
				
					$thisAJ .= " // Zitat";
					if (!empty($AFs)) $thisAJ .= " in ".implode("/",array_unique($af));
					if (!empty($AGs)) $thisAJ .= " auf S. ".implode("/",array_unique($ag));
					
				}
				
				if (!empty($AHs)) $thisAJ .= " // Urquelle: ".implode(" / ",array_unique($ah));
				if (!empty($AIs)) $thisAJ .= " // Anmerkung: ".implode(" ",array_unique($ai));
				
				
				$this->AJ[] = $thisAJ;
				
				$this->AJ_comment[] = "AJ::".implode(" // ",array_unique($aj)).";;"
									 ."AF::".implode(" // ",array_unique($af)).";;"
									 ."AG::".implode(" // ",array_unique($ag)).";;"
									 ."AH::".implode(" // ",array_unique($ah)).";;"
									 ."AI::".implode(" // ",array_unique($ai)).""
									;
			break;
		}
		
		/*
		if (!empty($Qs) && mytrim(implode('',$Qs))!='') {
			$this->Q = implode(" //// ",$Qs);
		}
		
		if (!empty($Ps) && mytrim(implode('',$Ps))!='') {
			$this->P = implode(" //// ",$Ps);
		}
		*/
		
		$event_codes = array(
			'18'=>'R',
			'19'=>'S',
			'20'=>'T',
			'21'=>'U',
			'22'=>'V',
			'23'=>'W',
			'24'=>'X',
			'25'=>'Y'
		);
		
		for ($col = 18; $col<26; $col++) {
			$event_set = mytrim($xls->val( $rowOne, $col));
			if (!empty($event_set)) {
				$this->events[] = $event_codes[$col];
			}
		}
		
		
		$this->AK = $xls->val( $rowOne, 37);
		
	}
	
	private function leseZeit() {
		$xls = $this->xls;
		$row = $this->row;
		
		/* Zeit
		7	G	Tag A
		8	H	Monat A
		9	I	Jahr A
		10	J	Tag B
		11	K	Monat B
		12	L	Jahr B
		*/
		$datumA = array();
		$datumA[0] = mytrim($xls->val( $row, 7));
		$datumA[1] = mytrim($xls->val( $row, 8));
		$datumA[2] = mytrim($xls->val( $row, 9));
		// ^\s*\[
		// \]\s=\>\s\d*
		$tagRP = '\A\[?(um|Um|ca\.|ca|nach|Nach|gegen|Gegen|vor|Vor|nahe|Nahe|ab|Anf\.)?\s?(\[?([1-3]?\d)?(I|II|III)?\.\s?(Dekade|Pentade|H.lfte)?)?(\s?\[?(Anfang|Mitte|Ende)?\]?)?\Z';
		$monatRP = '\A\[?(um|Um|ca\.|ca|nach|Nach|gegen|Gegen|vor|Vor|nahe|Nahe|ab)?\s?(Januar|Februar|M.rz|April|Mai|Juni|Juli|August|September|Oktober|November|Dezember)?(Jan\.|Feb\.|Febr\.|Apr\.|Aug\.|Sep\.|Sept\.|Okt\.|Nov\.|Dez\.)?(Fr.hling|Fr.hjahr|Sommer|Herbst|Winter)?(\d\.\s\H.lfte)?(Anfang|Mitte|Ende)?\]?\Z';
		$jahrRP = '\A\[?\d{4}\]?\Z'; // Bei Winter ist eckige Klammer nicht unsicher, sondern Vorjahr
		
		//preg_match braucht hinten und vorne am Pattern ein #
		$this->datum = "";
		$this->datum_error = null;
		
		$tagEmpty = false;
		$monatEmpty = false;
		$jahrEmpty = false;
		
		// Konformitätsprüfung A
		
		if (empty($datumA[0])) {
			$tagOK = true;
			$tagEmpty = true;
		} else {
			$this->datum .= $datumA[0]." ";
			$tagOK = mb_ereg_match( $tagRP, $datumA[0] );
		}
		if (empty($datumA[1])) {
			$monatOK = true;
			$monatEmpty = true;
		} else {
			$this->datum .= $datumA[1]." ";
			$monatOK = mb_ereg_match( $monatRP, $datumA[1] );
		}
		if (empty($datumA[2])) {
			$jahrOK = false;
			$jahrEmpty = true;
		} else {
			$this->datum .= $datumA[2];
			$jahrOK = mb_ereg_match( $jahrRP, $datumA[2] );
		}
		
		// Parsen A

		$this->datumA_certain = array();
		$this->datumA_certain[0] =SQL_Null;
		$this->datumA_certain[1] =SQL_Null;
		$this->datumA_certain[2] =SQL_Null;
		$this->datumA_code = array();
		$this->datumA_code[0] =SQL_Null;
		$this->datumA_code[1] =SQL_Null;
		$this->datumA_code[2] =SQL_Null;
		
		if ($tagOK) {
			$this->datumA_certain[0] =SQL_True;
			
			$this->parseDay( $datumA[0], $this->datumA_certain[0], $this->datumA_code[0], $tagOK );
			
			$datumA[0] = mytrim( $datumA[0] );
			
			if (!empty($datumA[0])) {
				$this->datumA_code[0] =SQL_Null;
				$this->datumA_certain[0] =SQL_False;
				$tagOK = false;
				$this->datum_error .= "Tag A fehlformatiert: ".$datumA[0]." ";
			}
		} else {
			$this->datum_error .= "Tag A fehlformatiert: ".$datumA[0]." ";
			$this->datumA_certain[0] =SQL_False;
		}
		
		if ($monatOK) {
			$this->datumA_certain[1] =SQL_True;
			
			$this->parseMonth( $datumA[1], $this->datumA_certain[1], $this->datumA_code[1] );
			
			$datumA[1] = mytrim( $datumA[1] );
			
			// hier entsteht der Fehler für November
			if (!empty($datumA[1])) {
				$this->datumA_code[1] =SQL_Null;
				$this->datumA_certain[1] =SQL_False;
				$monatOK = false;
				$this->datum_error .= "Monat A fehlformatiert. ".$datumA[1]." ";
			}
		} else {
			$this->datum_error .= "Monat A fehlformatiert. ".$datumA[1]." ";
			$this->datumA_certain[1] =SQL_False;
		}
		
		if ($jahrOK) {
			$this->datumA_code[2] = str_ireplace(array('[',']'), '', $datumA[2], $count);
			if ($count==0) {
				$this->datumA_certain[2] =SQL_True;
			} else {
				$this->datumA_certain[2] =SQL_False;
			}
		} else {
			$this->datum_error .= "Jahr A fehlformatiert: ".$datumA[2]." ";
			$this->datumA_certain[2] =SQL_False;
		}
		
		$this->datumA_OK = $tagOK && $monatOK && $jahrOK;
		
		// certain zurücksetzen wenn Feld leer
		if ($tagEmpty) $this->datumA_certain[0] =SQL_Null;
		if ($monatEmpty) $this->datumA_certain[1] =SQL_Null;
		if ($jahrEmpty) $this->datumA_certain[2] =SQL_Null;
		
		
		
		
		$tagEmpty = false;
		$monatEmpty = false;
		$jahrEmpty = false;
		
		$datumB = array();
		$datumBgesetzt = mytrim($xls->val( $row, 10)).mytrim($xls->val( $row, 11)).mytrim($xls->val( $row, 12));
		if (!empty($datumBgesetzt)) {
			$datumBgesetzt = true;
			
			$this->datum .= " - ";
			
			$datumB[0] = mytrim($xls->val( $row, 10));
			$datumB[1] = mytrim($xls->val( $row, 11));
			$datumB[2] = mytrim($xls->val( $row, 12));
		}
		
		// Konformitätsprüfung B
		if ($datumBgesetzt) {
			if (empty($datumB[0])) {
				$tagOK = true;
				$tagEmpty = true;
			} else {
				$this->datum .= $datumB[0]." ";
				$tagOK = mb_ereg_match( $tagRP, $datumB[0] );
			}
			if (empty($datumB[1])) {
				$monatOK = true;
				$monatEmpty = true;
			} else {
				$this->datum .= $datumB[1]." ";
				$monatOK = mb_ereg_match( $monatRP, $datumB[1] );
			}
			if (empty($datumB[2])) {
				$jahrOK = true;
				$jahrEmpty = true;
			} else {
				$this->datum .= $datumB[2];
				$jahrOK = mb_ereg_match( $jahrRP, $datumB[2] );
			}
		}
		
		// Parsen B
		$this->datumB_certain = array();
		$this->datumB_certain[0] =SQL_Null;
		$this->datumB_certain[1] =SQL_Null;
		$this->datumB_certain[2] =SQL_Null;
		$this->datumB_code = array();
		$this->datumB_code[0] =SQL_Null;
		$this->datumB_code[1] =SQL_Null;
		$this->datumB_code[2] =SQL_Null;
		
		if ($datumBgesetzt) {
			if ($tagOK) {
				$this->datumB_certain[0] =SQL_True;
				
				$this->parseDay( $datumB[0], $this->datumB_certain[0], $this->datumB_code[0], $tagOK );
				
				$datumB[0] = mytrim( $datumB[0] );
				
				if (!empty($datumB[0])) {
					$this->datumB_code[0] =SQL_Null;
					$tagOK = false;
					$this->datum_error .= "Tag B fehlformatiert: ".$datumB[0]." ";
				}
			} else {
				$this->datum_error .= "Tag B fehlformatiert: ".$datumB[0]." ";
				$this->datumB_certain[0] =SQL_False;
			}
			
			if ($monatOK) {
				$this->datumB_certain[1] =SQL_True;
				
				$this->parseMonth( $datumB[1], $this->datumB_certain[1], $this->datumB_code[1] );
				
				$datumB[1] = mytrim( $datumB[1] );
				
				if (!empty($datumB[1])) {
					$this->datumB_code[1] =SQL_Null;
					$monatOK = false;
					$this->datum_error .= "Monat B fehlformatiert. ".$datumB[1]." ";
				}
			} else {
				$this->datum_error .= "Monat B fehlformatiert. ".$datumB[1]." ";
				$this->datumB_certain[1] =SQL_False;
			}
			
			if ($jahrOK) {
				if (!empty($datumB[2])) {
					$this->datumB_code[2] = str_ireplace(array('[',']'), '', $datumB[2], $count);
					if ($count==0) {
						$this->datumB_certain[2] =SQL_True;
					} else {
						$this->datumB_certain[2] =SQL_False;
					}
				}
			} else {
				$this->datumB_certain[2] =SQL_False;
			}
			$this->datumB_OK = $tagOK && $monatOK && $jahrOK;
		} else {
			$this->datumB_OK = true;
		}
		
		// Auf Winter überprüfen!!!
		if ( $this->datumA_code[1]==13
			&& $this->datumB_code[0]==SQL_Null
			&& $this->datumB_code[1]==SQL_Null) 
		{
			$this->datumA_code[2] = $this->datumB_code[2];
			
			// uncertain übernehmen
			if ($this->datumA_certain[2] ==SQL_False || $this->datumB_certain[2] ==SQL_False)
			{
				$this->datumA_certain[2] =SQL_False;
			}
			
			// B zurücksetzen
			$this->datumB_code[2] =SQL_Null;
			$this->datumB_certain[0] =SQL_Null;
			$this->datumB_certain[1] =SQL_Null;
			$this->datumB_certain[2] =SQL_Null;
		}
		
		if ($this->datumA_OK && $this->datumB_OK){} else {
			$this->datum_description = $this->datum;
		}
		
		if ($tagEmpty) $this->datumB_certain[0] =SQL_Null;
		if ($monatEmpty) $this->datumB_certain[1] =SQL_Null;
		if ($jahrEmpty) $this->datumB_certain[2] =SQL_Null;
		
		
		$this->datumA = $datumA;
		$this->datumB = $datumB;
		
	}
	
	private function parseMonth( &$month, &$month_certain, &$month_code ) {
	
		$month = str_ireplace(array('[',']'), '', $month, $count);
		if ($count>0) {
			$month_certain =SQL_False;
		}
		
		/*
		1;"Januar                                            "
		2;"Februar                                           "
		3;"März                                              "
		4;"April                                             "
		5;"Mai                                               "
		6;"Juni                                              "
		7;"Juli                                              "
		8;"August                                            "
		9;"September                                         "
		10;"Oktober                                           "
		11;"November                                          "
		12;"Dezember                                          "
		13;"Winter                                            "
		14;"Frühjahr                                          "
		15;"Sommer                                            "
		16;"Herbst                                            "
	*/
		$month = str_ireplace(array('januar','jan.'), '', $month, $count);
		if ($count>0) {
			$month_code = 1;
		}
		$month = str_ireplace(array('februar','feb.','febr.'), '', $month, $count);
		if ($count>0) {
			$month_code = 2;
		}
		$month = str_ireplace('märz', '', $month, $count);
		if ($count>0) {
			$month_code = 3;
		}
		$month = str_ireplace(array('april','apr.'), '', $month, $count);
		if ($count>0) {
			$month_code = 4;
		}
		$month = str_ireplace('mai', '', $month, $count);
		if ($count>0) {
			$month_code = 5;
		}
		$month = str_ireplace('juni', '', $month, $count);
		if ($count>0) {
			$month_code = 6;
		}
		$month = str_ireplace('juli', '', $month, $count);
		if ($count>0) {
			$month_code = 7;
		}
		$month = str_ireplace(array('august','aug.'), '', $month, $count);
		if ($count>0) {
			$month_code = 8;
		}
		$month = str_ireplace(array('september','sept.','sep.'), '', $month, $count);
		if ($count>0) {
			$month_code = 9;
		}
		$month = str_ireplace(array('oktober','okt.'), '', $month, $count);
		if ($count>0) {
			$month_code = 10;
		}
		$month = str_ireplace(array('november','nov.'),'', $month, $count);
		if ($count>0) {
			$month_code = 11;
		}
		$month = str_ireplace(array('dezember','dez.'), '', $month, $count);
		if ($count>0) {
			$month_code = 12;
		}
		
		$month = str_ireplace('winter', '', $month, $count);
		if ($count>0) {
			$month_code = 13;
		}
		$month = str_ireplace(array('frühling','frühjahr'), '', $month, $count);
		if ($count>0) {
			$month_code = 14;
		}
		$month = str_ireplace('sommer', '', $month, $count);
		if ($count>0) {
			$month_code = 15;
		}
		$month = str_ireplace('herbst', '', $month, $count);
		if ($count>0) {
			$month_code = 16;
		}		
		$month = str_ireplace(array('anfang','anf.'), '', $month, $count);
		if ($count>0) {
			$month_code = 17;
		}
		$month = str_ireplace('mitte', '', $month, $count);
		if ($count>0) {
			$month_code = 18;
		}
		$month = str_ireplace('ende', '', $month, $count);
		if ($count>0) {
			$month_code = 19;
		}
		
		$month = str_ireplace('hälfte', '', $month, $count);
		if ($count>0) {
			$month = str_ireplace(array('2. ','II. '), '', $month, $count);
			if ($count>0) {
				$month_code = 21;
			}
			$month = str_ireplace(array('1. ','I. '), '', $month, $count);
			if ($count>0) {
				$month_code = 20;
			}
		}
		
		
	}
	
	private function parseDay( &$day, &$day_certain, &$day_code, &$ok ) {
		$day = str_ireplace(array('[',']','ca. ','ca ','um '), '', $day, $count);
		if ($count>0) {
			$day_certain =SQL_False;
		}
		$day = str_ireplace(array('nach ','gegen ','vor ','nahe ','ab '), '', $day, $count);
		if ($count>0) {
			$day_certain =SQL_False;
			$ok = false;
		}
		
		$day = str_ireplace(array('anfang','anf.'), '', $day, $count);
		if ($count>0) {
			$day_code = 41;
		}
		$day = str_ireplace('mitte', '', $day, $count);
		if ($count>0) {
			$day_code = 42;
		}
		$day = str_ireplace('ende', '', $day, $count);
		if ($count>0) {
			$day_code = 43;
		}
		
		$day = str_ireplace('hälfte', '', $day, $count);
		if ($count>0) {
			$day = str_ireplace(array('2. ','ii. '), '', $day, $count);
			if ($count>0) {
				$day_code = 45;
			}
			$day = str_ireplace(array('1. ','i. '), '', $day, $count);
			if ($count>0) {
				$day_code = 44;
			}
		}
		
		$day = str_ireplace('dekade', '', $day, $count);
		if ($count>0) {
			$day = str_ireplace(array('3. ','iii. '), '', $day, $count);
			if ($count>0) {
				$day_code = 40;
			}
			$day = str_ireplace(array('2. ','ii. '), '', $day, $count);
			if ($count>0) {
				$day_code = 39;
			}
			$day = str_ireplace(array('1. ','i. '), '', $day, $count);
			if ($count>0) {
				$day_code = 38;
			}
		}
		
		$day = str_ireplace('pentade', '', $day, $count);
		if ($count>0) {
			$day = str_ireplace('1. ', '', $day, $count);
			if ($count>0) {
				$day_code = 32;
			}
			$day = str_ireplace('2. ', '', $day, $count);
			if ($count>0) {
				$day_code = 33;
			}
			$day = str_ireplace('3. ', '', $day, $count);
			if ($count>0) {
				$day_code = 34;
			}
			$day = str_ireplace('4. ', '', $day, $count);
			if ($count>0) {
				$day_code = 35;
			}
			$day = str_ireplace('5. ', '', $day, $count);
			if ($count>0) {
				$day_code = 36;
			}
			$day = str_ireplace('6. ', '', $day, $count);
			if ($count>0) {
				$day_code = 37;
			}
		}
		
		$day = str_ireplace('.', '', $day, $count);
		if ($count>0) {
			$day_code = intval( $day );
			
			if ($day_code<1 || $day_code>31) {
				$day_code =SQL_Null;
				$ok = false;
			} else {
				$day = str_ireplace($day_code, '', $day, $count);
			}
		}
		
	}
	
	public function zitate() {
		$zitate = array();
		
		switch ($this->fall) {
			case "Normal":
			break;
			
			case "Zitat mit mehreren Quellen":
			break;
			
			case "Fortgesetztes Zitat":
			break;
			
			default:
			break;
		}
		
		return $zitate();
	}
	
	public function quellen() {
		$xls = $this->xls;
		$quellen = array();
		
		foreach ($this->rows as $row) {
			$quellen[] = $xls->val( $row, 36);
		}
		return $quellen;
	}
	
}





class Event {

	private $xls;
	private $rows; //array
	private $fall;
	private $jahr;

	
	private $commentTemplate = 'WEIKINN;;year::%YEAR%;;row::%ROWS%;;C::%C%;;D::%D%;;
DATE;;%G%;;%H%;;%I%;;%J%;;%K%;;%L%;;
PLACE;;M::%M%;;N::%N%;;O:%O%;;
TRANSLATION;;Q::%Q%;;
EVENT;;R::%R%;;S::%S%;;T::%T%;;U::%U%;;V::%V%;;W::%W%;;X::%X%;;Y::%Y%;;
%SOURCE%';
	
	private $sourceTemplate = "SOURCE;;Z::%Z%;;AA::%AA%;;AB::%AB%;;AC::%AC%;;AD::%AD%;;AE::%AE%;;AF::%AF%;;AG::%AG%;;AH::%AH%;;AI::%AI%;;AJ::%AJ%;;";
	
	public $quote = array(
			'source_id'		=> '', 	// FIXER WERT	integer,	
			'project_id'	=> '', 	// FIXER WERT	integer,
			//'doi_id'		=> '', 	// leer			integer,
			'created_by'	=> 1, 	// FIXER WERT	integer,
			//'modified_by'	=> '', 	//  integer,
			'text'			=> '', 	//  text,
			'page'			=> '', 	//  character varying(16),
			'file'			=> '', 	//  character varying(256),
			//'text_vector'	=> '', 	//  tsvector,
			'publish'		=> true, //  FIXER WERT boolean,
			'comment'		=> '', 	//  text,
			'timestamp'		=> '', 	// timestamp with time zone,
		);
		
	public $event = array(
			'quote_id'			=> '', 	// integer NOT NULL,
			'project_id'		=> '', 	// FIXER WERT	integer,
			//'doi_id'			=> '', 	// integer,
			'code_id'			=> '', 	// integer,
			'name_id'			=> '', 	// integer,
			'created_by'		=> 1, 	// FIXER WERT integer,
			//'modified_by'		=> '', 	// integer,
			'measurement'		=> '', 	// numeric,
			'time_begin'		=> '', 	// timestamp without time zone,
			'time_end'			=> '', 	// timestamp without time zone,
			'publish'			=> true,// boolean,
			'time_description'	=> '', 	// text,
			'hour_id_begin'		=> '', 	// integer,
			'hour_id_end'		=> '', 	// integer,
			'day_id_begin'		=> '', 	// integer,
			'day_id_end'		=> '', 	// integer,
			'month_id_begin'	=> '', 	// integer,
			'month_id_end'		=> '', 	// integer,
			'year_begin'		=> '', 	// integer,
			'year_end'			=> '', 	// integer,
			'comment'			=> '', 	// text,
			'year_begin_certain'=> '', 	// boolean,
			'year_end_certain'	=> '', 	// boolean,
			'month_begin_certain'=> '', // boolean,
			'month_end_certain'	=> '', 	// boolean,
			'day_begin_certain'	=> '', 	// boolean,
			'day_end_certain'	=> '', 	// boolean,
			'hour_end_certain'	=> '', 	// boolean,
			'timestamp'			=> '', 	// timestamp with time zone
		);
	
	function __construct( $xls, $row) {
		global $GLOBAL_PROJECT_ID, $GLOBAL_CREATED_BY, $GLOBAL_PUBLISH, $GLOBAL_SOURCE_ID, $GLOBAL_TIMESTAMP;
		
		$this->event['project_id'] = $GLOBAL_PROJECT_ID;
		$this->quote['project_id'] = $GLOBAL_PROJECT_ID;
		
		$this->event['created_by'] = $GLOBAL_CREATED_BY;
		$this->quote['created_by'] = $GLOBAL_CREATED_BY;
		
		$this->event['publish'] = $GLOBAL_PUBLISH;
		$this->quote['publish'] = $GLOBAL_PUBLISH;
		
		$this->quote['source_id'] = $GLOBAL_SOURCE_ID;
		
		$this->event['timestamp'] = $GLOBAL_TIMESTAMP;
		$this->quote['timestamp'] = $GLOBAL_TIMESTAMP;
		
		$this->rawdata = array();
		$this->row = $row;
		$this->xls = $xls;
		
		$this->createQuote();
		$this->storeQuote();
		
		$this->createEventTemplates();
		$this->storeEventsForNames();
		
	}
	
	function createQuote() {
		$xls = $this->xls;
		
		
		switch ($this->fall) {
			case "Normal":
			case "Zitat mit mehreren Quellen":
				$row = $this->rows[0];
				$this->quote['text'] = mytrim($xls->val( $row, 16));
			break;
			
			case "Fortgesetztes Zitat":
				$this->quote['text'] = '';
				$first = true;
				foreach( $this->rows as $row ) {
					if (!first) {
						$this->quote['text'] .= ' /// ';
					} else {
						$first = false;
					}
					$this->quote['text'] .= mytrim($xls->val( $row, 16));
				}
			break;
		}
	
	}
	
		function buildComment() {
		
		}
		
		function buildSourceList() {
		
		}
	
	function storeQuote() {
	
	}
	
	function createEventTemplates() {
	
	}
	
		function parseTime() {
		
		}
		
		function parseEreigniscodes() {
		
		}
	
	function storeEventsForNames() {
	
	}
	
		function parseNames() {
		
		}
}






class Ortstabelle {

/*
1 Ort	
2 Ort 2 (Datenbank)	
3 Ort 3 (Ereignisse)	
4 übertragener Ort (Ort, Quelle)	
5 Nord_Süd	
6 Ost_West
7 Ausrichtung
8 Winkel von Nord
9 lange Achse
10 kurze Achse
	Höhe
	Maßeinheit
	Ebene 3
14 Ebene 2
	Ebene 1
	Land
	Landschaft
	Kontinent
19 Art
	genauere Bezeichnung
	Anmerkung
	Identifikation Ort
	Vorkommen Schreibweise
24 (googlemaps url)
	DGM_Hoehe

*/


	public $locationTypes = array(
		'AE' => 12,	// 12 AE_Weikinn 
		'HL' => 13,	// 13 HL_Weikinn
		'N'  => 14,	// 14 N_Weikinn 
		'O'  => 15,	// 15 O_Weikinn
		'OU' => 16	// 16 OU_Weikinn
	);
	
	public $locations = array();
	
	public $errors = array();
	
	function __construct() {
		global $ortstabelle;
		
		
		$xls = new Spreadsheet_Excel_Reader($ortstabelle, true, 'UTF-8');
		//$xls->setUTFEncoder('iconv');
		$xls->setOutputEncoding('UTF-8');
		$xls->setUTFEncoder('mb');
		$numRows = $xls->rowcount();
		
		
		//$xls = new XLS( $ortstabelle );
		//$numRows = $xls->rowcount();
		
		for($z=2; $z<=$numRows; $z++) {
			$namen = array();
			
			$ort1 = mytrim($xls->val($z,1) );
			$ort2 = $xls->val($z,2);//mytrim($xls->val($z,2) );
			$ort3 = mytrim($xls->val($z,3) );
			$ort4 = mytrim($xls->val($z,4) );
			
			$y = mytrim($xls->val($z,5) );
			$x = mytrim($xls->val($z,6) );
			$punkt = $y." ".$x;
			
			$hasCoordinates = !empty($x) && !empty($x);
			
			if (stripos($punkt,",")!==FALSE) {
				$y = str_replace(",",".", $y);
				$x = str_replace(",",".", $x);
			}
			
			$winkel = $xls->val($z,8);
			$achse_klein = $xls->val($z,10);
			$achse_gross = $xls->val($z,9);
			
			
			$winkel = str_replace(",",".",mytrim($winkel));
			$achse_klein = str_replace(",",".",mytrim($achse_klein));
			$achse_gross = str_replace(",",".",mytrim($achse_gross));
			
			// ausführlicher Test
			
			$punkt .= mytrim($xls->val($z,7) ).$winkel.$achse_klein.$achse_gross
						//.mytrim($xls->val($z,13) ).mytrim($xls->val($z,14) )
						//.mytrim($xls->val($z,15) ).mytrim($xls->val($z,16) )
						;
			
			$wkt_punkt = "SRID=4326;GEOMETRYCOLLECTION(POINT($x $y))";
			$wkt_polygon = null;
			
			if (!empty($achse_gross)) {
				if (empty($winkel)) {
					// Kreis
					$wkt_polygon = $this->createKreis($x, $y, $achse_gross);
				} else {
					// Ellipse
					$wkt_polygon = $this->createEllipse($x, $y, $achse_gross, $achse_klein, $winkel);
				}
			}
			
			
			
			/*
			if (!empty($ort1)) {
				$this->extractNames($namen,$ort1);
			}
			
			if (!empty($ort2)) {
				$this->extractNames($namen,$ort2);
			}
			
			if (!empty($ort3)) {
				$this->extractNames($namen,$ort3);
			}
			if (!empty($ort4)) {
				$this->extractNames($namen,$ort4);
			}
			*/
			if ($hasCoordinates) {
				if (!isset($this->locations[$ort2])) {
					$this->locations[$ort2] = array();
				} else {
					if (!isset($this->locations[$ort2][$punkt])) {
						@$this->errors[$z] .= "Abweichende NAME2 Koordinaten $ort2 | ";
					}
				}
				$this->locations[$ort2][$punkt]++;
				if (!empty($wkt_polygon)) {
					$this->locations[$ort2]['wkt'] = $wkt_polygon;
				} else {
					$this->locations[$ort2]['wkt'] = $wkt_punkt;
				}
				
			} else {
				@$this->errors[$z] .= "Keine Koordinaten | ";
			}
			
			$type = mytrim($xls->val($z,20) );
			
			$this->locations[$ort2]['type'] = $this->locationTypes[$type];
			
			if (empty($this->locations[$ort2]['type'])) {
				$this->locations[$ort2]['type'] = '15';
			}
		}
	}
	
	private function extractNames(&$dataArray,$ort) {
		$ort = explode("//", $ort);
		
		foreach( $ort as $name) {
			$name = mytrim($name);
			$dataArray[] = $name;
		}
	}
	
	
	// Returns geometry string
	private function createKreis($x, $y, $achse_gross) {
		// GEOMETRYCOLLECTION( POINT( x y ));
		// GEOMETRYCOLLECTION( POINT( x y ), POLYGON() );
		return $this->createEllipse( $x, $y, $achse_gross, $achse_gross, 0);
	
	}
	
	public function createEllipse($x, $y, $achse_gross, $achse_klein, $winkel) {
		// GEOMETRYCOLLECTION( POINT( x y ));
		// GEOMETRYCOLLECTION( POINT( x y ), POLYGON() );
		$PI2 = pi()*0.5;
		
		$rotation = deg2rad($winkel -90);// * (pi()/180);
		$cos_rot = cos($rotation);
		$sin_rot = sin($rotation);
		
		$nmax = 8;
		$n4 = $nmax*0.25;
		
		// Kilometer in Grad umrechnen:     
		
		$factor = 111.3 * cos(deg2rad($y));
		
		$horizontal = $achse_gross / $factor;
		$vertical = $achse_gross / 111.3;
		$a = $horizontal - ($horizontal-$vertical)*cos(deg2rad($winkel));
		
		$horizontal = $achse_klein / $factor;
		$vertical = $achse_klein / 111.3;
		$b = $horizontal - ($horizontal-$vertical)*cos(deg2rad($winkel-90));
		
		$wkt = "SRID=4326;GEOMETRYCOLLECTION( POINT($x $y), POLYGON((";
		
		$points = array();
		
		for ($n=0; $n <= $nmax; $n++) {
			$theta = $PI2 * $n/$n4;
			$x1 = $a * cos($theta);
			$y1 = $b * sin($theta);
			
			$x2 = $x + $x1*$cos_rot + $y1*$sin_rot;
			$y2 = $y + $y1*$cos_rot - $x1*$sin_rot;
			
			$points[] = sprintf("%01.4F %01.4F", $x2, $y2);
		}
		$wkt .= implode( ",", $points );
		$wkt .= ")) )";
		$wkt = str_replace("-0.0000","0.0000",$wkt);
		return $wkt;
	}

}


$bilderfilename = $ordner."/dateinamen.txt";
class Bilder {
	
	public $jahre = array();
	
	public $jahreQueried = array();
	public $jahrcodes = array();
	
	public $letzteZettelNummer = array();
	
	public $fehler = array();
	
	
	function __construct($bilderfile=null) {
		global $bilderfilename;
		
		if (isset($bilderfile)) {
			$f = file_get_contents( $bilderfile );
		} else {
			$f = file_get_contents( $bilderfilename );
		}
		
		$lines = explode( "\n", $f );
		
		unset( $f );
		
		$jahr = 0;
		$zettelnummer = 0;
		
		foreach( $lines as $line ) {
			$line2 = preg_replace('/[^(\x20-\x7F)|\r\n]*/','', $line);
			
			if ($line2!=$line) {
				$this->fehler[] = "Sonderzeichen im Dateinamen ersetzt: $line > $line2";
			}
			
			$filenodes = explode( "\\", $line2);
			
			if (count($filenodes)==3) {
			
				$subfolder = explode( "_", $filenodes[1]);
				$jahr = $subfolder[1];
				
				
				if ($jahr>999 && $jahr<2000) {
					if (!isset($this->jahre[$jahr])) {
						$this->jahre[$jahr] = array();
						$this->jahrcodes[$jahr] = $subfolder[0];
					}
					
					$filename = explode( ".", $filenodes[2]);
					
					// Ausnahme für 1800 und 1802
					// Hier fehlt der Code vor der Zettelnummer
					if ($jahr==1800 || $jahr==1801) {
						$filename = $this->jahrcodes[$jahr]."_".$filename[0];
					} else {
						$filename = $filename[0];
					}
					
					
					$filename = explode( "_", $filename);
					$zettelnummer = $filename[1];
					
					// Korrekturen für Geo Import
					
					if ($jahr==1796 && $zettelnummer>174) {
						$zettelnummer -= 1;
					}
					if ($jahr==1898 && $zettelnummer>19) {
						$zettelnummer += 1;
					}
					if ($jahr==1904 && $zettelnummer>18) {
						$zettelnummer += 1;
					}
					if ($jahr==1905 && $zettelnummer>57) {
						$zettelnummer += 1;
					}
					
					
					// Korrekturen für OG Import
					if ($jahr==1748 && $zettelnummer>278) {
						$zettelnummer += 1;
					}
					if ($jahr==1750 && $zettelnummer>177) {
						$zettelnummer += 1;
					}
					if ($jahr==1784 && $zettelnummer>12 && $zettelnummer<424) {
						$zettelnummer -= 1;
					}
					if ($jahr==1827 && $zettelnummer>312) {
						$zettelnummer += 1;
					}
					if ($jahr==1838 && $zettelnummer>11) {
						$zettelnummer += 1;
					}
					if ($jahr==1865 && $zettelnummer>3) {
						$zettelnummer += 1;
					}
					if ($jahr==1884 && $zettelnummer>92) {
						$zettelnummer -= 1;
					}
					
					
					$dateijahr = "";
					$dateidatum = array();
					$dateiort = array();
					$dateijahrpos = 2;
					while ( $dateijahr=="" && $dateijahrpos<count($filename)) {
						if ( ($filename[$dateijahrpos]==$jahr) 
							|| ($filename[$dateijahrpos]==($jahr-1)) ) {
							$dateijahr = $filename[$dateijahrpos];
							$dateijahrpos++;
						} else {
							$dateidatum[] = $filename[$dateijahrpos];
							$dateijahrpos++;
						}
					}
					
					if ($dateijahrpos==(count($filename))) {
						$this->fehler[] = "fehlformatierter Dateiname in Zeile:\n$line";
					}
					
					while ($dateijahrpos<count($filename)) {
						$dateiort[] = $filename[$dateijahrpos];
						$dateijahrpos++;
					}
					
					$dateiort = implode(" ",$dateiort);
					
					$bild = array();
					$bild[] = trim($line2); 		// 0	url
					$bild[] = trim($filenodes[2]); //1	file
					$bild[] = $zettelnummer; //2	nummer
					$bild[] = $dateijahr;	  //3 jahr aus dateiname
					$bild[] = $dateidatum;	  //4 array: datum aus dateiname
					$bild[] = $dateiort;	  //5 array: ort & co aus dateiname
					$bild[] = false;		  //6 has follow-up scan
					$bild[] = false;		  //7 has been requested
					
					$this->jahre[$jahr][] = $bild;
					
					// Test, ob Fortgesetzter Scan
					$length = count($this->jahre[$jahr]);
					if ($length>1) {
						$previousBild = $this->jahre[$jahr][$length-2];
						if ($previousBild[2] == $bild[2]) {
							$this->jahre[$jahr][$length-2][6] = true;
						}
					}
					
					$this->letzteZettelNummer[$jahr] = $zettelnummer;
					
				} else {
					$this->fehler[] = "ungueltiges Jahr in Zeile: $line";
				}
			}
		}
	}
	
	function istJahrKonsistent( $year, $maxZettel) {
		if ($this->letzteZettelNummer[$year]==$maxZettel) {
			return true;
		} else {
			return false;
		}
	}
	
	function findImage($year, $sheet, $date=null, $place=null) {
		$this->jahreQueried[$year] = 1;
		//$length = count( $this->jahre[$year] );
		
		if (isset($this->jahre[$year])) {
			$voriges_bild = null;
			foreach ($this->jahre[$year] as $id=>$bild) {
				if ($bild[2]==$sheet) {
					$this->jahre[$year][$id][7] = true;
					$result = $bild[1];
					
					$voriges_bild = $bild;
					$vorige_id = $id;
					while ($voriges_bild[6]) {
						$folge_id = $vorige_id+1;
						$folge_bild = $this->jahre[$year][$folge_id];
						
						$result .= ";;".$folge_bild[1];
						$this->jahre[$year][$folge_id][7] = true;
						
						$voriges_bild = $folge_bild;
						$vorige_id = $folge_id;
					}
					return $result;
					
				}
			}
		}
		return null;
	}
	
	
	// create a report on files that have not been queried
	function report() {
		$ungefragt = array();
		foreach( array_keys($this->jahreQueried) as $year ) {
			if (isset($this->jahre[$year])) {
				foreach ($this->jahre[$year] as $id=>$bild) {
					if (!$bild[7]) {
						$ungefragt[] = $bild[0];
					}
				}
			} else {
				$ungefragt[] = "$year: Abgefragt, aber nicht vorhanden";
			}
		}
		return $ungefragt;
	}
}





class Orte {

}

?>