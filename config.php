<?php

// database connection
$config['dsn'] = 'pgsql://user:pass@localhost:5432/sanctions';

// is debug mode enabled ?
$config['debug'] = true;

// resource configuration
$config['resources'] = [
    'ano' => [
        'url' => 'http://sankcijas.kd.gov.lv/files/consolidated.xml',
        //'url' => 'http://localhost/SanctionsList/docs/consolidated.xml',
        'parser' => \SanctionsList\Resource\ANO::class,
    ],
    'ofac' => [
        'url' => 'http://sankcijas.kd.gov.lv/files/sdn.xml',
        //'url' => 'http://localhost/SanctionsList/docs/sdn.xml',
        'parser' => \SanctionsList\Resource\OFAC::class,
    ],
    'es_old' => [
        'url' => 'http://sankcijas.kd.gov.lv/files/global.xml',
        //'url' => 'http://localhost/SanctionsList/docs/global.xml',
        'parser' => \SanctionsList\Resource\ES_OLD::class,
    ],
    'es' => [
        'url' => 'http://sankcijas.kd.gov.lv/files/xmlFullSanctionsList_1_1.xml',
        //'url' => 'http://localhost/SanctionsList/docs/xmlFullSanctionsList_1_1.xml',
        'parser' => \SanctionsList\Resource\ES::class,
    ],
    'lv' => [
        'url' => 'http://sankcijas.kd.gov.lv/files/LV_national.xml',
        //'url' => 'http://localhost/SanctionsList/docs/LV_national.xml',
        'parser' => \SanctionsList\Resource\LV::class,
    ],
];

// include local config if it exists
if (file_exists(dirname(__FILE__).'/config-local.php')) {
    include(dirname(__FILE__).'/config-local.php');
}
