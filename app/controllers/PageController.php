<?php
namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller {
    public function about() {
        $current_page = 'about';
        $this->view('pages/about', compact('current_page'));
    }
    
    public function services() {
        $current_page = 'services';
        $this->view('pages/services', compact('current_page'));
    }
    
    public function portfolio() {
        $current_page = 'portfolio';
        $this->view('pages/portfolio', compact('current_page'));
    }
}





