<?php

namespace src\classes\auth;

class User {
    private $id;
    private $email;
    private $role;

    public function __construct($id, $email, $role) {
        $this->id = $id;
        $this->email = $email;
        $this->role = $role;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function __sleep() {
        return ['id', 'email', 'role'];
    }

}

