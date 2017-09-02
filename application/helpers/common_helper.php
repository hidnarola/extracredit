<?php

/**
 * Common Helper
 * Common functions used
 * @author KU
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Common Send Email Function
 * @param string $to - To Email ID
 * @param string $template - Email Template file
 * @param Array $data - Data to be passed
 * @return boolean
 */
function send_email($to = '', $template = '', $data = []) {
    if (empty($to) || empty($template) || empty($data)) {
        return false;
    }
    $ci = &get_instance();
    $ci->load->library('email');

    $config['protocol'] = 'smtp';
    $config['smtp_host'] = 'ssl://smtp.gmail.com';
    $config['smtp_port'] = '465';
    $config['smtp_user'] = 'demo.narola@gmail.com';
    $config['smtp_pass'] = 'narola21';
    $config['charset'] = 'utf-8';
    $config['newline'] = "\r\n";
    $config['mailtype'] = 'html';
    $config['validation'] = TRUE;

    $ci->email->initialize($config);

    $ci->email->to($to);
    $ci->email->from('no-reply@extracredit.com');
    $ci->email->subject($data['subject']);
    $view = $ci->load->view('email_templates/' . $template, $data, TRUE);
//    echo $view;
//    exit;
    $ci->email->message($view);
    $ci->email->send();
}

/**
 * Print array/string.
 * @param array $data - data which is going to be printed
 * @param boolean $is_die - if set to true then excecution will stop after print. 
 */
function p($data, $is_die = false) {

    if (is_array($data)) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    } else {
        echo $data;
    }

    if ($is_die)
        die;
}

/**
 * Print last executed query
 * @param boolean $bool - if set to true then excecution will stop after print
 */
function qry($bool = false) {
    $CI = & get_instance();
    echo $CI->db->last_query();
    if ($bool)
        die;
}

/**
 * Return verfication code with check already exit or not for users table
 */
function verification_code() {
    $CI = & get_instance();
    for ($i = 0; $i < 1; $i++) {
        $verification_string = 'abcdefghijk123' . time();
        $verification_code = str_shuffle($verification_string);

        $check_code = $CI->users_model->check_verification_code($verification_code);
        if (sizeof($check_code) > 0) {
            $i--;
        } else {
            return $verification_code;
        }
    }
}

/**
 * Uploads image
 * @param string $image_name
 * @param string $image_path
 * @return array - Either name of the image if uploaded successfully or Array of errors if image is not uploaded successfully
 */
function upload_image($image_name, $image_path) {
    $CI = & get_instance();
    $extension = explode('/', $_FILES[$image_name]['type']);
    $randname = uniqid() . time() . '.' . end($extension);
    $config = array(
        'upload_path' => $image_path,
        'allowed_types' => "png|jpg|jpeg|gif",
        'max_size' => "2048",
        // 'max_height'      => "768",
        // 'max_width'       => "1024" ,
        'file_name' => $randname
    );
    //--Load the upload library
    $CI->load->library('upload');
    $CI->upload->initialize($config);
    if ($CI->upload->do_upload($image_name)) {
        $img_data = $CI->upload->data();
        $imgname = $img_data['file_name'];
    } else {
        $imgname = array('errors' => $CI->upload->display_errors());
    }
    return $imgname;
}

function upload_communication($image_name, $image_path) {
    $CI = & get_instance();
    $extension = explode('/', $_FILES[$image_name]['type']);
    $randname = uniqid() . time() . '.' . end($extension);
    $config = array(
        'upload_path' => $image_path,
        'allowed_types' => "png|jpg|jpeg|doc|docx|pdf",
        // 'max_height'      => "768",
        // 'max_width'       => "1024" ,
        'file_name' => $randname
    );
    //--Load the upload library
    $CI->load->library('upload');
    $CI->upload->initialize($config);
    if ($CI->upload->do_upload($image_name)) {
        $img_data = $CI->upload->data();
        $imgname = $img_data['file_name'];
    } else {
        $imgname = array('errors' => $CI->upload->display_errors());
    }
    return $imgname;
}

/**
 * Generated random password
 * @return generated password
 */
function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

/**
 * Set up configuration array for pagination
 * @return array - Configuration array for pagination
 */
function front_pagination() {
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';
    $config['first_link'] = 'First';
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li style="display:none"></li><li class="active"><a data-type="checked" style="background-color:#62a0b4;color:#ffffff; pointer-events: none;">';
    $config['cur_tag_close'] = '</a></li><li style="display:none"></li>';
    $config['prev_link'] = '&laquo;';
    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';
    $config['next_link'] = '&raquo;';
    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';
    $config['last_link'] = 'Last';
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';
    return $config;
}

/**
 * Resise image to specified dimensions
 * @param string $src - Source of image
 * @param string $dest - Destination of image
 * @param int $width - Width of image
 * @param int $height - Height of image
 */
function resize_image($src, $dest, $width, $height) {
    $CI = & get_instance();
    $CI->load->library('image_lib');
    $CI->image_lib->clear();
    $config['image_library'] = 'gd2';
    $config['source_image'] = $src;
    $config['maintain_ratio'] = FALSE;
    $config['width'] = $width;
    $config['height'] = $height;
    $config['new_image'] = $dest;
    $CI->image_lib->initialize($config);
    $CI->image_lib->resize();
}

/**
 * Returns file size in GB/MB or KB
 * @param int $bytes
 * @return string
 */
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

/**
 * Crops the image
 * @param int $source_x
 * @param int $source_y
 * @param int $width
 * @param int $height
 * @param string $image_name
 */
function crop_image($source_x, $source_y, $width, $height, $image_name) {
    ini_set('memory_limit', '500M');

    $output_path = CROP_FACES;
    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
    $filename = pathinfo($image_name, PATHINFO_FILENAME);


    if ($extension == 'jpg' || $extension == 'jpeg') {
        $image = imagecreatefromjpeg($image_name);
    } else if ($extension == 'png') {
        $image = imagecreatefrompng($image_name);
    }

    $new_image = imagecreatetruecolor($width, $height);
    imagecopy($new_image, $image, 0, 0, $source_x, $source_y, $width, $height);
    // Now $new_image has the portion cropped from the source and you can output or save it.
    if ($extension == 'jpg' || $extension == 'jpeg') {
        imagejpeg($new_image, $output_path . $filename . '.' . $extension);
    } else if ($extension == 'png') {
        imagepng($new_image, $output_path . $filename . '.' . $extension);
    }
}
