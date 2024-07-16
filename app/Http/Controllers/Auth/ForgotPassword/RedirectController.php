<?php

namespace App\Http\Controllers\Auth\ForgotPassword;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function __invoke($token, Request $request)
    {
        if (strpos($request->userAgent(), "Mobile")) {
            $domainFELogin = config("frontend.app.domain") . config("frontend.app.paths.forgot_password");
        } else {
            $domainFELogin = config("frontend.web.domain") . config("frontend.web.paths.forgot_password");
        }
        return redirect()->away($domainFELogin . "?token=" . $token);
    }
}
