<?php

namespace App\Http\Controllers;

use App\Services\OdooService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OdooController extends Controller
{
    protected $odooService;

    public function __construct(OdooService $odooService)
    {
        $this->odooService = $odooService;
    }

    // public function index()
    // {
    //     $partners = $this->odooService->getPartners();
    //     return Inertia::render('Odoo', compact('partners'));
    // }

    // public function patners(){

    //     $partners = $this->odooService->getPartners();
    //     return response()->json($partners);
    // }

    public function guardar(Request $request){
        $nuevo =[
            'id' => rand(1000, 9999),
            'name' => $request->name,
        ];

        // guardar a la db

        return redirect()->back();
    }
}
