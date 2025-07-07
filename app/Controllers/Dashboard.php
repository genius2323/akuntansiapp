<?php namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'page_heading' => 'Ringkasan Penjualan'
        ];
        
        return view('dashboard_view', $data);
    }

}