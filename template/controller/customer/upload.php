<?php

class ControllerClubUpload extends PT_Controller
{
    public function index() {
        $json = array();

        $this->load->language('club/project');
        
        # Make sure we have the correct directory
        if (isset($this->request->get['directory'])) {
            $directory = DIR_IMAGE . 'template/' . html_entity_decode($this->request->get['directory'], ENT_QUOTES, 'UTF-8') . '/';
        } else {
            $directory = DIR_IMAGE . 'template/';
        }

        //$directory = DIR_IMAGE . 'template/';

        # Check its a directory
        if (!is_dir($directory) || substr(str_replace('\\', '/', realpath($directory)), 0, strlen(DIR_IMAGE . 'template')) != DIR_IMAGE . 'template') {
            $json['error'] = $this->language->get('error_directory');
        }

        if (!$json) {
            # Check if multiple files are uploaded or just one
            $files = array();

            if (!empty($this->request->files['file']['name']) && is_array($this->request->files['file']['name'])) {
                foreach (array_keys($this->request->files['file']['name']) as $key) {
                    $files[] = array(
                        'name'  => $this->request->files['file']['name'][$key],
                        'type'  => $this->request->files['file']['type'][$key],
                        'tmp_name'  => $this->request->files['file']['tmp_name'][$key],
                        'error'  => $this->request->files['file']['error'][$key],
                        'size'  => $this->request->files['file']['size'][$key]
                    );
                }
            }

            foreach ($files as $file) {
                if (is_file($file['tmp_name'])) {
                    # Sanitize the filename
                    $filename = preg_replace('[/\\?%*:|"<>]', '', basename(html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8')));

                    # Validate the filename length
                    if ((utf8_strlen($filename) < 4) || (utf8_strlen($filename) > 255)) {
                        $json['error'] = $this->language->get('error_filename');
                    }

                    # Allow file extensions type
                    $allowed = array(
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                    );

                    if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
                        $json['error'] = $this->language->get('error_filetype');
                    }

                    # Allowed file mime types
                    $allowed = array(
                        'image/jpeg',
                        'image/pjpeg',
                        'image/png',
                        'image/x-png',
                        'image/gif'
                    );

                    if (!in_array($file['type'], $allowed)) {
                        $json['error'] = $this->language->get('error_filetype');
                    }

                    # Return any upload error
                    if ($file['error'] != UPLOAD_ERR_OK) {
                        $json['error'] = $this->language->get('error_upload_' . $file['error']);
                    }
                } else {
                    $json['error'] = $this->language->get('error_upload');
                }

                if (!$json) {
                    $thumb = HTTP_SERVER . 'image/template/' . $filename;
                    $target = 'template/' . $filename;

                    move_uploaded_file($file['tmp_name'], $directory . $filename);
                }
            }
        }

        if (!$json) {
            $json['success'] = $this->language->get('text_uploaded');
            $json['thumb'] = $thumb;
            $json['target'] = $target;
        }

        if(!$json) {
            //$json['thumb'] = 'template/' . $this->request->files['file']['name'];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
}
