<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Receipts extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('receipts_model');
    }
    

    public function index()
    {
        var_dump(expression);die();
    }
    
}