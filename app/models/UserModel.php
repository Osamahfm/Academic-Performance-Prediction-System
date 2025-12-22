<?php
namespace App\Models;

use App\Core\Model;

class UserModel extends Model {
    protected $table = 'users';
    
    public function findByEmail($email) {
        return $this->findOne(['email' => $email]);
    }
    
    public function createUser($name, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'status' => 'active'
        ]);
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function getActiveUsers() {
        return $this->findAll(['status' => 'active']);
    }
    
    public function getUserByRole($role) {
        return $this->findAll(['role' => $role]);
    }
}





