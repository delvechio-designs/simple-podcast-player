<?php
/* Add “Settings → Podcast Player” */
add_action( 'admin_menu', function () {
    add_options_page(
        'Podcast Player Settings',
        'Podcast Player',
        'manage_options',
        'spp-settings',
        'spp_settings_page'
    );
});

/* Register all options + enqueue colour-picker */
add_action( 'admin_init', function () {
    register_setting( 'spp_opts', 'spp_ga4_id'      );
    register_setting( 'spp_opts', 'spp_default_art' );
    register_setting( 'spp_opts', 'spp_accent'      );
    register_setting( 'spp_opts', 'spp_wave'        );
    register_setting( 'spp_opts', 'spp_progress'    );

    wp_enqueue_style ( 'wp-color-picker' );
    wp_enqueue_script( 'spp-color', plugins_url( 'assets/admin-color.js', __FILE__ ),
                       [ 'wp-color-picker' ], '1.0', true );
});

/* Settings page markup */
function spp_settings_page() { ?>
<div class="wrap">
  <h1>Podcast Player Settings</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'spp_opts' ); ?>
    <table class="form-table">
      <tr>
        <th><label for="spp_ga4_id">GA4 Measurement ID</label></th>
        <td><input name="spp_ga4_id" id="spp_ga4_id" value="<?php echo esc_attr(get_option('spp_ga4_id','')); ?>" class="regular-text" placeholder="G-XXXXXXX"></td>
      </tr>

      <tr><th>Default artwork</th>
        <td>
          <?php $art = get_option('spp_default_art',''); ?>
          <img id="spp_art_prev" src="<?php echo esc_url($art); ?>" style="max-width:120px;height:auto;">
          <input type="hidden" id="spp_default_art" name="spp_default_art" value="<?php echo esc_attr($art); ?>">
          <button type="button" class="button" id="spp_pick_art">Choose Image</button>
        </td>
      </tr>

      <tr><th>Accent colour</th>   <td><input class="spp-col" name="spp_accent"   value="<?php echo esc_attr(get_option('spp_accent','#000')); ?>"></td></tr>
      <tr><th>Wave colour</th>     <td><input class="spp-col" name="spp_wave"     value="<?php echo esc_attr(get_option('spp_wave','#c7c7c7')); ?>"></td></tr>
      <tr><th>Progress colour</th> <td><input class="spp-col" name="spp_progress" value="<?php echo esc_attr(get_option('spp_progress','#1f6bff')); ?>"></td></tr>
    </table>
    <?php submit_button(); ?>
  </form>
</div>
<?php }
