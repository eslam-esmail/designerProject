<?php

namespace OlaHub\DesignerCorner\ModuleName\Controllers;

use OlaHub\DesignerCorner\commonData\Controllers\CommonController as CommonController;
use OlaHub\DesignerCorner\ModuleName\Models\Example;

class ExamplesController extends CommonController {

    public function __construct(\Illuminate\Http\Request $request) {
        parent::__construct();
        $this->model = (new Example)->newQuery();
        $this->helper = new \OlaHub\DesignerCorner\ModuleName\Helpers\ExamplesHelper();
        $requestFinal = $this->helper->returnRequestData($request);
        $this->requestData = isset($requestFinal["requestData"]) ? $requestFinal["requestData"] : [];
        $this->requestFilters = isset($requestFinal["requestFilter"]) ? $requestFinal["requestFilter"] : [];
        $this->responseHandler = "\OlaHub\DesignerCorner\ModuleName\Handlers\ExamplesHandler";
        $this->response = ['status' => false, 'msg' => 'No data found'];
        $this->paginateCount = isset($this->requestFilters["paginateCount"]) && $this->requestFilters["paginateCount"] > 0 ? $this->requestFilters["paginateCount"] : PAGINATE_COUNT;
        $this->columnsMapping = isset(Example::$columnsMapping) ? Example::$columnsMapping : [];
    }

    function getAll() {
        $this->handlingFilters();
        $data = $this->model->get();
        if ($data && $data->count() > 0) {
            $this->response = $this->handlingResponseCollection($data);
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getPagination() {
        $this->handlingFilters();
        $data = $this->model->paginate($this->paginateCount);
        if ($data && $data->count() > 0) {
            $this->response = $this->handlingResponseCollectionPginate($data);
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getOneByID($id) {
        $data = $this->model->find($id);
        if ($data) {
            $this->response = $this->handlingResponseItem($data);
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getOneByFilter() {
        $this->handlingFilters();
        $data = $this->model->first();
        if ($data) {
            $this->response = $this->handlingResponseItem($data);
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }
        return response($this->response);
    }

    function createNewEntry() {
        $validation = $this->checkValidation();
        if ($validation === true) {
            $columnsMaping = $this->mapDataNaming();
            if (is_array($columnsMaping) && count($columnsMaping) > 0) {
                $this->setModelForSave(true);
                $this->setSaveData($columnsMaping);
            }
            return response($this->response);
        }
        return response(['status' => false, "errors" => $validation, 'msg' => 'Some data is wrong'], 415);
    }

    function updateExsitEntryById($id) {
        $validation = $this->checkValidation();
        if ($validation === true) {
            $columnsMaping = $this->mapDataNaming();
            if (is_array($columnsMaping) && count($columnsMaping) > 0) {
                $this->setModelForSave(false, $id);
                if ($this->model) {
                    $this->saveData($columnsMaping);
                }
            }
            return response($this->response);
        }
        return response(['status' => false, "errors" => $validation, 'msg' => 'Some data is wrong'], 415);
    }

    function updateExsitEntryByFilter($id) {
        $validation = $this->checkValidation();
        if ($validation === true) {
            $columnsMaping = $this->mapDataNaming();
            if (is_array($columnsMaping) && count($columnsMaping) > 0) {
                $this->handlingFilters();
                $this->setModelForSave(false);
                if ($this->model) {
                    $this->saveData($columnsMaping);
                }
            }
            return response($this->response);
        }
        return response(['status' => false, "errors" => $validation, 'msg' => 'Some data is wrong'], 415);
    }

    private function handlingFilters() {
        if (count($this->requestFilters) > 0) {
            //Write filter here by using if conditions and $this->model->where();
        }
    }

    private function setSaveData($columnsMapping) {
        foreach ($columnsMapping as $requestName => $columnName) {
            if (isset($this->requestData[$requestName])) {
                $this->model->$columnName = $this->requestData[$requestName];
            }
        }
    }

    private function setModelForSave($new, $id = 0) {
        if ($new) {
            $this->model = new Example;
        } elseif (!$new && $id > 0) {
            $this->model = Example::find($id);
        } else {
            $this->model = Example::first();
        }
    }

    private function saveData($columnsMaping) {
        $this->setSaveData($columnsMaping);
        if ($this->model->save()) {
            $id = $this->model->id;
            //Add additional functions or code here
            $data = $this->model->find($id);
            $this->response = $this->handlingResponseItem($data);
            $this->response['status'] = true;
            $this->response['msg'] = "data saved";
        } else {
            $this->response = ['status' => false, 'msg' => 'An error has been occured'];
        }
    }

}
