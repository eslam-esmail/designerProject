<?php

namespace OlaHub\DesignerCorner\Additional\Helpers;

class SecureHelper {

    function matchOldPasswordHash($password, $hash) {
        $passHashed = hash('sha256', $password);
        if (strtoupper($passHashed) == $hash) {
            return true;
        }
        return false;
    }

    function matchPasswordHash($password, $hash) {
        $fullHashReplace = str_replace('OlaHubHashing:', '', $hash);
        $fullHashDecode = base64_decode($fullHashReplace);
        $fullHash = str_replace('OlaHubHashing:', '$2y$' . constant('CRYPT_COST') . '$', $fullHashDecode);
        $FirstStepEncrypt = $password;
        for ($i = 0; $i <= constant('CRYPT_COST'); $i++) {
            $FirstStepEncrypt = md5($FirstStepEncrypt);
        }
        return password_verify($FirstStepEncrypt, $fullHash);
    }

    function setPasswordHashing($password) {
        $options = [
            'cost' => constant('CRYPT_COST')
        ];
        $mdEncrypt = $password;
        for ($i = 0; $i <= constant('CRYPT_COST'); $i++) {
            $mdEncrypt = md5($mdEncrypt);
        }
        $bEncrypt = password_hash($mdEncrypt, PASSWORD_BCRYPT, $options);
        $tempPassReplace = str_replace('$2y$' . constant('CRYPT_COST') . '$', 'OlaHubHashing:', $bEncrypt);
        $tempPassEncoding = 'OlaHubHashing:' . base64_encode($tempPassReplace);
        return $tempPassEncoding;
    }

    function setTokenHashing($agent, $id, $code) {
        return $this->setPasswordHashing(serialize([
                    'agent' => $agent,
                    'id' => $id,
                    'code' => $code,
        ]));
    }

    function matchTokenHash($hashToken, $agent, $id, $code) {
        $data = serialize([
            'agent' => $agent,
            'id' => $id,
            'code' => $code,
        ]);
        return $this->matchPasswordHash($data, $hashToken);
    }

    function creatUniquePayToken($billNumber, $userID) {
        $time = md5(time() * time() * constant('CRYPT_COST'));
        $token = $billNumber . "_" . $time . "_" . $userID;
        for ($i = 0; $i <= (constant('CRYPT_COST') * constant('CRYPT_COST')); $i++) {
            $token = md5($token);
        }
        $token = mb_substr($token, 0, 15);
        return [$token, $time];
    }

    function verifyPayToken($billing, $createdToken) {
        $token = $billing->billing_number . "_" . $billing->bill_time . "_" . $billing->user_id;
        for ($i = 0; $i <= (constant('CRYPT_COST') * constant('CRYPT_COST')); $i++) {
            $token = md5($token);
        }
        $token = mb_substr($token, 0, 15);
        return ($token == $createdToken);
    }

}
