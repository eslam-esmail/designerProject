<?php

namespace OlaHub\DesignerCorner\commonData\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use \League\Fractal\Manager;
use \League\Fractal\Resource\Collection as FractalCollection;
use \League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Validator;

class CommonController extends BaseController {

    protected $responseHandler;
    protected $columnsMapping;
    protected $requestData;
    protected $requestFilters;
    protected $model;
    protected $helper;
    protected $response;
    protected $paginateCount;

    public function __construct() {
        
    }

    protected function handlingResponseItem($data, $responseHandler = false) {
        if (!$responseHandler) {
            $responseHandler = $this->responseHandler;
        }
        $fractal = new Manager();
        $resource = new FractalItem($data, new $responseHandler);
        return $fractal->createData($resource)->toArray();
    }

    protected function handlingResponseCollection($data, $responseHandler = false) {
        if (!$responseHandler) {
            $responseHandler = $this->responseHandler;
        }
        $collection = $data;
        $fractal = new Manager();
        $resource = new FractalCollection($collection, new $responseHandler);
        return $fractal->createData($resource)->toArray();
    }

    protected function handlingResponseCollectionPginate($data, $responseHandler = false) {
        if (!$responseHandler) {
            $responseHandler = $this->responseHandler;
        }
        $collection = $data->getCollection();
        $fractal = new Manager();
        $resource = new FractalCollection($collection, new $responseHandler);
        $resource->setPaginator(new IlluminatePaginatorAdapter($data));
        return $fractal->createData($resource)->toArray();
    }

    protected function checkValidation() {
        $return = true;
        $validationData = $this->mappingValidation();
        if (count($validationData) > 0) {
            $requestValidator = Validator::make($this->requestData, $validationData);
            if ($requestValidator->fails()) {
                $return = $this->requestValidator->errors()->toArray();
            }
        }
        return $return;
    }

    private function mappingValidation() {
        $return = [];
        if (is_array($this->columnsMapping) && count($this->columnsMapping) > 0) {
            foreach ($this->columnsMapping as $requestName => $data) {
                if (isset($this->requestData[$requestName]) && isset($data["validations"])) {
                    $return[$requestName] = $data["validations"];
                }
            }
        }
        return $return;
    }

    protected function mapDataNaming() {
        $return = [];
        if (is_array($this->columnsMapping) && count($this->columnsMapping) > 0) {
            foreach ($this->columnsMapping as $requestName => $data) {
                if (isset($this->requestData[$requestName]) && isset($data["columnName"])) {
                    $return[$requestName] = $data["columnName"];
                }
            }
        }
        return $return;
    }

    protected function normalLoop($data) {
        $return = [];
        foreach ($data as $one) {
            if (!in_array($one, $return)) {
                $return[] = $one;
            }
        }
        return $return;
    }

}
