<?php

add_action("wp_ajax_ms_req_form_file_data_ajax", "ms_req_form_file_data_ajax");
function ms_req_form_file_data_ajax(){

    $upload = 'err';

    if(!empty($_FILES['file'])){ 
        
        // File upload configuration        

        $fileName = basename($_FILES['file']['name']);

        $tmp_name = $_FILES['file']['tmp_name'];

        $wordpress_upload_dir = wp_upload_dir();
        
        $new_file_path = $wordpress_upload_dir['path'] . '/'. $fileName;

        if( move_uploaded_file( $tmp_name, $new_file_path ) ) {

            $upload_id = wp_insert_attachment( array(
                'guid'           => $new_file_path,
                'post_mime_type' => $_FILES['file']['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', $fileName ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ), $new_file_path );

            // wp_generate_attachment_metadata() won't work if you do not include this file
            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            // Generate and save the attachment metas into the database
            wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
            $upload = 'ok';
        }

    }

    if( $upload == 'err' ){
        wp_send_json_error( array('success' => false) );
    }else{
        wp_send_json( array('success' => true,'file_id' => $upload_id, 'file_name' => $fileName ) );
    }
}