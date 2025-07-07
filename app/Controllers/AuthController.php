<?php namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $departmentModel;

    public function __construct()
    {
         $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
        
        if (session_status() === PHP_SESSION_NONE) {
            session()->start();
        }
    }

    public function login()
    {
        if (session('logged_in') === true && is_numeric(session('department_id'))) {
            $redirectPath = $this->getDashboardPath((int)session('department_id'));
            return redirect()->to($redirectPath);
        }

        try {
            $data = [
                'departments' => $this->departmentModel
                    ->where('deleted_at', null)
                    ->orderBy('name', 'ASC')
                    ->findAll(),
                'validation' => \Config\Services::validation()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Department fetch error: ' . $e->getMessage());
            $data['departments'] = [];
        }

        return view('auth/login', $data);
    }

    public function authenticate()
    {
        log_message('debug', 'Login attempt: '.print_r($this->request->getPost(), true));

        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[3]',
            'department' => 'required|numeric|is_not_unique[departments.id]'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Validation errors: '.print_r($this->validator->getErrors(), true));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $credentials = [
            'username' => trim($this->request->getPost('username')),
            'password' => trim($this->request->getPost('password')),
            'department' => (int)$this->request->getPost('department')
        ];

        log_message('debug', 'Processing credentials for: '.$credentials['username']);

        $user = $this->userModel->verifyUser(
            $credentials['username'],
            $credentials['password'],
            $credentials['department']
        );

        if ($user === false) {
            log_message('error', 'Invalid credentials for: '.$credentials['username']);
            return redirect()->to('/login')
                ->withInput()
                ->with('error', 'Username/password salah atau departemen tidak sesuai');
        }

        session()->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'department_id' => $user['department_id'],
            'logged_in' => true
        ]);

        log_message('info', 'User logged in: '.$user['username']);
        return redirect()->to($this->getDashboardPath($user['department_id']));
    }

    private function getDashboardPath(int $departmentId): string
    {
        // === PERUBAHAN DI SINI ===
        // Hapus '/dashboard' agar mengarah ke method index() di setiap controller
        $routes = [
            1 => '/pos',
            2 => '/accounting',
            3 => '/general'
        ];

        if (!array_key_exists($departmentId, $routes)) {
            log_message('error', "Invalid department route requested: {$departmentId}");
            return '/dashboard'; // Fallback ke controller Dashboard utama
        }

        return $routes[$departmentId];
    }

    public function logout()
    {
        try {
            session()->destroy();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                config('App')->cookiePath,
                config('App')->cookieDomain,
                config('App')->cookieSecure,
                config('App')->cookieHTTPOnly
            );
        } catch (\Exception $e) {
            log_message('error', 'Logout error: ' . $e->getMessage());
        }

        return redirect()->to('/login');
    }
}