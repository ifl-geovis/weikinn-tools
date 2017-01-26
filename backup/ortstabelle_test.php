<?php
require_once( 'weikinn.php' );


echo "Teste Ortstabelle-Klasse\n\n";


$orte = new Ortstabelle();


//print_r( $orte->locations );

print_r( $orte->errors );



/*

echo $orte->createEllipse( 0, 0, 1113, 111, 0);
echo "\n";
echo $orte->createEllipse( 0, 0, 1113, 111, 90);
echo "\n";
echo $orte->createEllipse( 0, 45, 1113, 111, 90);
echo "\n";
echo $orte->createEllipse( 0, 45, 1113, 111, 45);
echo "\n";

*/


/*
-- DROP TABLE ortstabelle;

CREATE TABLE ortstabelle
(
  id serial NOT NULL,
  name character varying(1024),
  CONSTRAINT id PRIMARY KEY (id )
)
WITH (
  OIDS=FALSE
);
ALTER TABLE ortstabelle
  OWNER TO postgres;
  
  
SELECT AddGeometryColumn( 'ortstabelle', 'geom', 4326, 'GEOMETRYCOLLECTION', 2);


CREATE OR REPLACE VIEW ortstabelle_p AS 
 SELECT ortstabelle.id, ortstabelle.name, st_collectionextract(ortstabelle.geom, 1) AS st_collectionextract
   FROM ortstabelle;

ALTER TABLE ortstabelle_p
  OWNER TO postgres;

  

CREATE OR REPLACE VIEW ortstabelle_poly AS 
 SELECT ortstabelle.id, ortstabelle.name, st_collectionextract(ortstabelle.geom, 3) AS st_collectionextract
   FROM ortstabelle;

ALTER TABLE ortstabelle_poly
  OWNER TO postgres;

  */

// INSERT INTO ortstabelle( name, geom) VALUES ('Hallo',ST_GeomFromEWKT('SRID=4326;GEOMETRYCOLLECTION(POINT(0 0))'))


/*
$sql = "";
foreach ($orte->locations as $ortsname=>$ort) {
	$name = str_replace("'","''",$ortsname);
	$sql.= "INSERT INTO ortstabelle( name, geom) VALUES ('$name',ST_GeomFromEWKT('{$ort['wkt']}'));\n";
}
echo $sql;

*/
?>