<?php
/**
 * SANCTIONS REST ENDPOINT
 * =======================
 */

require_once '../../vendor/autoload.php';
require_once '../../config.php';
error_reporting(E_ALL);

// initialize API app
$api = new \atk4\api\Api();

// Dummy service
$api->get('/ping', function() {
    return 'pong';
});

// Methods can accept arguments, and everything is type-safe.
$api->get('/search/:q', function ($q) {
    // init app
    $app = new \SanctionsList\App();
    $app->init();

    // data model
    $m = $app->add(new $app->model_class($app->db));

    // full-text search
    $m->addExpression('query', $m->expr('websearch_to_tsquery(\'English\', [])', [$q]));
    $m->addExpression('rank', $m->expr('ts_rank_cd([], [])', [
        $m->getElement('tags'),
        $m->getElement('query'),
    ]));
    $m->addCondition($m->getElement('query'), '@@', $m->getElement('tags'));

    // order by rank and set limit
    $m->setOrder('rank desc');
    $m->setLimit(10);
    //var_dump($m->action('select')->getDebugQuery());

    // response
    return $m->export(['rank','list','type','name','country','sync_time'/*,'tags','query'*/]);
});

// No service matched request
die('No service was called');
