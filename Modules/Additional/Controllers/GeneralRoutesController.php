<?php

namespace OlaHub\DesignerCorner\Additional\Controllers;

class GeneralRoutesController extends \OlaHub\DesignerCorner\commonData\Controllers\CommonController {

    protected $requestSort;
    protected $celebration = null;

    public function __construct(\Illuminate\Http\Request $request) {
        parent::__construct();
        $requestFinal = (new \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper)->returnRequestData($request);
        $this->requestData = isset($requestFinal["requestData"]) ? $requestFinal["requestData"] : [];
        $this->requestFilters = isset($requestFinal["requestFilter"]) ? $requestFinal["requestFilter"] : [];
        $this->requestSort = isset($requestFinal["requestSort"]) ? $requestFinal["requestSort"] : [];
        $this->response = ['status' => false, 'msg' => 'No data found'];
        $this->paginateCount = isset($this->requestFilters["paginateCount"]) && $this->requestFilters["paginateCount"] > 0 ? $this->requestFilters["paginateCount"] : PAGINATE_COUNT;
        if($request->headers->get('celebration') > 0){
            $this->celebration = \OlaHub\DesignerCorner\Additional\Models\Celebration::find($request->headers->get('celebration'));
        }
    }

    function getTrendingItems() {
        $data = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)->take(20)->skip(rand(0, 3))->get();
        if ($data && $data->count() > 0) {
            $this->response = $this->handlingResponseCollection($data, "OlaHub\DesignerCorner\Additional\Handlers\HomePageItemsHandler");
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getOffersItems() {
        $data = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)->take(20)
                ->whereNotNull("discount_end_date")
                ->where("discount_end_date", ">=", date("Y-m-d"))
                ->get();
        if ($data && $data->count() > 0) {
            $this->response = $this->handlingResponseCollection($data, "OlaHub\DesignerCorner\Additional\Handlers\HomePageItemsHandler");
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getClassesItems($classSlug) {
        $slug = str_replace("for-", "", $classSlug);
        $data = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)
                ->skip(rand(0, 3))
                ->take(20)
                ->where("item_classification_slug", $slug)
                ->get();
        if ($data && $data->count() > 0) {
            $this->response = $this->handlingResponseCollection($data, "OlaHub\DesignerCorner\Additional\Handlers\HomePageItemsHandler");
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getAllDesignersItems() {
        $itemData = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)
                ->whereNotNull("designer_id")
                ->groupBy("designer_id")
                ->get();
        if ($itemData->count() > 0) {
            $designerIds = $this->getDesignersFromItems($itemData);
            $data = \OlaHub\DesignerCorner\Additional\Models\Desginers::whereIn("id", $designerIds)->get();
            $this->response = $this->handlingResponseCollection($data, "OlaHub\DesignerCorner\Additional\Handlers\HomePageDesignersHandler");
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getOccasionsItems() {
        $itemData = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)
                ->whereNotNull("item_occasion_ids")
                ->groupBy("item_occasion_ids")
                ->get();
        if ($itemData->count() > 0) {
            $occassionIds = $this->getOccasionsFromItems($itemData);
            $data = \OlaHub\DesignerCorner\Occasions\Models\Occasions::whereIn("id", $occassionIds)->get();
            $this->response = $this->handlingResponseCollection($data, "OlaHub\DesignerCorner\Additional\Handlers\HomePageOccassionsHandler");
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getInterestsItems() {
        $itemData = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)
                ->whereNotNull("item_interest_id")
                ->groupBy("item_interest_id")
                ->get();
        if ($itemData->count() > 0) {
            $interestIds = $this->getInterestsFromItems($itemData);
            $data = \OlaHub\DesignerCorner\Additional\Models\Interest::whereIn("interest_id", $interestIds)->get();
            $this->response = $this->handlingResponseCollection($data, "OlaHub\DesignerCorner\Additional\Handlers\HomePageInterestsHandler");
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    function getCategoriesItems() {
        $itemData = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)
                ->whereNotNull("item_parent_category_id")
                ->groupBy("item_parent_category_id")
                ->get();
        if ($itemData->count() > 0) {
            $categoriesIds = [];
            $childsID = [];
            foreach ($itemData as $desginerItem) {
                array_push($categoriesIds, $desginerItem->item_parent_category_id);
                array_push($childsID, $desginerItem->item_sub_category_id);
            }

            if (count($categoriesIds) && count($childsID)) {
                $categories = \OlaHub\DesignerCorner\DesginerItems\Models\ItemCategory::whereIn('id', $categoriesIds)->get();
                $this->response = \OlaHub\DesignerCorner\DesginerItems\Models\ItemCategory::setReturnResponse($categories, $childsID);
                $this->response['status'] = true;
            }
            return response($this->response, 200);
        }
    }

    function getClassificationsItems() {
        $itemData = (new \OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems)
                ->whereNotNull("item_classification_id")
                ->groupBy("item_classification_id")
                ->get();
        if ($itemData->count() > 0) {
            $classIds = [];
            foreach ($itemData as $item) {
                array_push($classIds, $item->item_classification_id);
            }
            $data = \OlaHub\DesignerCorner\DesginerItems\Models\Classification::whereIn("id", $classIds)->get();
            $this->response = $this->handlingResponseCollection($data, "\OlaHub\DesignerCorner\DesginerItems\Handlers\ClassificationResponseHandler");
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }
    
    //Private Helper functions

    private function getDesignersFromItems($itemData) {
        $designerIds = [];
        foreach ($itemData as $item) {
            $designerIds[] = $item->designer_id;
        }
        return $designerIds;
    }

    private function getOccasionsFromItems($itemData) {
        $occassionIds = [];
        foreach ($itemData as $item) {
            $ids = $item->item_occasion_ids;
            if (count($ids) > 0) {
                foreach ($ids as $one) {
                    if (!in_array($one, $occassionIds)) {
                        $occassionIds[] = $one;
                    }
                }
            }
        }
        return $occassionIds;
    }

    private function getInterestsFromItems($itemData) {
        $occassionIds = [];
        foreach ($itemData as $item) {
            $ids = $item->item_interest_id;
            if (is_array($ids) && count($ids) > 0) {
                foreach ($ids as $one) {
                    if (!in_array($one, $occassionIds)) {
                        $occassionIds[] = (int)$one;
                    }
                }
            }elseif($ids > 0){
                $occassionIds[] = (int)$ids;
            }
        }
        return $occassionIds;
    }

}
