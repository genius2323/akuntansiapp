<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        // Check department access
        if (!empty($arguments)) {
            $allowedDepartments = $arguments;
            $userDepartment = session()->get('department_id');
            if (!in_array($userDepartment, $allowedDepartments)) {
                // Redirect ke login jika tidak punya akses, hindari loop
                return redirect()->to('/login')->with('error', 'Unauthorized access');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}