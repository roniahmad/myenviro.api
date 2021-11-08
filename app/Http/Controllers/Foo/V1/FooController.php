<?php

namespace App\Http\Controllers\Foo\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FooController extends Controller
{
    public function index()
    {
        return "Bar";
    }

}
