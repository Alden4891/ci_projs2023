<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class viewer extends CI_Controller {

  public function index() {
    // Get the document filename from the query string
    $filename = $this->input->get('filename');
    // $filename  = $fn;
    $filename = site_url('uploads'.'/'.$filename);
    print($filename);
    
    // return;
    // Set the MIME type based on the file extension
    $mime_types = array(
      'pdf' => 'application/pdf',
      'doc' => 'application/msword',
      'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'xls' => 'application/vnd.ms-excel',
      'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'ppt' => 'application/vnd.ms-powerpoint',
      'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    );
    $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
    $mime_type = isset($mime_types[$file_extension]) ? $mime_types[$file_extension] : '';

    if ($mime_type) {
      // Set the response headers to disable caching
      $this->output->set_header('Expires: 0');
      $this->output->set_header('Cache-Control: must-revalidate');
      $this->output->set_header('Pragma: public');
  
      // Output the document with the appropriate MIME type
      $this->output->set_content_type($mime_type);
      $this->output->set_header('Content-Disposition: inline; filename="'.$filename.'"');
      $this->output->set_output(file_get_contents($filename));
    } else {
      // Display an error message if the file type is not supported
      echo 'Unsupported file type';
    }
  }

}
?>
