<?php
namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        $current_page = 'index';
        $this->view('home/index', compact('current_page'));
    }
}





