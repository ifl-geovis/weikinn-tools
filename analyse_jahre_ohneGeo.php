<?php 
require_once( './vendors/php-excel-reader-2.21/excel_reader2.php');

$store = true;

$datafolder = '/Temp/weikinn/jahreOhneGeo/';
$resultfolder = '/Temp/weikinn/';

$files = glob($datafolder."*.xls");
/*
1 lNr	
2 Zettel-Nr.	
3 Zett. / Formel	
4 KZ	
5 Bildname	
6 Forts.	
7 Tag A	
8 Monat A	
9 Jahr A	
10 Tag B	
11 Monat B	
12 Jahr B	
13 Ort	
14 Ort 2 (Datenbank)	
15 Ort 3

*/

$years = array();

$data1 = array();
$data2 = array();
$data3 = array();

$recordCount = array();

foreach ($files as $importfilename) {
	$filecounter++;
	
	$year = explode("_",$importfilename);
	$year = $year[1];
	$years[] = $year;
	
	echo $importfilename." \t$filecounter / ".count($files)." Jahr: ".$year."\n";
	
	$xls = new Spreadsheet_Excel_Reader($importfilename,true,'UTF-8');
	$xls->setOutputEncoding('UTF-8');//ISO-8859-1');
	$xls->setUTFEncoder('mb');
	//$xls->read($importfilename);
	
	$numRows = $xls->rowcount();//$xls->sheets[0]['numRows'];
	
	for($z=2; $z<=$numRows; $z++) {
	
		//$zeile = @$xls->sheets[0]['cells'][$z];
		$ort1 = trim($xls->val($z,13) );//@$zeile[13]);
		$ort2 = trim($xls->val($z,14) );//@$zeile[14]);
		$ort3 = trim($xls->val($z,15) );//@$zeile[15]);
		
		if (!empty($ort1)) {
			extractNames($data1,$ort1,$year);
		
		}
		
		if (!empty($ort2)) {
			extractNames($data2,$ort2,$year);
		}
		
		if (!empty($ort3)) {
			extractNames($data3,$ort3,$year);
		
		}
		
		$ereignis = trim($xls->val($z,16) );
		if (!empty($ereignis)) {
			$recordCount[$year]++;
		}
		
		
	}
	unset ($xls);
}	
	

function extractNames(&$data,$ort,$year) {
	$ort = explode("//",$ort);
	
	foreach( $ort as $name) {
		$name = trim($name);
		if ($year) {
			if (!isset($data[$name])) $data[$name] = array();
			@$data[$name][$year]++;
			//@$dataArray[$name]['count']++;
		} else {
			 $data[$name] = 1;
		}
	}
}



function integrateNames( &$uniqueNames, $name, $array, $years) {
	if ($uniqueNames[$name]) {
		foreach($years as $year) {
			$uniqueNames[$name][$year] += $array[$year];
		}
	} else {
		$uniqueNames[$name]=$array;
	}
}


// Vorbereitung Ausgabe
	



$uniqueNames = array();
$names = array();
$nameColumns = array();

foreach($data1 as $name=>$array) {
	integrateNames( $uniqueNames, $name, $array, $years);
	if (!isset($nameColumns[$name])) $nameColumns[$name] = array();
	$nameColumns[$name]["1"]++;
	$names[$name]=1;
}

$f = fopen($resultfolder."og_namen_1.txt",'w');
fwrite( $f, implode("\n",array_keys($names)) );
fclose($f);
$names = array();

foreach($data2 as $name=>$array) {
	integrateNames( $uniqueNames, $name, $array, $years);
	if (!isset($nameColumns[$name])) $nameColumns[$name] = array();
	$nameColumns[$name]["2"]++;
	$names[$name]=1;
}

$f = fopen($resultfolder."og_namen_2.txt",'w');
fwrite( $f, implode("\n",array_keys($names)) );
fclose($f);
$names = array();

foreach($data3 as $name=>$array) {
	integrateNames( $uniqueNames, $name, $array, $years);
	if (!isset($nameColumns[$name])) $nameColumns[$name] = array();
	$nameColumns[$name]["3"]++;
	$names[$name]=1;
}

$f = fopen($resultfolder."og_namen_3.txt",'w');
fwrite( $f, implode("\n",array_keys($names)) );
fclose($f);


// Ortsdaten laden

$orte = array();
$file = "/Temp/weikinn/120425_Ortsdatei.xls";

/*
1 Ort	
2 Ort 2 (Datenbank)	
3 Ort 3 (Ereignisse)	
4 übertragener Ort (Ort, Quelle)	
5 Nord_Süd	
6 Ost_West
*/

$xls = new Spreadsheet_Excel_Reader($file,true,'UTF-8');
$xls->setOutputEncoding('UTF-8');
$xls->setUTFEncoder('mb');
$numRows = $xls->rowcount();

for($z=3; $z<=$numRows; $z++) {
	$ort1 = trim($xls->val($z,1) );
	$ort2 = trim($xls->val($z,2) );
	$ort3 = trim($xls->val($z,3) );
	$ort4 = trim($xls->val($z,4) );
	//$punkt = trim($xls->val($z,5) )." ".trim($xls->val($z,6) );
	
	if (!empty($ort1)) {
		extractNames($orte,$ort1,null);
	}
	
	if (!empty($ort2)) {
		extractNames($orte,$ort2,null);
	}
	
	if (!empty($ort3)) {
		extractNames($orte,$ort3,null);
	}
	if (!empty($ort4)) {
		extractNames($orte,$ort4,null);
	}
}

// AUSGABE GESAMT 


$datadump = "NAME\tSPALTE1\tSPALTE2\tSPALTE3\tORTSDATEI?\tGESAMTZAHL\t";
foreach($years as $year) { $datadump.=$year."\t"; }
$datadump .= "\n";

foreach($uniqueNames as $name=>$count) {
	$datadump .= $name."\t";
	$datadump .= $nameColumns[$name]["1"]."\t";
	$datadump .= $nameColumns[$name]["2"]."\t";
	$datadump .= $nameColumns[$name]["3"]."\t";
	$datadump .= ($orte[$name] ? "ja" : "nein")."\t";
	$datadump .= array_sum($count)."\t";
	foreach($years as $year) {
		//$datadump .= $count[$year] ? $count[$year] : "";
		$datadump .= $count[$year];
		$datadump .= "\t";
	}
	$datadump .= "\n";
}

$f = fopen($resultfolder."og_alle_namen_unique.txt",'w');
fwrite( $f, $datadump );
fclose($f);

$datadump = "JAHR\tANZAHL ZEILEN\n";
foreach($years as $year) {
	$datadump .= "$year\t{$recordCount[$year]}\n";
}
$f = fopen($resultfolder."anzahl_texte_jahreOhneGeo.txt",'w');
fwrite( $f, $datadump );
fclose($f);










?>