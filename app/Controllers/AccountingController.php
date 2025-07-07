<?php namespace App\Controllers;

class AccountingController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Accounting Dashboard',
            'page_heading' => 'Accounting Dashboard',
            'user' => session()->get()
        ];
        
        return view('dashboard_view', $data);
    }
}