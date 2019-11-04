<?php

$router->group([
    'namespace' => 'OlaHub\DesignerCorner\Additional\Controllers',
    'middleware' => []
        ], function () use($router) {

    // List routes
    $router->post('home/trending', 'GeneralRoutesController@getTrendingItems');
    $router->post('home/offers', 'GeneralRoutesController@getOffersItems');
    $router->post('home/occassions', 'GeneralRoutesController@getOccasionsItems');
    $router->post('home/interests', 'GeneralRoutesController@getInterestsItems');
    $router->post('home/categories', 'GeneralRoutesController@getCategoriesItems');
    $router->post('home/classifications', 'GeneralRoutesController@getClassificationsItems');
    $router->post('designers', 'GeneralRoutesController@getAllDesignersItems');
    $router->post('home/{classSlug:\bfor-women|for-men\b}', 'GeneralRoutesController@getClassesItems');
});
