<?php namespace App\Controllers;

class PosController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'POS Dashboard',
            'page_heading' => 'POS Dashboard',
            'user' => session()->get()
        ];
        
        return view('dashboard_view', $data);
    }
}