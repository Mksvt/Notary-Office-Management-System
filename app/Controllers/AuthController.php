<?php
/**
 * Контролер авторизації
 */

require_once ROOT_PATH . '/app/Models/User.php';

class AuthController {
    public function loginForm() {
        if (isset($_SESSION['user_id'])) {
            redirect('/');
        }

        $title = 'Вхід до системи';
        $errors = [];
        $data = [];
        
        ob_start();
        include ROOT_PATH . '/app/Views/auth/login.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/auth.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $errors = [];

        if (empty($username)) {
            $errors['username'] = 'Введіть логін';
        }

        if (empty($password)) {
            $errors['password'] = 'Введіть пароль';
        }

        if (empty($errors)) {
            $userModel = new User();
            $user = $userModel->findByUsername($username);

            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['related_id'] = $user['related_id'];

                setFlashMessage('Ви успішно увійшли до системи', 'success');
                redirect('/');
            } else {
                $errors['general'] = 'Невірний логін або пароль';
            }
        }

        // Відобразити форму з помилками
        $title = 'Вхід до системи';
        $data = ['username' => $username];
        
        ob_start();
        include ROOT_PATH . '/app/Views/auth/login.php';
        $content = ob_get_clean();
        include ROOT_PATH . '/app/Views/layouts/auth.php';
    }

    public function logout() {
        session_destroy();
        redirect('/login');
    }
}
