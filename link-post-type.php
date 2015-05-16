<?php

/*
Plugin Name: Link Post Type
Description: Custom post types for links
Version: 0.1
Author: Tomi Novak
*/

/*  Copyright 2015  Tomi Novak (email : dev.tomi33@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class LinkPostType {

    function __construct() {
        add_action( 'init', array($this, 'register_link_post_type'));
        add_action( 'add_meta_boxes', array($this,'add_link_custom_fields' )); //calls the function in this class

        add_action( 'save_post', array($this,'save_link_url'));
    }

    function register_link_post_type() {
        register_post_type( 'Links',
            array(
                'labels' => array(
                    'name' => 'Links',
                    'singular_name' => 'Link'
                ),
                'public' => false,
                'has_archive' => true,
                'show_ui' => true,
                'show_in_admin_bar' => true,
                'menu_position' => 5,
                'register_meta_box_cb' => array($this,'add_link_custom_fields'),
                'supports' => array( 'title', 'editor', 'thumbnail' ),
                'taxonomies' => array('post_tag')
            )
        );
    }


    //Callback from register_post_type
    function add_link_custom_fields() {
        add_meta_box( 'meta_id', 'link_url_value', array($this, 'links_url_custom_field_display'), 'links', 'normal', 'high' );
    }

    //Display the contents of the custom meta box
    function links_url_custom_field_display(){
        wp_nonce_field( 'link_save', 'link_url_nonce' );
        $value = get_post_meta(get_the_ID(), 'link_url_value', true);
        echo '<label for="link_url">';
        echo 'URL for external link :';
        echo '</label> ';
        echo '<input type="text" id="link_url_field" name="link_url_value" value="' . esc_attr( $value ) . '" size="60" />';
    }

    //Save the meta value entered
    function save_link_url( $post_id ) {

        // Check if nonce is set
        if ( ! isset( $_POST['link_url_nonce'] ) ) {
            return $post_id;
        }

        if ( ! wp_verify_nonce( $_POST['link_url_nonce'], 'link_save' ) ) {
            return $post_id;
        }

        // Check that the logged in user has permission to edit this post
        if ( ! current_user_can( 'edit_post' ) ) {
            return $post_id;
        }

        $link_url_value = sanitize_text_field( $_POST['link_url_value'] );

        update_post_meta( $post_id, 'link_url_value', $link_url_value );
    }

}

$link_post_type = new LinkPostType();