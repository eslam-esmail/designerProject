<?php

namespace OlaHub\DesignerCorner\DesginerItems\Helpers;

use OlaHub\DesignerCorner\commonData\Helpers\CommonHelper;
use Illuminate\Http\Request;

class DesginerItemsHelper extends CommonHelper {

    private $return;
    private $request;

    function getOneItemData($product, $slug, $requestFilter) {

        $user = app('session')->get('tempID') ? \OlaHub\DesignerCorner\Additional\Models\UserMongo::where('user_id', app('session')->get('tempID'))->first() : false;

        $this->return["productID"] = isset($product->item_id) ? $product->item_id : 0;
        $this->return["productSlug"] = isset($product->item_slug) ? $product->item_slug : null;
        $this->return["productName"] = isset($product->item_title) ? $product->item_title : null;
        $this->return["productDescription"] = isset($product->item_description) ? $product->item_description : null;
        $this->return["productInStock"] = isset($product->item_stock) ? $product->item_stock : 0;
        $this->return["productPrice"] = isset($product->item_price) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($product->item_price, true) : 0;
        $this->return["productOwner"] = isset($product->designer_id) ? $product->designer_id : 0;
        $this->return["productOwnerName"] = isset($product->designer_name) ? $product->designer_name : null;
        $this->return["productOwnerSlug"] = isset($product->designer_slug) ? $product->designer_slug : null;
        $this->return["number"] = isset($product->_id) ? $product->_id : 0;
        $this->return["productShowLabel"] = true;
        $this->return["followed"] = $user && isset($user->followed_designers) && is_array($user->followed_designers) && in_array($product->id, $user->followed_designers) ? true : false;
        $this->return["classifications"] = $this->setItemClassifications($product);
        $this->return["categories"] = $this->setItemParentCategory($product);
        $this->return["subCategories"] = $this->setItemSubCategory($product);
        $this->return["interests"] = $this->setItemInterests($product);
        $this->return["occasions"] = $this->setItemOccasions($product);

        $this->setPriceData($product);
        $this->setImageData($product);
        $this->setRateData($product);
        $this->setShippingDatesData($product);
        $this->setCartData($product);
        $item = false;
        
        
        if(isset($requestFilter['attributes']) && is_array($requestFilter['attributes']) && count($requestFilter['attributes']) > 0){
            foreach ($product->items as $one) {
                    $oneItem = (object) $one;
                    if (isset($oneItem->item_attr) && is_array($oneItem->item_attr) && $this->checkInArray($requestFilter['attributes'], $oneItem->item_attr)) {
                        $item = $oneItem;
                    }
                }
        } else {
            if ($product->item_slug != $slug) {

                foreach ($product->items as $one) {
                    $oneItem = (object) $one;
                    if (isset($oneItem->item_slug) && $oneItem->item_slug == $slug) {
                        $item = $oneItem;
                    }
                }
            }
        }
        if ($item) {
            $this->return["productID"] = isset($item->item_id) ? $item->item_id : 0;
            $this->return["productSlug"] = isset($item->item_slug) ? $item->item_slug : null;
            $this->return["productPrice"] = isset($item->item_price) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($item->item_price, true) : 0;
            $this->setPriceData($item);
            $this->setImageData($item);
            $this->setCartData($item);
        }

        return $this->return;
    }

    private function setPriceData($product) {
        if (isset($product->discount_end_date) && $product->discount_end_date >= date("Y-m-d")) {
            $this->return["productOriginalPrice"] = $product->item_original_price ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($product->item_original_price, true): 0;
            $this->return["productWillSavePerc"] = ceil(((\OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($product->item_original_price, false) - \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($product->item_price, false)) / \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($product->item_original_price, false)) * 100);
            $this->return["productWillSaveMount"] = ((\OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($product->item_original_price, false) - \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($product->item_price, false))) . " ". CommonHelper::getTranslatedCurrency("JOD");
        }
    }

    private function setImageData($product) {
        $this->return["productImages"] = [];
        $images = [];
        if(isset($product->item_images)){
            $images = $product->item_images;
        }elseif (isset($product->item_image)) {
            $images = $product->item_image;
        }
        //$images = isset($product->item_images) ? $product->item_images : isset($product->item_image) ? $product->item_image : [];
        if (isset($images) && $images && is_array($images) && count($images) > 0) {
            foreach ($images as $image) {
                $this->return["productImages"][] = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($image);
            }
        } else {
            $this->return["productImages"][] = "/img/no_image.png";
        }

        $this->return["productImage"] = isset($this->return["productImages"][0]) ? $this->return["productImages"][0] : "/img/no_image.png";
    }

    private function setRateData($product) {
        $this->return["productRate"] = 0;
    }

    private function setShippingDatesData($product) {
        $dateFrom = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkHolidaysDatesNumber($product->item_min_shipping_days);
        $dateTo = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkHolidaysDatesNumber($product->item_max_shipping_days);
        $this->return["shippingDateFrom"] = date("D d F, Y", strtotime("+$dateFrom Days"));
        if ($dateTo) {
            $this->return["shippingDateTo"] = date("D d F, Y", strtotime("+$dateTo Days"));
        }
    }

    private function setCartData($product) {
        $this->request = Request::capture();
        $this->return['productInCart'] = 0;

        $itemID = $product->item_id;

        $headerCelebration = $this->request->headers->get("celebration") ? $this->request->headers->get("celebration") : "";
        if ($headerCelebration && app('session')->get('tempID')) {
            $existInCelebration = FALSE;
            $existCelebration = TRUE;
            $acceptParticipant = FALSE;
            $celebrationCart = \OlaHub\DesignerCorner\Additional\Models\Cart::withoutGlobalScope('countryUser')->where('celebration_id', $headerCelebration)->first();
            if ($celebrationCart) {
                $cartItem = \OlaHub\DesignerCorner\Additional\Models\CartItems::where('shopping_cart_id', $celebrationCart->id)->where('item_id', $itemID)->where("item_type", "designer")->first();
                if ($cartItem) {
                    $existInCelebration = TRUE;
                }
            } else {
                $existCelebration = FALSE;
            }
            $participant = \OlaHub\DesignerCorner\Additional\Models\CelebrationParticipant::where('celebration_id', $headerCelebration)->where('is_approved', 1)->where('user_id', app('session')->get('tempID'))->first();
            if ($participant) {
                $acceptParticipant = TRUE;
            }
            $this->return["existCelebration"] = $existCelebration;
            $this->return["existInCelebration"] = $existInCelebration;
            $this->return["acceptParticipant"] = $acceptParticipant;
        }

        $cartCookie = $this->request->headers->get("cartCookie") ? json_decode($this->request->headers->get("cartCookie")) : [];

        if ($cartCookie && is_array($cartCookie) && count($cartCookie) > 0) {
            $id = $product->item_id;
            $itemsId = [];
            foreach ($cartCookie as $item) {
                array_push($itemsId, $item->productId);
            }
            if (in_array($id, $itemsId)) {
                $this->return['productInCart'] = 1;
            }
        } else {
            if (\OlaHub\DesignerCorner\Additional\Models\Cart::whereHas('cartDetails', function ($q) use($itemID) {
                        $q->where('item_id', $itemID);
                        $q->where("item_type", "designer");
                    })->count() > 0) {
                $this->return['productInCart'] = 1;
            }
        }
    }
    
    
    private function setItemClassifications($product) {
        $class = [];
        if(isset($product->item_classification_id) && $product->item_classification_id){
            $classification = \OlaHub\DesignerCorner\DesginerItems\Models\Classification::where('id', $product->item_classification_id)->first();
            if($classification){
               $class[] = [
                   "classificationId" => isset($classification->id) ? $classification->id : 0,
                   "classificationName" => isset($classification->name) ? CommonHelper::returnCurrentLangField($classification, "name") : null,
                   "classificationSlug" => isset($classification->class_slug) ? $classification->class_slug : null,
               ]; 
            }
        }
        return $class;
    }
    
    private function setItemParentCategory($product) {
        $cat = [];
        if(isset($product->item_parent_category_id) && $product->item_parent_category_id){
            $category = \OlaHub\DesignerCorner\DesginerItems\Models\ItemCategory::where('id', $product->item_parent_category_id)->first();
            if($category){
               $cat[] = [
                   "categoryId" => isset($category->id) ? $category->id : 0,
                   "categoryName" => isset($category->name) ? CommonHelper::returnCurrentLangField($category, "name") : null,
                   "categorySlug" => isset($category->category_slug) ? $category->category_slug : null,
               ]; 
            }
        }
        return $cat;
    }
    
    private function setItemSubCategory($product) {
        $cat = [];
        if(isset($product->item_sub_category_id) && $product->item_sub_category_id){
            $category = \OlaHub\DesignerCorner\DesginerItems\Models\ItemCategory::where('id', $product->item_sub_category_id)->first();
            if($category){
               $cat[] = [
                   "categoryId" => isset($category->id) ? $category->id : 0,
                   "categoryName" => isset($category->name) ? CommonHelper::returnCurrentLangField($category, "name") : null,
                   "categorySlug" => isset($category->category_slug) ? $category->category_slug : null,
               ]; 
            }
        }
        return $cat;
    }
    
    private function setItemInterests($product) {
        $interestData = [];
        if(isset($product->item_interest_id) && $product->item_interest_id){
            $interest = \OlaHub\DesignerCorner\Additional\Models\Interest::where('interest_id', (int) $product->item_interest_id)->first();
            if($interest){
               $interestData[] = [
                   "interestId" => isset($interest->interest_id) ? $interest->interest_id : 0,
                   "interestName" => isset($interest->name) ? CommonHelper::returnCurrentLangField($interest, "name") : null,
                   "interestSlug" => isset($interest->interest_slug) ? $interest->interest_slug : null,
               ]; 
            }
        }
        return $interestData;
    }
    
    private function setItemOccasions($product) {
        $occasionData = [];
        if(isset($product->item_occasion_ids) && count($product->item_occasion_ids) > 0){
            $occasions = \OlaHub\DesignerCorner\Occasions\Models\Occasions::whereIn('id', $product->item_occasion_ids)->get();
            if($occasions->count() > 0){
                foreach ($occasions as $occasion){
                    $occasionData[] = [
                        "occasionId" => isset($occasion->id) ? $occasion->id : 0,
                        "occasionName" => isset($occasion->name) ? CommonHelper::returnCurrentLangField($occasion, "name") : null,
                        "occasionSlug" => isset($occasion->occasion_slug) ? $occasion->occasion_slug : null,
                    ];
                }
            }
        }
        return $occasionData;
    }
    
    
    public function checkInArray($request, $array){
        $inArray = true;
        foreach ($request as $req){
            if(!in_array($req, $array)){
                $inArray = false;
                break;
            }
        }
        return $inArray;
    }

}
