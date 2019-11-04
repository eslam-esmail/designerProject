<?php

namespace OlaHub\DesignerCorner\DesginerItems\Handlers;

use OlaHub\DesignerCorner\DesginerItems\Models\DesginerItems;
use League\Fractal;
use Illuminate\Http\Request;

class DesginerItemsHandler extends Fractal\TransformerAbstract {

    private $return;
    private $data;
    private $request;

    public function transform(DesginerItems $data) {
        $this->request = Request::capture();
        $this->data = $data;
        $this->setDefaultData();
        $this->setCartData();
        return $this->return;
    }

    private function setDefaultData() {
        $user = app('session')->get('tempID') ? \OlaHub\DesignerCorner\Additional\Models\UserMongo::where('user_id', app('session')->get('tempID'))->first() : false;
        $itemPrice = DesginerItems::checkPrice($this->data);
        $this->return = [
            "productID" => isset($this->data->id) ? $this->data->id : 0,
            "productRealID" => isset($this->data->item_id) ? $this->data->item_id : 0,
            "productSlug" => isset($this->data->item_slug) ? $this->data->item_slug : null,
            "productName" => isset($this->data->item_title) ? $this->data->item_title : null,
            "productDescription" => isset($this->data->item_description) ? $this->data->item_description : null,
            "productInStock" => isset($this->data->item_stock) ? $this->data->item_stock : 0,
            "productPrice" => isset($this->data->item_price) ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_price, true) : 0,
            "productOwner" => isset($this->data->designer_id) ? $this->data->designer_id : 0,
            "productOwnerName" => isset($this->data->designer_name) ? $this->data->designer_name : 0,
            "productOwnerSlug" => isset($this->data->designer_slug) ? $this->data->designer_slug : 0,
            "productOwnerFollowers" => isset($this->data->designer_id) ? \OlaHub\DesignerCorner\Additional\Models\UserMongo::whereIn("followed_designers", [(int) $this->data->designer_id, (string)$this->data->designer_id])->count() : 0,
            "number" => isset($this->data->_id) ? $this->data->_id : 0,
            "productSlug" => \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkSlug($this->data, 'item_slug', $this->data->item_title),
            "productName" => isset($this->data->item_title) ? $this->data->item_title : NULL,
            "productDescription" => isset($this->data->item_description) ? $this->data->item_description : NULL,
            "productInStock" => isset($this->data->item_stock) ? (int) $this->data->item_stock : NULL,
            "productShowLabel" => true,
            "followed" => $user && isset($user->followed_designers) && is_array($user->followed_designers) && in_array($this->data->id, $user->followed_designers) ? true : false
        ];
        $this->setPriceData();
        $this->setImageData();
        $this->setRateData();
        $this->setShippingDatesData();
        $this->setFollowStatus();
    }

    private function setPriceData() {
        if ($this->data->discount_end_date && $this->data->discount_end_date >= date("Y-m-d")) {
            $this->return["productOriginalPrice"] = $this->data->item_original_price ? \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_original_price, true): 0;
            $this->return["productWillSavePerc"] = ceil(((\OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_original_price, false) - \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_price, false)) / \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_original_price, false)) * 100);
            $this->return["productWillSaveMount"] = ((\OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_original_price, false) - \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setDesignerPrice($this->data->item_price, false))) . " JOD";
        }
    }

    private function setImageData() {
        $this->return["productImages"] = [];
        if ($this->data->item_images && is_array($this->data->item_images) && count($this->data->item_images) > 0) {
            foreach ($this->data->item_images as $image) {
                $this->return["productImages"][] = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::setImageUrl($image);
            }
        } else {
            $this->return["productImages"][] = "/img/no_image.png";
        }

        $this->return["productImage"] = isset($this->return["productImages"][0]) ? $this->return["productImages"][0] : "/img/no_image.png";
    }

    private function setRateData() {
        $this->return["productRate"] = 0;
    }

    private function setShippingDatesData() {
        $dateFrom = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkHolidaysDatesNumber($this->data->item_min_shipping_days);
        $dateTo = \OlaHub\DesignerCorner\commonData\Helpers\CommonHelper::checkHolidaysDatesNumber($this->data->item_max_shipping_days);
        $this->return["shippingDateFrom"] = date("D d F, Y", strtotime("+$dateFrom Days"));
        if ($dateTo) {
            $this->return["shippingDateTo"] = date("D d F, Y", strtotime("+$dateTo Days"));
        }
    }

    private function setCartData() {
        $this->return['productInCart'] = 0;
        $itemID = $this->data->item_id;
        if (app('session')->get('tempID')) {
            $headerCelebration = $this->request->headers->get("celebration") ? $this->request->headers->get("celebration") : "";
            if ($headerCelebration && $headerCelebration > 0) {
                $this->checkCelebrationCart($headerCelebration, $itemID);
            } else {
                $this->checkDefaultCart($itemID);
            }
        } else {
            $this->checkNotLogeedCart();
        }
    }

    private function checkCelebrationCart($headerCelebration, $itemID) {
        $existInCelebration = FALSE;
        $existCelebration = true;
        $acceptParticipant = FALSE;
        $celebrationCart = \OlaHub\DesignerCorner\Additional\Models\Cart::withoutGlobalScope('countryUser')->where('celebration_id', $headerCelebration)->first();
        if ($celebrationCart) {
            $cartItem = \OlaHub\DesignerCorner\Additional\Models\CartItems::where('shopping_cart_id', $celebrationCart->id)->where('item_id', $itemID)->where("item_type", "designer")->first();
            if ($cartItem) {
                $existInCelebration = true;
                $this->return['productInCart'] = 1;
            }
            $participant = \OlaHub\DesignerCorner\Additional\Models\CelebrationParticipant::where('celebration_id', $headerCelebration)->where('is_approved', 1)->where('user_id', app('session')->get('tempID'))->first();
            if ($participant) {
                $acceptParticipant = TRUE;
            }
        } else {
            $existCelebration = false;
        }
        $this->return["existCelebration"] = $existCelebration;
        $this->return["existInCelebration"] = $existInCelebration;
        $this->return["acceptParticipant"] = $acceptParticipant;
    }

    private function checkDefaultCart($itemID) {
        $cartItem = \OlaHub\DesignerCorner\Additional\Models\Cart::whereNull("calendar_id")->whereHas('cartDetails', function ($q) use($itemID) {
                    $q->where('item_id', $itemID);
                    $q->where("item_type", "designer");
                })->count();
        if ($cartItem > 0) {
            $this->return['productInCart'] = 1;
        }
    }

    private function checkNotLogeedCart() {
        $cartCookie = $this->request->headers->get("cartCookie") ? json_decode($this->request->headers->get("cartCookie")) : [];
        if ($cartCookie && is_array($cartCookie) && count($cartCookie) > 0) {
            $id = $this->data->_id;
            foreach ($cartCookie as $item) {
                if ($id == $item->productId) {
                    $this->return['productInCart'] = 1;
                    return;
                }
            }
        }
    }

    private function setFollowStatus() {
        $this->return["productOwnerFollowed"] = 0;
        if (app('session')->get('tempID')) {
            $user = \OlaHub\DesignerCorner\Additional\Models\UserMongo::where("user_id", app('session')->get('tempID'))->first();
            if ($user) {
                $designers = $user->followed_designers && is_array($user->followed_designers) ? $user->followed_designers : [];
                if (in_array($this->data->designer_id, $designers)) {
                    $this->return["productOwnerFollowed"] = 1;
                }
            }
        }
    }

}
