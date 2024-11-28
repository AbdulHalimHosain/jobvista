<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController {
    protected $db;
    public function __construct() {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show the login page
     * 
     * @return void
     */
    public function login() {
        loadView('users/login');
    }
    public function create() {
        loadView('users/create');
    }

    /**
     * store user data in database
     * 
     * @return void
     */
    public function store() {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        $errors = [];

        //validation
        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if (!Validation::string($name, 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 characters';
        }

        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be between 6 and 50 characters';
        }
        if (!Validation::match($password, $passwordConfirmation)) {
            $errors['password_confirmation'] = 'Password does not match';
        }

        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors ?? [],
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state,
                ]
            ]);
            exit;
        } 

        // Check if email already exists
        $params = [
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();
        if($user){
            $errors['email'] = 'Email already exists';
            loadView('users/create', [
                'errors'=> $errors 
            ]);
            exit;
        }

        //create user account

        $params = [
            'name' => $name,
            'email'=> $email,
            'city'=> $city,
            'state'=> $state,
            'password'=> password_hash($password, PASSWORD_DEFAULT)

            ];

            $this->db->query('INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)', $params);

            // Get new user ID
            $userID = $this->db->conn->lastInsertId();

            // Set user session
            Session::set('user', [
                'id' => $userID,
                'name' => $name,
                'email' => $email,
                'city' => $city,
                'state' => $state
            ]);

            redirect('/');
}
    public function logout(){
        Session::clearAll('');

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

    redirect('/');
    }

    public function authenticate(){
        $email = $_POST['email'];
        $password = $_POST['password'];

        $errors =[];

        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email address';

        }
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        // Check if errors exists
        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors 
            ]);
            exit;
        }
        // check for email

        $params =[
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

        if (!$user) {
            $errors['email'] = 'Incorrect credentials';
            loadView('users/login', [
                'errors' => $errors 
            ]);
            exit;
        }

       // check if password is correct
        if (!password_verify($password, $user->password)) {
            $errors['password'] = 'Incorrect credentials';
            loadView('users/login', [
                'errors' => $errors 
            ]);
            exit;
    }

    // SET USER SESSION
        Session::set('user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'city' => $user->city,
            'state' => $user->state
        ]);

        redirect('/');
}
}