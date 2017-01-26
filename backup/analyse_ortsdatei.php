<?php 
require_once( './vendors/Excel/reader.php');

$store = true;

$datafolder = '/Temp/weikinn/';

//$files = glob($datafolder."*.xls");
$files = array( $datafolder."101216_Ortsdatei.xls" );
/*
1 Ort	
2 Ort 2 (Datenbank)	
3 Ort 3 (Ereignisse)	
4 übertragener Ort (Ort, Quelle)	
5 Nord_Süd	
6 Ost_West
*/

$data = array();
$data13 = array();
$data134 = array();

foreach ($files as $importfilename) {
	$filecounter++;
	
	echo $importfilename." \t$filecounter / ".count($files)."\n";
	
	$xls = new Spreadsheet_Excel_Reader();
	$xls->setOutputEncoding('UTF-8');//ISO-8859-1');
	$xls->setUTFEncoder('mb');
	$xls->read($importfilename);
	
	$numRows = $xls->sheets[0]['numRows'];
	
	for($z=3; $z<=$numRows; $z++) {
	
		$zeile = @$xls->sheets[0]['cells'][$z];
		$ort1 = trim(@$zeile[1]);
		$ort2 = trim(@$zeile[2]);
		$ort3 = trim(@$zeile[3]);
		$ort4 = trim(@$zeile[4]);
		$punkt = @$zeile[5].' '.@$zeile[6];
		
		if (!empty($ort1)) {
			$ort1 = explode("//",$ort1);
			
			foreach( $ort1 as $ort1name) {
				$ort1name = trim($ort1name);
				if (!isset($data[$ort1name])) $data[$ort1name] = array();
				@$data[$ort1name][$punkt]++;
				if (!isset($data13[$ort1name])) $data13[$ort1name] = array();
				@$data13[$ort1name][$punkt]++;
				if (!isset($data13[$ort1name])) $data134[$ort1name] = array();
				@$data134[$ort1name][$punkt]++;
			}
		
		}
		
		if (!empty($ort2)) {
			$ort2 = explode("//",$ort2);
			
			foreach( $ort2 as $ort2name) {
				$ort2name = trim($ort2name);
				if (!isset($data[$ort2name])) $data[$ort2name] = array();
				@$data[$ort2name][$punkt]++;
			}
		
		}
		
		if (!empty($ort3)) {
			$ort3 = explode("//",$ort3);
			
			foreach( $ort3 as $ort3name) {
				$ort3name = trim($ort3name);
				if (!isset($data[$ort3name])) $data[$ort3name] = array();
				@$data[$ort3name][$punkt]++;
				if (!isset($data13[$ort3name])) $data13[$ort3name] = array();
				@$data13[$ort3name][$punkt]++;
				if (!isset($data134[$ort3name])) $data134[$ort3name] = array();
				@$data134[$ort3name][$punkt]++;
			}
		
		}
		
		if (!empty($ort4)) {
			if (!isset($data[$ort4])) $data[$ort4] = array();
			@$data[$ort4][$punkt]++;
			if (!isset($data134[$ort4])) $data134[$ort4] = array();
			@$data134[$ort4][$punkt]++;
		}
		
		
	
	}
	unset ($xls);
	
	// GEsamtausgabe
	
	$datadump = "name\tpunkt\tanzahl\tsoundex\tmetaphone\tkoelner phonetik\n";//print_r( $data, true );
	$uniqueNames = array();
	
	foreach($data as $name=>$point_count) {
		$uniqueNames[$name]=1;
		foreach($point_count as $point=>$count) {
			$datadump .= $name."\t".$point."\t".$count."\t".soundex($name)."\t".metaphone($name)."\t".cologne_phon($name)."\n";
		}
	}

	$f = fopen($importfilename.".analyse.txt",'w');
	fwrite( $f, $datadump );
	fclose($f);
	
	$f = fopen($importfilename.".unique.txt",'w');
	fwrite( $f, implode("\n",array_keys($uniqueNames)) );
	fclose($f);
	
	// Ort 1 und Ort 3
	
	$datadump = '';
	$uniqueNames = array();
	
	foreach($data13 as $name=>$point_count) {
		$uniqueNames[$name]=1;
		foreach($point_count as $point=>$count) {
			$datadump .= $name."\t".$point."\t".$count."\n";
		}
	}

	$f = fopen($importfilename.".analyse_13.txt",'w');
	fwrite( $f, $datadump );
	fclose($f);
	
	$f = fopen($importfilename.".unique_13.txt",'w');
	fwrite( $f, implode("\n",array_keys($uniqueNames)) );
	fclose($f);
	
	// Ort 1 und Ort 3 und Ort 4
	
	$datadump = '';
	$uniqueNames = array();
	
	foreach($data134 as $name=>$point_count) {
		$uniqueNames[$name]=1;
		foreach($point_count as $point=>$count) {
			$datadump .= $name."\t".$point."\t".$count."\n";
		}
	}

	$f = fopen($importfilename.".analyse_134.txt",'w');
	fwrite( $f, $datadump );
	fclose($f);
	
	$f = fopen($importfilename.".unique_134.txt",'w');
	fwrite( $f, implode("\n",array_keys($uniqueNames)) );
	fclose($f);
}



// Quelle: http://de.php.net/manual/de/function.soundex.php

/**
  * A function for retrieving the Kölner Phonetik value of a string
  * 
  * As described at http://de.wikipedia.org/wiki/Kölner_Phonetik
  * Based on Hans Joachim Postel: Die Kölner Phonetik. 
  * Ein Verfahren zur Identifizierung von Personennamen auf der 
  * Grundlage der Gestaltanalyse. 
  * in: IBM-Nachrichten, 19. Jahrgang, 1969, S. 925-931
  * 
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  * @package phonetics
  * @version 1.0
  * @link http://www.einfachmarke.de
  * @license GPL 3.0 <http://www.gnu.org/licenses/>
  * @copyright  2008 by einfachmarke.de
  * @author Nicolas Zimmer <nicolas dot zimmer at einfachmarke.de>
  */

function cologne_phon($word){
     
   /**
   * @param  string  $word string to be analyzed
   * @return string  $value represents the Kölner Phonetik value
   * @access public
   */
   
     //prepare for processing
     $word=strtolower($word);
     $substitution=array(
             "ä"=>"a",
             "ö"=>"o",
             "ü"=>"u",
             "ß"=>"ss",
             "ph"=>"f"
             );

     foreach ($substitution as $letter=>$substitution) {
         $word=str_replace($letter,$substitution,$word);
     }
     
     $len=strlen($word);
     
     //Rule for exeptions
     $exceptionsLeading=array(
		4=>array("ca","ch","ck","cl","co","cq","cu","cx"),
		8=>array("dc","ds","dz","tc","ts","tz")
     );
     
     $exceptionsFollowing=array("sc","zc","cx","kx","qx");
     
     //Table for coding
     $codingTable=array(
		 0=>array("a","e","i","j","o","u","y"),
		 1=>array("b","p"),
		 2=>array("d","t"),
		 3=>array("f","v","w"),
		 4=>array("c","g","k","q"),
		 48=>array("x"),
		 5=>array("l"),
		 6=>array("m","n"),
		 7=>array("r"),
		 8=>array("c","s","z"),
     );
     
     for ($i=0;$i<$len;$i++){
         $value[$i]="";
         
         //Exceptions
         if ($i==0 AND $word[$i].$word[$i+1]=="cr") $value[$i]=4;
         
         foreach ($exceptionsLeading as $code=>$letters) {
             if (in_array($word[$i].$word[$i+1],$letters)){

                     $value[$i]=$code;

			}                
		}
         
         if ($i!=0 AND (in_array($word[$i-1].$word[$i], $exceptionsFollowing))) {

             $value[$i]=8;        

		}                
         
         //Normal encoding
         if ($value[$i]==""){
                 foreach ($codingTable as $code=>$letters) {
                     if (in_array($word[$i],$letters))$value[$i]=$code;
                 }
             }
         }
     
     //delete double values
     $len=count($value);
     
     for ($i=1;$i<$len;$i++){
         if ($value[$i]==$value[$i-1]) $value[$i]="";
     }
     
     //delete vocals 
     for ($i=1;$i>$len;$i++){//omitting first characer code and h
         if ($value[$i]==0) $value[$i]="";
     }
     
     
     $value=array_filter($value);
     $value=implode("",$value);
     
     return $value;
     
 }






?>