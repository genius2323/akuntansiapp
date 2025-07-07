<?php namespace App\Controllers;

class GeneralController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'General Dashboard',
            'page_heading' => 'General Dashboard',
            'user' => session()->get()
        ];
        
        return view('dashboard_view', $data);
    }
}