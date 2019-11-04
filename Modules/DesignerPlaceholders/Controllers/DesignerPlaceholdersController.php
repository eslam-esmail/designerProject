<?php

namespace OlaHub\DesignerCorner\DesignerPlaceholders\Controllers;

use OlaHub\DesignerCorner\commonData\Controllers\CommonController as CommonController;
use OlaHub\DesignerCorner\DesignerPlaceholders\Models\DesignerPlaceholders;

class DesignerPlaceholdersController extends CommonController {

    public function __construct(\Illuminate\Http\Request $request) {
        parent::__construct();
        $this->model = (new DesignerPlaceholders)->newQuery();
        $this->helper = new \OlaHub\DesignerCorner\DesignerPlaceholders\Helpers\DesignerPlaceholdersHelper();
        $requestFinal = $this->helper->returnRequestData($request);
        $this->requestData = isset($requestFinal["requestData"]) ? $requestFinal["requestData"] : [];
        $this->requestFilters = isset($requestFinal["requestFilter"]) ? $requestFinal["requestFilter"] : [];
        $this->responseHandler = "\OlaHub\DesignerCorner\DesignerPlaceholders\Handlers\DesignerPlaceholdersHandler";
        $this->response = ['status' => false, 'msg' => 'No data found'];
        $this->paginateCount = isset($this->requestFilters["paginateCount"]) && $this->requestFilters["paginateCount"] > 0 ? $this->requestFilters["paginateCount"] : PAGINATE_COUNT;
        $this->columnsMapping = isset(DesignerPlaceholders::$columnsMapping) ? DesignerPlaceholders::$columnsMapping : [];
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
            $this->model = new DesignerPlaceholders;
        } elseif (!$new && $id > 0) {
            $this->model = DesignerPlaceholders::find($id);
        } else {
            $this->model = DesignerPlaceholders::first();
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

    public function homepageADS() {
        $this->response = ['status' => false, 'msg' => 'An error has been occured'];
        $showFor = [];
        if (app("session")->get("tempID")) {
            $showFor = [2, 3];
        } else {
            $showFor = [1, 3];
        }
        
        if (app("session")->get("tempID")) {
            $placeholders = DesignerPlaceholders::whereIn('placeholder_show_for', [2, 3])->orderBy('type_order', 'ASC')->get();
        } else {
            $placeholders = DesignerPlaceholders::whereIn('placeholder_show_for', [1, 3])->orderBy('type_order', 'ASC')->get();
        }
        if ($placeholders) {
            $data = $this->handlingResponseCollection($placeholders, '\OlaHub\DesignerCorner\DesignerPlaceholders\Handlers\DesignerPlaceholdersHandler');
            return $data;
        }
        return response($this->response);
    }

}
