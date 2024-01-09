<?php
require_once 'models/User.php';

class UserController
{
    private $model;

    public function __construct($conn)
    {
        $this->model = new User($conn);
    }

    public function getTrophies($username)
    {
        return $this->model->getTrophies($username);
    }
}
