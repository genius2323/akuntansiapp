<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class DepartmentFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userDept = $session->get('department_id');
        
        if (!in_array($userDept, $arguments)) {
            // Redirect ke login jika tidak punya akses, hindari loop
            return redirect()->to('/login')->with('error', 'Akses tidak diizinkan untuk departemen ini');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}