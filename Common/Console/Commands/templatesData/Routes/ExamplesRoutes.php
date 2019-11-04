<?php

$router->group([
    'namespace' => 'OlaHub\DesignerCorner\ModuleName\Controllers',
    'prefix' => strtolower(basename(dirname(__DIR__))),
    'middleware' => []
        ], function () use($router) {

    // List routes
    $router->post('/', 'ExamplesController@getPagination');
    $router->post('list', 'ExamplesController@getAll');
    $router->get('{id:[0-9]+}', 'ExamplesController@getOneByID');
    $router->post('one', 'ExamplesController@getOneByFilter');
    $router->get('prerequestForm', 'ExamplesController@getPrerequestFormData');
    $router->post('exportFile', 'ExamplesController@exportFile');

    //Add, update & delete  routes
    $router->post('save', 'ExamplesController@createNewEntry');
    $router->put('update/{id:[0-9]+}', 'ExamplesController@updateExsitEntryById');
    $router->post('update', 'ExamplesController@updateExsitEntryByFilter');
});
