<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinancerController extends Controller
{
    public function financer_list(Request $request)
    {
        return view('financer.financerlist');
    }

    public function import_financer(Request $request)
    {
        return view('financer.importfinancer');
    }

    public function add_financer(Request $request)
    {
        return view('financer.addfinancer');
    }

    public function view_financer($id)
    {
        return view('financer.viewfinancer', ['id' => $id]);
    }

    public function edit_financer($id)
    {
        return view('financer.editfinancer', ['id' => $id]);
    }
}
