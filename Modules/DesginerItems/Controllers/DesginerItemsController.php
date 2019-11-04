<?php

namespace OlaHub\DesignerCorner\DesginerItems\Controllers;

use OlaHub\DesignerCorner\commonData\Controllers\CommonController as CommonController;
use OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems;

class DesginerItemsController extends CommonController {

    protected $requestSort;

    public function __construct(\Illuminate\Http\Request $request) {
        parent::__construct();
        $this->model = (new DesginerItems)->newQuery();
        $this->helper = new \OlaHub\DesignerCorner\DesginerItems\Helpers\DesginerItemsHelper();
        $requestFinal = $this->helper->returnRequestData($request);
        $this->requestData = isset($requestFinal["requestData"]) ? $requestFinal["requestData"] : [];
        $this->requestFilters = isset($requestFinal["requestFilter"]) ? $requestFinal["requestFilter"] : [];
        $this->requestSort = isset($requestFinal["requestSort"]) ? $requestFinal["requestSort"] : [];
        $this->responseHandler = "\OlaHub\DesignerCorner\DesginerItems\Handlers\DesginerItemsHandler";
        $this->response = ['status' => false, 'msg' => 'No data found'];
        $this->paginateCount = isset($this->requestFilters["paginateCount"]) && $this->requestFilters["paginateCount"] > 0 ? $this->requestFilters["paginateCount"] : PAGINATE_COUNT;
        $this->columnsMapping = isset(DesginerItems::$columnsMapping) ? DesginerItems::$columnsMapping : [];
    }

    function getPagination() {
        $this->handlingFilters();
        $this->sortItems();
        $data = $this->model->paginate($this->paginateCount);
        if ($data && $data->count() > 0) {
            $this->response = $this->handlingResponseCollectionPginate($data);
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

    public function listDesginerCountriesFilter() {
        $this->handlingFilters();
        $this->model->whereNotNull("designer_id")
                ->groupBy("designer_id");
        $desginerItems = $this->model->get();
        $designerIDs = [];

        if ($desginerItems->count() > 0) {
            foreach ($desginerItems as $desginerItem) {
                array_push($designerIDs, $desginerItem->designer_id);
            }
        }

        if (count($designerIDs)) {
            $desginers = \OlaHub\DesignerCorner\Additional\Models\Desginers::select("country_id")->whereIn('id', $designerIDs)->groupBy('country_id')->get();
            $countries = \OlaHub\DesignerCorner\Additional\Models\Country::whereIn('id', $desginers)->get();
            $this->response = $this->handlingResponseCollection($countries, '\OlaHub\DesignerCorner\DesginerItems\Handlers\DesginersCountriesHandler');
            $this->response['status'] = true;
            $this->response['code'] = 200;
        }
        return response($this->response, 200);
    }

    public function getDesginersFilter() {
        if (isset($this->requestFilters['desginers']) && is_array($this->requestFilters['desginers']) && count($this->requestFilters['desginers']) > 0) {
            unset($this->requestFilters['desginers']);
        }
        $this->handlingFilters();
        $itemData = $this->model->whereNotNull("designer_id")
                ->groupBy("designer_id")
                ->get();
        if ($itemData->count() > 0) {
            $designerIds = $this->getDesignersFromItems($itemData);
            $data = \OlaHub\DesignerCorner\Additional\Models\Desginers::whereIn("id", $designerIds)->get();
            $return = $this->handlingResponseCollection($data, '\OlaHub\DesignerCorner\DesginerItems\Handlers\DesginersHandler');
            $return['status'] = true;
            return response($return);
        }
        return response(['status' => false, 'msg' => 'An error has been occured']);
    }

    private function getDesignersFromItems($itemData) {
        $designerIds = [];
        foreach ($itemData as $item) {
            $designerIds[] = $item->designer_id;
        }
        return $designerIds;
    }

    public function getItemFiltersCatsData() {
        $this->handlingFilters();
        $this->model->whereNotNull("item_sub_category_id")
                ->whereNotNull("item_parent_category_id")
                ->groupBy("item_parent_category_id")
                ->groupBy("item_sub_category_id");
        $desginerItems = $this->model->get();
        $categoriesIds = [];
        $childsID = [];
        if ($desginerItems->count() > 0) {
            foreach ($desginerItems as $desginerItem) {
                array_push($categoriesIds, $desginerItem->item_parent_category_id);
                array_push($childsID, $desginerItem->item_sub_category_id);
            }
        }
        if (count($categoriesIds)) {
            $categories = \OlaHub\DesignerCorner\DesginerItems\Models\ItemCategory::whereIn('id', $categoriesIds)->groupBy('id')->get();
            $this->response = \OlaHub\DesignerCorner\DesginerItems\Models\ItemCategory::setReturnResponse($categories, $childsID);
            $this->response['status'] = true;
            $this->response['code'] = 200;
        }
        return response($this->response, 200);
    }

    public function getItemFiltersAttrsData() {
        $this->handlingFilters();
        $this->model->groupBy("all_attribute_values_ids");
        $desginerItems = $this->model->get(["all_attribute_ids", "all_attribute_values_ids"]);
        $attributesIds = [];
        $childsID = [];
        if ($desginerItems->count() > 0) {
            foreach ($desginerItems as $desginerItem) {

                foreach ($desginerItem->all_attribute_ids as $attr) {
                    array_push($attributesIds, $attr);
                }

                foreach ($desginerItem->all_attribute_values_ids as $childAttr) {
                    array_push($childsID, $childAttr);
                }
            }
        }
        if (count($attributesIds) && count($childsID)) {
            $attributeModel = (new \OlaHub\DesignerCorner\DesginerItems\Models\Attribute)->newQuery();
            $attributeModel->whereIn('id', $attributesIds);
            $attributes = $attributeModel->groupBy('id')->get();
            $this->response = \OlaHub\DesignerCorner\DesginerItems\Models\Attribute::setReturnResponse($attributes, $childsID);
            $this->response['status'] = true;
            $this->response['code'] = 200;
        }
        return response($this->response, 200);
    }

    public function getSelectedAttributes() {
        $attributeModel = (new \OlaHub\DesignerCorner\DesginerItems\Models\Attribute)->newQuery();
        $attributeModel->whereIn('id', $this->requestFilters['attributesParent']);
        $attributes = $attributeModel->groupBy('id')->get();
        if ($attributes->count() < 1) {
            throw new NotAcceptableHttpException(404);
        }

        $return['data'] = [];
        foreach ($attributes as $attribute) {
            $attrData = [
                "valueID" => isset($attribute->id) ? $attribute->id : 0,
                "valueName" => isset($attribute->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($attribute, 'name') : NULL,
                "valueColorStyle" => isset($attribute->is_color_style) ? $attribute->is_color_style : 0,
                "valueSizeStyle" => isset($attribute->is_size_style) ? $attribute->is_size_style : 0,
            ];

            $attrData['childsData'] = [];
            $childs = \OlaHub\DesignerCorner\DesginerItems\Models\AttrValue::where('product_attribute_id', $attribute->id)
                            ->whereIn("id", $this->requestFilters['attributesChildsId'])->get();
            foreach ($childs as $child) {
                $attrData['childsData'][] = [
                    "valueID" => isset($child->id) ? $child->id : 0,
                    "valueName" => isset($child->attribute_value) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($child, 'attribute_value') : NULL,
                    "valueHexColor" => isset($child->color_hex_code) ? $child->color_hex_code : NULL,
                ];
            }
            $return['data'][] = $attrData;
        }

        $return['status'] = true;
        $return['code'] = 200;
        return response($return, 200);
    }

    // one product
    public function getOneProductData($productSlug) {
        $data = $this->model->whereIn('item_slugs', [$productSlug])->first();
        if ($data) {
            //$this->response = $this->handlingResponseItem($data, 'OlaHub\DesignerCorner\DesginerItems\Handlers\DesginerItemsHandler');
            $this->response['data'] = (new \OlaHub\DesignerCorner\DesginerItems\Helpers\DesginerItemsHelper)->getOneItemData($data, $productSlug, $this->requestFilters);
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    public function getOneItemAttrsData($productSlug) {
        $data = $this->model->whereIn('item_slugs', [$productSlug])->first();
        if ($data) {
            $attributesIds = $data->all_attribute_ids;
            $attributeValuesIds = $data->all_attribute_values_ids;
            if (is_array($attributesIds) && is_array($attributeValuesIds) && count($attributeValuesIds) && count($attributesIds)) {
                $enabledAttrs = [];
                if ($data->items && count($data->items)) {
                    foreach ($data->items as $oneItem) {
                        $item = (object) $oneItem;
                        if (isset($item->item_attr)) {
                            if (isset($this->requestFilters['attributes']) && is_array($this->requestFilters['attributes']) && count($this->requestFilters['attributes']) > 0) {
                                if (isset($item->item_attr) && is_array($item->item_attr) && (new \OlaHub\DesignerCorner\DesginerItems\Helpers\DesginerItemsHelper)->checkInArray($this->requestFilters['attributes'], $item->item_attr)) {
                                    $this->response["currentAttributes"] = $item->item_attr;
                                    foreach ($item->item_attr as $oneAttr){
                                        $enabledAttrs[] = $oneAttr;
                                    }
                                }
                            } else {
                                if ($item->item_slug == $productSlug) {
                                    $this->response["currentAttributes"] = $item->item_attr;
                                }
                            }



//                            if (isset($oneItem->item_attr) && is_array($oneItem->item_attr) && $this->checkInArray($requestFilter['attributes'], $oneItem->item_attr)) {
//                                $item = $oneItem;
//                            }
//                            if($item["item_slug"] == $productSlug){
//                                $this->response["currentAttributes"] = $item["item_attr"];
//                            }
//                            if($this->requestFilters->attributes && in_array($this->requestFilters->attributes, $item["item_attr"])){
//                                array_push($enabledAttrs, $item["item_attr"]);
//                            }
                        }
                    }
                }
                $attributes = \OlaHub\DesignerCorner\DesginerItems\Models\Attribute::
                        whereIn('id', $attributesIds)
                        ->groupBy('id')
                        ->get();
                $this->response["allAttributes"] = $this->setAttributes($attributes, $attributeValuesIds, $enabledAttrs,$this->requestFilters['attributesParent']);

                $this->response['status'] = true;
                $this->response['msg'] = "data fetched";
            }
        }
        return response($this->response);
    }

    public function getOneItemRelatedItems($productSlug) {
        $model = (new DesginerItems)->newQuery();
        $currentItem = $model->where('item_slug', $productSlug)->first();
        $data = $this->model->where('designer_id', $currentItem->designer_id)->where('item_slug', '!=', $productSlug)->get();
        if ($data) {
            $this->response = $this->handlingResponseCollection($data, 'OlaHub\DesignerCorner\DesginerItems\Handlers\DesginerItemsHandler');
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }
        return response($this->response);
    }
    
    
    public function getOfferItems(){
        $this->handlingFilters();
        $this->sortItems();
        $data = $this->model->whereNotNull("discount_end_date")->where("discount_end_date", ">=", date("Y-m-d"))->paginate($this->paginateCount);
        if ($data && $data->count() > 0) {
            $this->response = $this->handlingResponseCollectionPginate($data);
            $this->response['status'] = true;
            $this->response['msg'] = "data fetched";
        }

        return response($this->response);
    }

    //private functions

    private function setAttributes($attributes, $attributeValuesIds, $enabledAttrs, $parentAttr) {
        $allAttributes = [];
        foreach ($attributes as $attribute) {
            $attrData = [
                "valueID" => isset($attribute->id) ? $attribute->id : 0,
                "valueName" => isset($attribute->name) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($attribute, 'name') : NULL,
                "valueColorStyle" => isset($attribute->is_color_style) ? $attribute->is_color_style : 0,
                "valueSizeStyle" => isset($attribute->is_size_style) ? $attribute->is_size_style : 0,
            ];

            $attrData['childsData'] = [];
            $childs = \OlaHub\DesignerCorner\DesginerItems\Models\AttrValue::where('product_attribute_id', $attribute->id)
                            ->whereIn("id", $attributeValuesIds)->get();
            foreach ($childs as $child) {
                $enabled = true;
                if(!in_array($attribute->id, $parentAttr)){
                    if(count($enabledAttrs) > 0 && !in_array($child->id, $enabledAttrs)){
                        $enabled = false;
                    }
                     
                }
                $attrData['childsData'][] = [
                    "valueID" => isset($child->id) ? $child->id : 0,
                    "valueName" => isset($child->attribute_value) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::returnCurrentLangField($child, 'attribute_value') : NULL,
                    "valueHexColor" => isset($child->color_hex_code) ? $child->color_hex_code : NULL,
                    "enabled" => $enabled
                ];
            }
            $allAttributes[] = $attrData;
        }
        return $allAttributes;
    }

    private function sortItems($column = 'created_at', $type = 'DESC') {
        if ($this->requestSort) {
            $order = explode('-', $this->requestSort);
            if (count($order) == 2 && isset($order[0]) && isset($order[1])) {
                switch ($order[0]) {
                    case 'create':
                        $column = 'created_at';
                        break;
                    case 'name':
                        $column = 'item_title';
                        break;
                    case 'price':
                        $column = 'item_price';
                        break;
                }
                if (in_array($order[1], ['asc', 'desc'])) {
                    $type = strtoupper($order[1]);
                }
            }
        }
        $this->model->orderBy($column, $type);
    }

    private function handlingFilters() {
        if (count($this->requestFilters) > 0) {
            if (isset($this->requestFilters['categories']) && is_array($this->requestFilters['categories']) && count($this->requestFilters['categories'])) {
                $this->model->whereIn('item_parent_category_id', $this->requestFilters['categories']);
            }

            if (isset($this->requestFilters['subCategories']) && is_array($this->requestFilters['subCategories']) && count($this->requestFilters['subCategories'])) {
                $this->model->whereIn('item_sub_category_id', $this->requestFilters['subCategories']);
            }

            if (isset($this->requestFilters['categorySlug']) && strlen($this->requestFilters['categorySlug']) > 0) {
                $this->model->where('item_parent_category_slug', $this->requestFilters['categorySlug']);
            }

            if (isset($this->requestFilters['classificationSlug']) && strlen($this->requestFilters['classificationSlug']) > 0) {
                $this->model->where('item_classification_slug', $this->requestFilters['classificationSlug']);
            }

            if (isset($this->requestFilters['countries']) && is_array($this->requestFilters['countries']) && count($this->requestFilters['countries'])) {
                $designers = \OlaHub\DesignerCorner\Additional\Models\Desginers::whereIn("country_id", $this->requestFilters['countries'])->select("id")->get();
                if ($designers->count()) {
                    $designerIds = [];
                    foreach ($designers as $design) {
                        $designerIds[] = $design->id;
                    }
                    $this->model->whereIn('designer_id', $designerIds);
                }
            }

            if (isset($this->requestFilters['desginers']) && is_array($this->requestFilters['desginers']) && count($this->requestFilters['desginers'])) {
                $this->model->whereIn('designer_id', $this->requestFilters['desginers']);
            }

            if (isset($this->requestFilters['desginerSlug']) && strlen($this->requestFilters['desginerSlug']) > 0) {
                $this->model->where('designer_slug', $this->requestFilters['desginerSlug']);
            }

            if (isset($this->requestFilters['priceFrom']) && strlen($this->requestFilters['priceFrom']) > 0) {
                $priceFrom = $this->setDesignerFilterPrice($this->requestFilters['priceFrom']);
                $this->model->where('item_price', ">=", (double) $priceFrom);
            }

            if (isset($this->requestFilters['priceTo']) && strlen($this->requestFilters['priceTo']) > 0) {
                $priceTo = $this->setDesignerFilterPrice($this->requestFilters['priceTo']);
                $this->model->where('item_price', "<=", (double) $priceTo);
            }

            if (isset($this->requestFilters['occasionSlug']) && strlen($this->requestFilters['occasionSlug']) > 0) {
                $this->model->where('item_occasions_slugs', $this->requestFilters['occasionSlug']);
            }

            if (isset($this->requestFilters['attributesParent']) && is_array($this->requestFilters['attributesParent']) && count($this->requestFilters['attributesParent'])) {
                $this->model->whereIn('all_attribute_ids', $this->requestFilters['attributesParent']);
            }

            if (isset($this->requestFilters['attributes']) && is_array($this->requestFilters['attributes']) && count($this->requestFilters['attributes'])) {
                $this->model->whereIn('all_attribute_values_ids', $this->requestFilters['attributes']);
            }

            if (isset($this->requestFilters['interestSlug']) && strlen($this->requestFilters['interestSlug']) > 0) {
                $this->model->where('item_interest_slug', $this->requestFilters['interestSlug']);
            }
            
            if (isset($this->requestFilters['offerOnly']) && $this->requestFilters['offerOnly']) {
                $this->model->whereNotNull("discount_end_date")->where("discount_end_date", ">=", date("Y-m-d"));
            }
        }
    }
    
    
    private function setDesignerFilterPrice($itemPrice) {
        $price = (double) $itemPrice;
        $currency = config('def_currencyData');
        $exchangeRate = \DB::table("currencies_exchange_rates")->where("currency_to", $currency->code)->first();
        if ($exchangeRate) {
            $newPrice = $price / $exchangeRate->exchange_rate;
            $returnPrice = number_format($newPrice, 2);
        } else {
            $returnPrice = number_format($price, 2);
        }
        return $returnPrice;
    }

}
