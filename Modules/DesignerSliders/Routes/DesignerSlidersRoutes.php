<?php

$router->group([
    'namespace' => 'OlaHub\DesignerCorner\DesignerSliders\Controllers',
    'prefix' => strtolower(basename(dirname(__DIR__))),
    'middleware' => []
        ], function () use($router) {

    // List routes
    $router->post('/', 'DesignerSlidersController@getPagination');
    $router->post('list', 'DesignerSlidersController@getAll');
    $router->get('{id:[0-9]+}', 'DesignerSlidersController@getOneByID');
    $router->post('one', 'DesignerSlidersController@getOneByFilter');
    $router->get('prerequestForm', 'DesignerSlidersController@getPrerequestFormData');
    $router->post('exportFile', 'DesignerSlidersController@exportFile');

    //Add, update & delete  routes
    $router->post('save', 'DesignerSlidersController@createNewEntry');
    $router->put('update/{id:[0-9]+}', 'DesignerSlidersController@updateExsitEntryById');
    $router->post('update', 'DesignerSlidersController@updateExsitEntryByFilter');
    $router->post('slider', 'DesignerSlidersController@showSliderHome');
    
});
