<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UIController extends Controller
{
    //
    public static function getIndexPage() {
        return view('main');
    }

    public static function getAboutPage() {
        return view('content.about');
    }

    public function getHelpPage() {
        return view('content.help');
    }

}
