<?php

namespace App\Http\Controllers\Foo\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Traits\Client\V1\ComplaintTrait;

class FooController extends Controller
{
    use ComplaintTrait;

    public function index()
    {
        return "Bar";
    }

    public function genTicketNumber(Request $request)
    {
        $code = $request->code;

        return $this->generateTicketNumber($code);
    }

}
