<?php
error_reporting(E_ALL);
require_once( 'weikinn.php' );


echo "Teste Weikinn-Klasse\n\n";

$weikinn = new Weikinn();

echo "Dateinamen\n";
print_r( $weikinn->dateinamen() );


$fehler = array();
$start = microtime(true);

$jahreszahl = 1789;

$jahreszahlen = $weikinn->jahreszahlen() ;
//$jahreszahlen = array(1915);//1789,1794);//,1791,1890,1891);

$bilderOG = new Bilder();
$bilderGeo = new Bilder('/Temp/weikinn/dateinamen_2012-05-22.txt');

$namen = array();

foreach($jahreszahlen as $jahreszahl) {
	if ($weikinn->istGeokodiert($jahreszahl)) {
		$jahr = $weikinn->jahr( $jahreszahl );
		$namen[$jahreszahl]="==================================================";
		while ($jahr->hatWeitereZitate()) {		
			$zitat = $jahr->weiteresZitat();
			
			$CD = array();
			$CDalt = array();
			foreach($zitat->C as $key=>$val) {
				@$CD[$key] = $val.$zitat->D[$key];
				@$CDalt[$key] = $val.$zitat->DLeer[$key];
				
				$bildOG = $bilderOG->findImage($jahreszahl, $val);
				$bildGeo = $bilderGeo->findImage($jahreszahl, $val);
				
				if ($bildOG != $bildGeo) {
					$namen[$jahreszahl.'|'.$val] = "Geo: ".$bildGeo." | OG: ".$bildOG;
				}
			}
			$alt = implode(', ',$CDalt);
			$neu = implode(', ',$CD);
			
			if ($alt!=$neu) {

				$herkunft = $jahr->jahreszahl().'|';
				$herkunft .= $zitat->row.'|';
				$herkunft .= "\tneu: ".$neu;
				$herkunft .= "|\talt: ".$alt;
				$fehler[] = $herkunft;
			}
			
			
			
			
		}
		unset( $jahr );
	}
}

echo "\n\nNamensänderungen in Geo-Jahren Dateinamen seit Geo-Jahre-Import\n";
print_r ($namen);


echo "\n\nSpalte C/D Fehler in Geo-Jahren\n";
print_r ($fehler);


$namen = array();
$fehler = array();

foreach($jahreszahlen as $jahreszahl) {
	if (!$weikinn->istGeokodiert($jahreszahl)) {
		$jahr = $weikinn->jahr( $jahreszahl );
		$namen[$jahreszahl]="==================================================";
		while ($jahr->hatWeitereZitate()) {		
			$zitat = $jahr->weiteresZitat();
			
			$CD = array();
			$CDalt = array();
			foreach($zitat->C as $key=>$val) {
				@$CD[$key] = $val.$zitat->D[$key];
				@$CDalt[$key] = $val.$zitat->DLeer[$key];
				
				$bildOG = $bilderOG->findImage($jahreszahl, $val);
				$bildGeo = $bilderGeo->findImage($jahreszahl, $val);
				
				if ($bildOG != $bildGeo) {
					$namen[$jahreszahl.'|'.$val] = "Geo: ".$bildGeo." | OG: ".$bildOG;
				}
			}
			$alt = implode(', ',$CDalt);
			$neu = implode(', ',$CD);
			
			if ($alt!=$neu) {

				$herkunft = $jahr->jahreszahl().'|';
				$herkunft .= $zitat->row.'|';
				$herkunft .= "\tneu: ".$neu;
				$herkunft .= "|\talt: ".$alt;
				$fehler[] = $herkunft;
			}
			
			
		}
		unset( $jahr );
	}
}


echo "\n\nNamensänderungen in OG-Jahren Dateinamen seit Geo-Jahre-Import\n";
print_r ($namen);


echo "\n\nSpalte C/D Nicht-Fehler in Nicht-Geo-Jahren\n";
print_r ($fehler);



?>