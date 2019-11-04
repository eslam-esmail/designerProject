<?php

namespace OlaHub\DesignerCorner\DesginerItems\Controllers;

use OlaHub\DesignerCorner\commonData\Controllers\CommonController as CommonController;

class FiltersMainDataController extends CommonController {
    
    protected $requestSort;
    
    public function __construct(\Illuminate\Http\Request $request) {
        parent::__construct();
        $this->helper = new \OlaHub\DesignerCorner\DesginerItems\Helpers\DesginerItemsHelper();
        $requestFinal = $this->helper->returnRequestData($request);
        $this->requestData = isset($requestFinal["requestData"]) ? $requestFinal["requestData"] : [];
        $this->requestFilters = isset($requestFinal["requestFilter"]) ? $requestFinal["requestFilter"] : [];
        $this->requestSort = isset($requestFinal["requestSort"]) ? $requestFinal["requestSort"] : [];
        $this->response = ['status' => false, 'msg' => 'No data found'];
    }

    function getMainDataDetails() {
        $this->setModel();
        $data = $this->model->first();
        if ($data) {
            $this->response = $this->handlingResponseItem($data);
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }
        return response($this->response);
    }
    
    //private functions
    
    private function setModel() {
        if(isset($this->requestFilters['categorySlug']) && strlen($this->requestFilters['categorySlug']) > 0){
            $this->responseHandler = "OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers\CategoryMainResponseHandler";
            $this->model = (new \OlaHub\DesignerCorner\Categories\Models\Categories)->newQuery()->where("category_slug", $this->requestFilters['categorySlug']);
        }elseif(isset($this->requestFilters['classificationSlug']) && strlen($this->requestFilters['classificationSlug']) > 0){
            $this->responseHandler = "OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers\ClassificationMainResponseHandler";
            $this->model = (new \OlaHub\DesignerCorner\DesginerItems\Models\Classification)->newQuery()->where("class_slug", $this->requestFilters['classificationSlug']);
        }elseif(isset($this->requestFilters['desginerSlug']) && strlen($this->requestFilters['desginerSlug']) > 0){
            $this->responseHandler = "OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers\DesignerMainResponseHandler";
            $this->model = (new \OlaHub\DesignerCorner\Additional\Models\Desginers)->newQuery()->where("designer_slug", $this->requestFilters['desginerSlug']);
        }elseif(isset($this->requestFilters['occasionSlug']) && strlen($this->requestFilters['occasionSlug']) > 0){
            $this->responseHandler = "OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers\OccasionMainResponseHandler";
            $this->model = (new \OlaHub\DesignerCorner\Occasions\Models\Occasions)->newQuery()->where("occasion_slug", $this->requestFilters['occasionSlug']);
        }elseif(isset($this->requestFilters['interestSlug']) && strlen($this->requestFilters['interestSlug']) > 0){
            $this->responseHandler = "OlaHub\DesignerCorner\DesginerItems\mainDetails\Handlers\InterestMainResponseHandler";
            $this->model = (new \OlaHub\DesignerCorner\Additional\Models\Interest)->newQuery()->where("interest_slug", $this->requestFilters['interestSlug']);
        }
    }
}
