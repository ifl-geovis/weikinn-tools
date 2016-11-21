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
//$jahreszahlen = array(1800,1802,1803,1804,1805,1806,1807,1808,1809,1810,1811,1812,1813,1814,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1826);


$bilder = new Bilder();


//print_r($bilder->jahre[1789]);


$nummerJahre = array();
$bilderUndZitate = array();

foreach($jahreszahlen as $jahreszahl) {
	if (!$weikinn->istGeokodiert($jahreszahl)) {
	
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

?>