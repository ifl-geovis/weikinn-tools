<?php
error_reporting(E_ALL);
require_once( 'weikinn.php' );


echo "Teste Weikinn-Klasse\n\n";

$weikinn = new Weikinn();

echo "Dateinamen\n";
print_r( $weikinn->dateinamen() );

/* Einzelfalltest zur Programmierung
*/

/*

echo "\n\n";
echo "Ist 1901 geokodiert? " . $weikinn->istGeokodiert(1901);
echo "\n\n";
echo "Ist 1813 geokodiert? " . $weikinn->istGeokodiert(1840);

$jahr = $weikinn->jahr( 1894 );

echo "\n\n";

echo "Jahr:Zeile:D;F;G-O ident.;P ident.;R-Y ident.;Z-AJ ident.;:Fall:Fehler\n";




echo "{$jahr->jahreszahl()}:\n";

$faelle = 0;
$regelFall = 0;
$sonderFall = 0;

$fallTypen = array();

while ($jahr->hatWeiterezettele()) {
	$faelle++;
	
	$zettel = $jahr->weitereszettel();
	
	
	
	if ($zettel->regelFall) {
		$regelFall++;
	} else {
		$sonderFall++;
		echo $jahr->jahreszahl().':';
		echo $zettel->row.':';
		$type = $zettel->D_type.';';
		$type .= $zettel->F_leer.';';
		$type .= $zettel->GO_identisch.';';
		$type .= $zettel->P_identisch.';';
		$type .= $zettel->RY_identisch.';';
		$type .= $zettel->ZAJ_identisch.';';
		echo $type;
		echo "\n";
		$fallTypen[$type]++;
	}
}
echo "$faelle Faelle. $regelFall Regelfaelle und $sonderFall Sonderfaelle.\n";

print_r( $fallTypen );

$jahr->reset();

if ($jahr->hatWeiterezettele()) {
	//print_r( $jahr->weitereszettel()->rawdata );
}
*/

// =============================================================

/* Durchlauf durch alle
*/



$fallTypen = array();
$fehler = array();
$komisch = array();
$datumTypen = array();
$datumFehler = array();

$datumTypen[0] = array();
$datumTypen[1] = array();
$datumTypen[2] = array();

$datumTest = array();

$quellen = array();

//echo "Jahr:Zeile:Cs ident.;D;F nicht x;G-O ident.;P ident.;R-Y ident.;Z-AJ ident.;:Fall:Fehler\n";

$start = microtime(true);

$jahreszahl = 1789;

$jahreszahlen = $weikinn->jahreszahlen() ;
//$jahreszahlen = array(1897);//1789,1794);//,1791,1890,1891);


$bilder = new Bilder();


//print_r($bilder->jahre[1789]);


$nummerJahre = array();
$bilderUndZitate = array();

foreach($jahreszahlen as $jahreszahl) {
	if ($weikinn->istGeokodiert($jahreszahl)) {
	
		$jahr = $weikinn->jahr( $jahreszahl );
		$text = "$jahreszahl's Zettelnummer XLS vs. Bild\t$jahr->maxZettelNummer".
				"\t{$bilder->letzteZettelNummer[$jahreszahl]}";
		if ( ($bilder->letzteZettelNummer[$jahreszahl])!=$jahr->maxZettelNummer) {
			$text .= "\t!\n";
		} else {
			$text .= "\n";
		}
		$nummerJahre[] = $text;
			
		while ($jahr->hatWeitereZitate()) {		
			
			$zettel = $jahr->weiteresZitat();
		
		
			
			
			
			
			
			
			//$text = "";
			foreach ($zettel->C as $c) {
				$bild = $bilder->findImage($jahreszahl, $c);
				$bilderUndZitate[] = $jahreszahl."\t".$c."\t".$zettel->datum."\t".$zettel->ort_weikinn."\t".$bild;
			}
			
			//$bilderUndZitate[] = $text;
			
		}
		unset( $jahr );
		
		
		
		
		
		
		
		//while ($jahr->hatWeitereZitate()) {		
			/*
			$zettel = $jahr->weiteresZitat();
			
			$herkunft = $jahr->jahreszahl().'|';
			//$herkunft .= $zettel->row.'|';
			$herkunft .= implode(",",$zettel->C).'|';
			
			$type1 = $zettel->C_identisch	?'C;':' ;';
			$type1 .= $zettel->D_type.';';
			$type1 .= $zettel->GO_identisch	?'GO':'  ';
			
			$type2 = $zettel->F_leer		?' ;':'X;';
			//$type2 .= $zettel->GO_identisch	?'GO;':'  ;';
			$type2 .= $zettel->P_identisch	?'P;':' ;';
			$type2 .= $zettel->RY_identisch	?'RY;':'  ;';
			$type2 .= $zettel->ZAJ_identisch?'AJ':'  ';
			
			$fall = $zettel->fall.':';
			$fall .= $zettel->fallFehler;
			
			echo $herkunft.": ".($bilder->findImage($jahreszahl, $zettel->C[0]) )."\n";
			*/
			
			
			/*
			echo $herkunft.$type1.'|'.$type2.'|'.$fall.'|'.join(",",$zettel->rows).'|'.join(",",$zettel->C);
			echo "\n";
			
			if (empty($zettel->fallFehler)) {
				
				if ($zettel->fall=="0") {
					$komisch[] = $herkunft.$type1.'|'.$type2.'|'.$fall;;
				}
				
				$fallTypen[0][$zettel->fall]++;
				$fallTypen[1][$zettel->fall][$type1][$type1.'|'.$type2]++;
				
			} else {
				$fehler[$zettel->fallFehler][] = $herkunft.$type1.'|'.$type2.'|'.$fall;
			}
			
			if ($zettel->datumA_OK) {
				@$datumTypen[0][$zettel->datumA_code[0]]++;
				@$datumTypen[1][$zettel->datumA_code[1]]++;
				@$datumTypen[2][$zettel->datumA_code[2]]++;
			} else {
				//@$datumFehler[] = $herkunft.$zettel->datum_error."|A ".$zettel->datum_description;
			}
				
			if (!empty($zettel->datumB)) {
				if ($zettel->datumB_OK) {
					@$datumTypen[0][$zettel->datumB_code[0]]++;
					@$datumTypen[1][$zettel->datumB_code[1]]++;
					@$datumTypen[2][$zettel->datumB_code[2]]++;
				} else {
					//@$datumFehler[] = $herkunft.$zettel->datum_error."|B ".$zettel->datum_description;
				}
			}
			
			if (!empty($zettel->datum_error)) {
				@$datumFehler[] = $herkunft.$zettel->datum_error."|E ".$zettel->datum_description;
			}
			
			@$datumTest[] = $herkunft
							."\t".$zettel->datumA_code[0]."\t".$zettel->datumA_code[1]."\t".$zettel->datumA_code[2]
							."\t".($zettel->datumA_certain[0]).($zettel->datumA_certain[1]).($zettel->datumA_certain[2])
							."\t".$zettel->datumB_code[0]."\t".$zettel->datumB_code[1]."\t".$zettel->datumB_code[2]
							."\t".($zettel->datumB_certain[0]).($zettel->datumB_certain[1]).($zettel->datumB_certain[2])
							."\t".$zettel->datum_error."| E ".$zettel->datum_description;
			
			
			$q = $zettel->quellen();
			foreach ($q as $quelle) {
				@$quellen[$quelle]['count']++;
				@$quellen[$quelle][] = $herkunft;
			}
			*/
		//}
		
		unset( $jahr );
	}
}

echo "\n\nFehlerhafte Dateinamen:\n\n";
print_r($bilder->fehler);


echo "\n\nDiese Dateinamen wurden nicht abgerufen:\n\n";
print_r($bilder->report());

echo "\n\nMaximale Zettelzahl Zitate vs. Bild:\n\n";
print_r($nummerJahre);


echo "\n\nVergleich Zitate und Dateinamen\n\n";
print_r($bilderUndZitate);

/*
echo "\n\nKomische Faelle\n";
print_r ($komisch);

echo "\n\nFehler\n";
print_r ($fehler);

echo "\n\n";
echo "Iteration durch alle zettele dauerte " . (microtime(true)-$start) . " s";
echo "\n\n";

echo "C   C identisch (mit nächster)\n";
echo ";;  \n";
echo "GO  Spalten G bis O sind identisch (außer M/13/Ort)\n\n";
echo "X   Spalte F enthält ein X\n";
echo "P   Zitat ist identisch\n";
echo "RY  Ereigniskodierung ist identisch\n";
echo "ZAJ Quelle ist identisch (außer Urquelle/Anmerkung)\n\n\n";


echo "Falltypen\n";

//sort($fallTypen);

print_r ($fallTypen);


echo "\nDatumswerte\n";

print_r ($datumTypen);

print_r ($datumTest);

echo "\nDatumsfehler\n";
echo "in time_description speichern mit Angabe 'IMPORTFEHLER'\n";

print_r ($datumFehler);


echo "\nQuellen\n";
setlocale(LC_ALL, '');
ksort($quellen, SORT_LOCALE_STRING);
foreach( $quellen as $quelle=>$nennungen) {
	echo $quelle . "\t" . implode(",",$nennungen)."\n";
}


// Nur Infrastruktur: 123.74 Sekunden

// Tests, aber in Datei: 121.42
*/

?>