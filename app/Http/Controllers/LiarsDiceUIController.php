<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class LiarsDiceUIController extends Controller
{

    public function help(Request $request)
    {
        return view('liarsdice.help');
    }

}
