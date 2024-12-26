<?php

namespace App\Http\Controllers;

use App\Services\ServicioOdoo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OdooController extends Controller
{
    protected $servicioOdoo;

    public function __construct(ServicioOdoo $servicioOdoo)
    {
        $this->servicioOdoo = $servicioOdoo;
    }

    // public function index()
    // {
    //     $partners = $this->servicioOdoo->getPartners();
    //     return Inertia::render('Odoo', compact('partners'));
    // }

    // public function patners(){

    //     $partners = $this->servicioOdoo->getPartners();
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
