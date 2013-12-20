<?php

class WPSPA_Meta {

    public static $meta_data = array(
        'description' => array(
          'field' => '_meta_description', 
          'label' => 'Meta Description',
          'input_type' => 'text',
          'style' => 'width:100%;',
          'maxlength' => '165'
        ),
        'title' => array(
          'field' => '_page_title',
          'label' => 'Page Title',
          'input_type' => 'text',
          'style' => 'width:100%;',
          'maxlength' => '65'
        ),
        'prefetch' => array(
            'field' => '_prefetch',
            'label' => 'Prefetch',
            'input_type' => 'checkbox',
            'style' => 'margin-left:3px;',
            'maxlength' => '1'
        )
    );

    public static function add_custom_box() {
        $post_types = array( 'post', 'page' );
        $post_types = apply_filters('WPSPA_meta_box_post_types', $post_types);

        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'WPSPA_sectionid',
                __( 'WPSPA Meta Data', 'WPSPA_textdomain' ),
                array(__CLASS__, 'render_custom_box'),
                $post_type
            );
        }
    }

    protected static function render_input_value_string($post_meta, $meta) {        
        $input_string = ' ';
        if ($meta['input_type'] == 'text') {
            $input_string .= 'value="'.esc_attr( $post_meta ).'"';
        } else if ($meta['input_type'] == 'checkbox') {
            if ((bool)$post_meta == true) {
                $input_string .= 'checked value="yes"';
            } else {
                $input_string .= ' value="yes"';
            }
        }
        return $input_string;
    }
    
    public static function render_custom_box( $post ) {

      // Add an nonce field so we can check for it later.
      wp_nonce_field( 'WPSPA_inner_custom_box', 'WPSPA_inner_custom_box_nonce' );
      
      foreach( self::$meta_data as $meta ) {
          $post_meta = get_post_meta( $post->ID, '_wpspa' . $meta['field'], true);
          echo '<p>';
          echo '<label for="WPSPA'.$meta['field'].'">';
          _e( $meta['label'], 'WPSPA_textdomain' );
          echo '<br />';
          echo '<input type="'.$meta['input_type'].'"' .
            ' id="WPSPA'.$meta['field'].'" name="WPSPA'.$meta['field'].'"' .
            WPSPA_Meta::render_input_value_string($post_meta, $meta).' ' .
            'style="'.$meta['style'].'"'.
            ' maxlength="'.$meta['maxlength'].'" />';
          echo '</label>';
          echo '</p>';
      }
    }

    public static function _save_post( $post_id ) {

      /*
       * We need to verify this came from the our screen and with proper authorization,
       * because save_post can be triggered at other times.
       */

      // Check if our nonce is set.
      if ( !isset( $_POST['WPSPA_inner_custom_box_nonce'] ) )
        return $post_id;

      $nonce = $_POST['WPSPA_inner_custom_box_nonce'];

      // Verify that the nonce is valid.
      if ( !wp_verify_nonce( $nonce, 'WPSPA_inner_custom_box' ) )
          return $post_id;

      // If this is an autosave, our form has not been submitted, so we don't want to do anything.
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return $post_id;

      // Check the user's permissions.
      if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
      } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
      }

      /* OK, its safe for us to save the data now. */
      foreach ( self::$meta_data as $meta ) {
          if (isset($_POST['WPSPA'.$meta['field']])) {
            $value = sanitize_text_field( $_POST['WPSPA'.$meta['field']] );
          } else {
            $value = "";
          }
          update_post_meta( $post_id, '_wpspa'.$meta['field'], $value );
      }
    }    
}

?>
