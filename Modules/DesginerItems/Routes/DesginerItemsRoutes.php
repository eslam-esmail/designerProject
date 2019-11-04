<?php

$router->group([
    'namespace' => 'OlaHub\DesignerCorner\DesginerItems\Controllers',
    'prefix' => strtolower(basename(dirname(__DIR__))),
    'middleware' => []
        ], function () use($router) {

    // List routes
    $router->post('/', 'DesginerItemsController@getPagination');
    $router->post('one', 'DesginerItemsController@getOneByFilter');
    $router->post('filter_details', 'FiltersMainDataController@getMainDataDetails');
    $router->get('prerequestForm', 'DesginerItemsController@getPrerequestFormData');
    
    //other routes
    $router->post('countries', 'DesginerItemsController@listDesginerCountriesFilter');
    $router->post('desginersFilter', 'DesginerItemsController@getDesginersFilter');
    $router->post('categoriesFilter', 'DesginerItemsController@getItemFiltersCatsData');
    $router->post('categoriesFilter/{all:\ball\b}', 'DesginerItemsController@getItemFiltersCatsData');
    $router->post('attributeFilter', 'DesginerItemsController@getItemFiltersAttrsData');
    $router->post('selectedAttributeFilter', 'DesginerItemsController@getSelectedAttributes');    
    $router->post('oneProductData/{productSlug}', 'DesginerItemsController@getOneProductData');
    $router->post('getProductAttributes/{productSlug}/attribute', 'DesginerItemsController@getOneItemAttrsData');
    $router->post('getProductRelatedItems/{productSlug}/related', 'DesginerItemsController@getOneItemRelatedItems');
    $router->post('offerItems', 'DesginerItemsController@getOfferItems');
});
