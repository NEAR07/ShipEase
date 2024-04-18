<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function showForm($form)
    {
        return View::make("Multimedia.forms.$form")->render();
    }
}
