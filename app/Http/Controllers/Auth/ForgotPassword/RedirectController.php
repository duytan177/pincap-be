<?php

namespace App\Http\Controllers\Auth\ForgotPassword;

use App\Http\Controllers\Controller;

class RedirectController extends Controller
{
    public function __invoke($token)
    {
        $redirectUrlFe = config("frontend.web.domain") .
            config("frontend.web.paths.forgot_password") .
            "?token=" . $token;
        return redirect()->away($redirectUrlFe);
    }
}
