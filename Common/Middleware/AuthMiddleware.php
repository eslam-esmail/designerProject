<?php

namespace OlaHub\DesignerCorner\commonData\Middlewares;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Closure;

class AuthMiddleware {

    private $userSession = false;
    private $user = false;
    private $secureHelper = false;
    private $agent;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $authHeader = $request->headers->get('authorization');
        if ($authHeader) {
            $this->checkUser($authHeader);
            if ($this->user && $this->user->is_active == 1) {
                $this->checkAgent($request);
                $id = $this->userSession->user_id;
                $code = $this->userSession->activation_code;
                $check = $this->secureHelper->matchTokenHash($authHeader, $this->agent, $id, $code);
                if ($check) {
                    app('session')->put('tempID', $this->userSession->user_id);
                    app('session')->put('tempData', $this->user);
                    app('session')->put('tempSession', $this->userSession);
                    return $next($request);
                }
            }
        }
        throw new UnauthorizedHttpException(401);
    }

    private function checkUser($authHeader) {
        $this->userSession = \OlaHub\DesignerCorner\Additional\Models\UserSession::where('hash_token', $authHeader)->first();
        if ($this->userSession) {
            $this->user = $this->userSession->user;
            $this->secureHelper = new \OlaHub\DesignerCorner\Additional\Helpers\SecureHelper();
        }
    }

    private function checkAgent($request) {
        if ($request->headers->get('uniquenum')) {
            $this->agent = $request->headers->get('uniquenum');
        } else {
            $this->agent = $request->headers->get('user-agent');
        }
    }

}
