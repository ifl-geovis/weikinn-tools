<?php
error_reporting(E_ALL);


$fehler = array();
$jahre = array();


$f = file_get_contents( "./temp/weikinn/dateinamen.txt" );

$lines = explode( "\n", $f );
$linenum = 0;
$vorigeZettelnummer = "";

foreach( $lines as $line ) {
    $linenum++;
    $line_clean = preg_replace('/[^(\x20-\x7F)|\r\n]*/','', $line);

    if ($line_clean!=$line) {
        $fehler[] = "$linenum\tSonderzeichen\t".trim($line)."\t$line_clean";
    }
    $filenodes = explode( "\\", $line_clean);
    if (count($filenodes)==4) {
        $jahr = $filenodes[2];
        
        // prepare time range
        $jahresabschnitt = array();
        $jahr_von = intval(explode("_", $jahr)[0])-1;
        $jahr_bis = intval(explode("_", $jahr)[1]);
        for ($i=$jahr_von; $i < $jahr_bis+1; $i++) { 
            array_push($jahresabschnitt, $i);
        };
        count($jahresabschnitt); 
        // end prepare 
     
        if ($jahr>999 && $jahr<2000) {

            $neuesJahr = false;
            if (!isset($jahre[$jahr])) {
				$jahre[$jahr] = array();
                $jahre[$jahr]['max'] = 0;
                $jahre[$jahr]['count'] = 0;
                $jahre[$jahr]['last'] = 0;
                $neuesJahr = true;
                $zettelnummer_num = 0;
            }
            $filename = explode( ".", $filenodes[3]);
            $filename = explode( "_", $filename[0]);
            $jahrcode = $filename[0];
            $zettelnummer = $filename[1];
            // maximale Zettelnummer
            $jahre[$jahr]['max'] = max(intval("$zettelnummer"),$jahre[$jahr]['max']);
            // Zettel zählen
            $jahre[$jahr]['count']++;
            //
            $dateijahr = "";
            $dateidatum = array();
            $dateiort = array();
            $dateijahrpos = 2;
            while ( $dateijahr=="" && $dateijahrpos<count($filename)) {

                //if ( ($filename[$dateijahrpos]==$jahr) || ($filename[$dateijahrpos]==($jahr-1)) ) {
                if (in_array($filename[$dateijahrpos], $jahresabschnitt)) {                    
                    $dateijahr = $filename[$dateijahrpos];
                    $dateijahrpos++;
                } else {
                    $dateidatum[] = $filename[$dateijahrpos];
                    $dateijahrpos++;
                }
            }

            if ($dateijahrpos==(count($filename))) {
                $fehler[] = "$linenum\tfehlformatierter Dateiname\t$line";
            }

            while ($dateijahrpos<count($filename)) {
                $dateiort[] = $filename[$dateijahrpos];
                $dateijahrpos++;
            }
            if ($vorigeZettelnummer==$zettelnummer) {
                if (!in_array($filename[$dateijahrpos-1], array('a','b','c','d','e'))) {
                    $fehler[] = "$linenum\tFolgebuchstaben\t$line";
                }
            } else {
                // numerische Zettelnummer
                $zettelnummer_num++;
                if (intval("$zettelnummer")!==$zettelnummer_num) {
                    // unerwartete Zettelnummer, es sei denn es ist ein Folge-Zettel
                    $fehler[] = "$linenum\tunerwartete Zettelnummer\t$line";
                }
            }
            $zettelnummer_num = intval("$zettelnummer");
            $jahre[$jahr]['last'] = $zettelnummer;
            $vorigeZettelnummer = $zettelnummer;
        
        } else {
            $fehler[] = "$linenum\tungueltiges Jahr\t$line";
        }
    }
}

echo "Fehler\n";
foreach($fehler as $value) {
    echo $value."\n";
}

$jahreszahlen = array_keys($jahre);
$spalten = array_keys($jahre[$jahreszahlen[0]]);
echo "Jahre\nJahr\t";
echo implode("\t", $spalten)."\r\n";
foreach($jahre as $year=>$value) {
    echo $year."\t";
    echo implode("\t",$value);
    echo "\r\n";
}

?>