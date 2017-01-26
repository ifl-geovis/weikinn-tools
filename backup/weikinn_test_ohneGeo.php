<?php
error_reporting(E_ALL);
require_once( 'weikinn.php' );


echo "Teste Weikinn-Klasse\n\n";

$weikinn = new Weikinn();

echo "Dateinamen\n";
print_r( $weikinn->dateinamen() );

/* Einzelfalltest zur Programmierung
*/


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
//$jahreszahlen = array(1915);//1789,1794);//,1791,1890,1891);

foreach($jahreszahlen as $jahreszahl) {
	if (!$weikinn->istGeokodiert($jahreszahl)) {
		$jahr = $weikinn->jahr( $jahreszahl );
		
		while ($jahr->hatWeitereZitate()) {		
			$zettel = $jahr->weiteresZitat();
			
			$herkunft = $jahr->jahreszahl().'|';
			$herkunft .= $zettel->row.'|';
			
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
			
			echo $herkunft.$type1.'|'.$type2.'|'.$fall.'|'.join(",",$zettel->rows).'|'.join(",",$zettel->C);
			echo "\n";
			
			if (empty($zettel->fallFehler)) {
				
				if ($zettel->fall=="0") {
					$komisch[] = $herkunft.$type1.'|'.$type2.'|'.$fall;;
				}
				
				
				
			} else {
				@$fehler[$zettel->fallFehler][] = $herkunft.$type1.'|'.$type2.'|'.$fall;
			}
			
			@$fallTypen[0][$zettel->fall]++;
			@$fallTypen[1][$zettel->fall][$type1][$type1.'|'.$type2]++;
			
			if ($zettel->datumA_OK) {
				@$datumTypen[0][$zettel->datumA_code[0]]++;
				@$datumTypen[1][$zettel->datumA_code[1]]++;
				@$datumTypen[2]['A'.$zettel->datumA_code[2]]++;
			} else {
				//@$datumFehler[] = $herkunft.$zettel->datum_error."|A ".$zettel->datum_description;
			}
				
			if (!empty($zettel->datumB)) {
				if ($zettel->datumB_OK) {
					@$datumTypen[0][$zettel->datumB_code[0]]++;
					@$datumTypen[1][$zettel->datumB_code[1]]++;
					@$datumTypen[2]['B'.$zettel->datumB_code[2]]++;
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
			
		}
		unset( $jahr );
	}
}



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


?>