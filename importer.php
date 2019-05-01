<?php
/**
 * SANCTIONS IMPORTER
 * ==================
 *
 * http://sankcijas.kd.gov.lv/files/consolidated.xml - ANO
 * http://sankcijas.kd.gov.lv/files/sdn.xml - OFAC
 * http://sankcijas.kd.gov.lv/files/global.xml - ES vecais
 * http://sankcijas.kd.gov.lv/files/xmlFullSanctionsList_1_1.xml - ES jaunais
 * http://sankcijas.kd.gov.lv/files/LV_national.xml - Latvijas
 *
 * More info: http://sankcijas.kd.gov.lv/
 */

require_once 'vendor/autoload.php';
require_once 'config.php';
error_reporting(E_ALL);

// initialize app
$app = new \SanctionsList\App();
$app->init();

// do schema migration
$app->schemaMigration();

// run imports
$app->import('ano');
$app->import('ofac');
//$app->import('es_old'); // vecais formāts, šo vēlāk slēdzam nost. atstāju pagaidām jo biju jau uztaisījis
$app->import('es');
$app->import('lv');
