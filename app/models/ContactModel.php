<?php
namespace App\Models;

use App\Core\Model;

class ContactModel extends Model {
    protected $table = 'contact_messages';
    
    public function createMessage($name, $email, $subject, $message) {
        return $this->create([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'status' => 'new'
        ]);
    }
    
    public function getUnreadMessages() {
        return $this->findAll(['status' => 'new'], 'created_at DESC');
    }
    
    public function markAsRead($id) {
        return $this->update($id, ['status' => 'read']);
    }
}









