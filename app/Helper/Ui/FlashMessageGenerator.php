<?php

namespace App\Helper\Ui;

use Illuminate\Support\Facades\Session;


class FlashMessageGenerator
{
    public static function generate($class, $message)
    {
        Session::flash('message', $message);
        Session::flash('class_name', $class);
    }
}
