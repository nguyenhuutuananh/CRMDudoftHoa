<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Images_email extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('email_marketing_model');
    }

    public function index()
    {

    }
    public function images_code()
    {
        $id=$this->input->get('id');
        $this->email_marketing_model->update_status($id);
        header("Content-Type: image/png");
        $im = @imagecreate(110, 20)
        or die("Cannot Initialize new GD image stream");
        $background_color = imagecolorallocate($im, 0, 0, 0);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
        imagepng($im);
        imagedestroy($im);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Disposition: attachment; filename="photos_icon.png"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $im);
        readfile($im);
        exit;
    }
}
?>