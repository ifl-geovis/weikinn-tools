# weikinn-tools
collection of scripts to validate weikinn import data 

created by Sebastian Specht, Sebastian Koslitz
copyrighted under MIT license by Leibniz Institute for Regional Geography, Leipzig, Germany 2016

## Usage php scripts:
1. First put `.xls` files in `./temp/weikinn/jahreOhneGeo or temp/weikinn/jahre`
2. Edit `create_import_og.php create_import_og_1571.php` to match proper IDs for tambora db IDs
3. Run with `php weikinn_test_ohneGeo.php > log.txt` for years after 1571
4. Run with `php weikinn_test_ohneGeo_1571.php > log_1571.txt` for years before 1571

## Usage go scripts:

1. Create .csv from pgsql `Select substring(comment FROM 'IMAGEFILES;;.*')
FROM grouping.quote
WHERE project_id = 38 OR project_id = 13` rename file to weikinn_abfrage.csv
2. Move dateinamen.txt and weikinn_abfrage.csv to `temp/weikinn`
3. To validate tambora images against harddisk images run `./validate-tmb-img-on-hd > log.txt`
4. To validate harddisk images against tambora images run `./validate-hd-image-in-tmb > log.txt`