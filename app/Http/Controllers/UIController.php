<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UIController extends Controller
{
    //
    public static function getIndexPage() {
        return view('main');
    }

    public static function getContactPage() {
        return view('content.contact');
    }

    public function getHelpPage() {
        return view('content.help');
    }

    public function getPrivacyPage() {
        return view('content.privacy');
    }

}
