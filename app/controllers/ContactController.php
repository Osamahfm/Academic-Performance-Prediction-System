<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Strategy\ContactValidationStrategy;
use App\Models\ContactModel;

class ContactController extends Controller {
    private $contactModel;
    
    public function __construct() {
        parent::__construct();
        $this->contactModel = new ContactModel();
    }
    
    public function index() {
        $message = '';
        $message_type = '';
        $current_page = 'contact';
        $errors = [];
        $form_data = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'subject' => $_POST['subject'] ?? '',
                'message' => $_POST['message'] ?? ''
            ];
            
            // Use validation strategy
            $strategy = new ContactValidationStrategy();
            
            if ($strategy->validate($data)) {
                // Sanitize data
                $validator = new \App\Core\Validator($data);
                $sanitized = [
                    'name' => $validator->sanitize('name'),
                    'email' => $validator->sanitize('email'),
                    'subject' => $validator->sanitize('subject'),
                    'message' => $validator->sanitize('message')
                ];
                
                try {
                    $this->contactModel->createMessage(
                        $sanitized['name'],
                        $sanitized['email'],
                        $sanitized['subject'],
                        $sanitized['message']
                    );
                    $message = 'Thank you for your message! We will get back to you soon.';
                    $message_type = 'success';
                    $form_data = []; // Clear form
                } catch (\Exception $e) {
                    $message = 'Sorry, there was an error saving your message. Please try again.';
                    $message_type = 'error';
                    $form_data = $data;
                }
            } else {
                $errors = $strategy->getErrors();
                $message = 'Please correct the errors below.';
                $message_type = 'error';
                $form_data = $data;
            }
        }
        
        $this->view('pages/contact', compact('current_page', 'message', 'message_type', 'errors', 'form_data'));
    }
}


