<?php

require __DIR__ . '/../plugins/CommonPlugin/functions.php';


$GLOBALS['public_scheme'] = 'http';
$GLOBALS['pageroot'] = '/lists';

function getConfig($key) {
    $phplist_config = [
        'website' => 'www.mysite.com',
    ];

    return $phplist_config[$key];
}
