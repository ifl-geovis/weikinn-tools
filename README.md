# weikinn-tools
collection of scripts to validate weikinn import data 

created by Sebastian Specht, Sebastian Koslitz
copyrighted under MIT license by Leibniz Institute for Regional Geography, Leipzig, Germany 2016

## Usage php scripts:
1. First put `.xls` files in `./temp/weikinn/jahreOhneGeo or temp/weikinn/jahre`
2. Run with `php weikinn_test_ohneGeo.php > log.txt`

## Usage go scripts:
1. Create .csv from pgsql `Select substring(comment FROM 'IMAGEFILES;;.*')
FROM grouping.quote
WHERE project_id = 38 OR project_id = 13` rename file to weikinn_abfrage.csv
2. Move dateinamen.txt and weikinn_abfrage.csv to `temp/weikinn`
3. Run `./extract-image.tmb > log.txt`