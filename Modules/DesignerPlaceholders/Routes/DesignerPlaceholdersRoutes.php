<?php

$router->group([
    'namespace' => 'OlaHub\DesignerCorner\DesignerPlaceholders\Controllers',
    'prefix' => strtolower(basename(dirname(__DIR__))),
    'middleware' => []
        ], function () use($router) {

    // List routes
    $router->post('/', 'DesignerPlaceholdersController@getPagination');
    $router->post('list', 'DesignerPlaceholdersController@getAll');
    $router->get('{id:[0-9]+}', 'DesignerPlaceholdersController@getOneByID');
    $router->post('one', 'DesignerPlaceholdersController@getOneByFilter');
    $router->get('prerequestForm', 'DesignerPlaceholdersController@getPrerequestFormData');
    $router->post('exportFile', 'DesignerPlaceholdersController@exportFile');

    //Add, update & delete  routes
    $router->post('save', 'DesignerPlaceholdersController@createNewEntry');
    $router->put('update/{id:[0-9]+}', 'DesignerPlaceholdersController@updateExsitEntryById');
    $router->post('update', 'DesignerPlaceholdersController@updateExsitEntryByFilter');
    
    $router->post('homepage', 'DesignerPlaceholdersController@homepageADS');
});
