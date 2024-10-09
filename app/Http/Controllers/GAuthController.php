<?php

namespace App\Http\Controllers;

use App\Services\GoogleAPI;

use Illuminate\Http\Request;

class GAuthController extends Controller
{
    public function gAuth(GoogleAPI $googleAPI)
    {
        return redirect($googleAPI->createAuthUrl());
    }

    public function gAuthPostBack(Request $request)
    {
        return redirect('/#/g-auth-postback?code=' . $request->get('code'));
    }
}
