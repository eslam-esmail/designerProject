<?php

namespace OlaHub\DesignerCorner\commonData\Helpers;

class CommonHelper {

    function returnRequestData($request) {
        $return['requestData'] = [];
        $return['requestFilter'] = [];
        $return['requestSort'] = [];
        if (REQUEST_DATA_TYPE == 'postMan') {
            $req = $request->all();
            $return['requestData'] = isset($req['data']) ? $req['data'] : [];
            $return['requestFilter'] = isset($req['filter']) ? $req['filter'] : [];
            $return['requestSort'] = isset($req['order']) ? $req['order'] : [];
        } else {
            $return['requestData'] = $request->json('data');
            $return['requestFilter'] = $request->json('filter');
            $return['requestSort'] = $request->json('order');
        }

        return $return;
    }

    static function returnCurrentLangField($objectData, $fieldName) {
        $return = "N/A";
        $languageArray = explode("_", config('def_lang'));
        $language = isset($languageArray[0]) ? strtolower($languageArray[0]) : "en";
        if (isset($objectData->$fieldName)) {
            $jsonData = json_decode($objectData->$fieldName);
            if (isset($jsonData->$language) && !empty($jsonData->$language)) {
                $return = $jsonData->$language;
            } else {
                $return = $objectData->$fieldName;
            }
        }
        return $return;
    }

    static function moveImage($fromPath, $toPath) {
        $return = false;
        $fileName = explode('/', $fromPath);
        $realPath = "../designersproject/temp_photos";
        $tempPath = $realPath . 'temp/';
        $newPath = $realPath . $toPath;
        if (!file_exists("$tempPath" . end($fileName))) {
            $return = false;
        }
        if (!file_exists($newPath)) {
            mkdir($newPath, 0777, true);
        }
        $moveFile = @rename("$tempPath" . end($fileName), "$newPath/" . end($fileName));
        if ($moveFile) {
            $return = $toPath . '/' . end($fileName);
        }
        return $return;
    }

    static function createSlugFromString($string, $delimiter = '-') {
        $return = $string;
        if ($string) {
            $return = str_replace(' ', '_', $string);
            $return = preg_replace("/[^|+-_a-zA-Z0-9\/]/", '', $return);
            $return = strtolower(trim($return, '-'));
            $return = preg_replace("/[\/_|+ -]+/", $delimiter, $return);
        }

        return $return;
    }

    static function setImageUrl($imageID, $type = "image") {
        $return = null;
        if (STORAGE_URL) {
            $defult_url = STORAGE_URL;
        } else {
            $defult_url = url();
        }
        if (strlen($imageID) > 4) {
            $imageID = str_replace("files", "files/", $imageID);
            $imageID = str_replace("temp_photos/", "", $imageID);
            if (strpos($imageID, "http") !== false || strpos($imageID, "https") !== false) {
                return $imageID;
            }
            $explodedData = explode('.', $imageID);
            $extension = end($explodedData);
            if (in_array(strtolower($extension), IMAGE_EXT)) {
                $return = $defult_url . "/$imageID";
            }
        }

        return $return;
    }

    static function convertStringToDate($string, $format = 'D d F, Y') {
        $return = $string;
        if ($string) {
            $time = strtotime($string);
            if ($time && $time > 0) {
                $return = date($format, $time);
            } else {
                $return = "N/A";
            }
        }
        return $return;
    }

    static function checkSlug($model, $column, $originalName, $delimiter = '-') {
        $return = NULL;
        if ($model) {
            if ($model->$column) {
                $return = $model->$column;
            } else {
                $slug = CommonHelper::createSlugFromString($originalName, $delimiter);
                $model->$column = $slug;
                $model->save();
                $return = $slug;
            }
        }
        return $return;
    }

    static function checkHolidaysDatesNumber($totalDays) {
        $returnDates = $totalDays;
        for ($i = 1; $i <= $totalDays; $i++) {
            $timeStamp = strtotime("+$i Days");
            $day = date("N", $timeStamp);
            if (in_array($day, WEEK_END_DATES)) {
                $returnDates++;
            }
        }
        return $returnDates;
    }

    static function setDesignerPrice($itemPrice, $withCurr = true) {
        $price = (double) $itemPrice;
        $currency = config('def_currencyData');
        $exchangeRate = \DB::table("currencies_exchange_rates")->where("currency_to", $currency->code)->first();
        if ($exchangeRate) {
            $newPrice = $price * $exchangeRate->exchange_rate;
            $returnPrice = number_format($newPrice, 2);
        } else {
            $returnPrice = number_format($price, 2);
        }

        if ($withCurr) {
            $returnCur = CommonHelper::getTranslatedCurrency($currency->code);
            return "$returnPrice $returnCur";
        }
        return $returnPrice;
    }
    
    static function getTranslatedCurrency($currency) {
        $languageArray = explode("_", config('def_lang'));
        $language = strtolower($languageArray[0]);
        if ($language == "ar") {
            return "د.أ.";
        } else {
            return $currency;
        }
    }

}
