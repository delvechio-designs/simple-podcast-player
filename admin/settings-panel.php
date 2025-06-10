<?php
/*────────────  Add “Settings → Podcast Player”  ────────────*/
add_action( 'admin_menu', function () {
    add_options_page(
        'Podcast Player Settings',
        'Podcast Player',
        'manage_options',
        'spp-settings',
        'spp_settings_page'
    );
});

/*────────────  Register options & enqueue assets  ────────────*/
add_action( 'admin_init', function () {

    foreach ([
        'spp_ga4_id',
        'spp_default_art',
        'spp_accent',
        'spp_wave',
        'spp_progress',
        'spp_title',
        'spp_subtitle'
    ] as $opt) {
        register_setting( 'spp_opts', $opt );
    }

    /* WP colour picker + small admin css + JS */
    wp_enqueue_style ( 'wp-color-picker' );
    wp_enqueue_style ( 'spp-admin', plugins_url( 'assets/css/admin.css', dirname(__FILE__,2) ), [], '1.0' );
    wp_enqueue_script( 'spp-admin-color', plugins_url( 'assets/js/admin-color.js', dirname(__FILE__,2) ),
                       [ 'wp-color-picker' ], '1.0', true );
});

/*────────────  Settings page markup  ────────────*/
function spp_settings_page() {
?>
<div class="wrap spp-settings">
  <h1>Podcast Player Settings</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'spp_opts' ); ?>
    <table class="form-table" role="presentation">

      <tr>
        <th scope="row"><label for="spp_ga4_id">GA4 Measurement&nbsp;ID</label></th>
        <td>
          <input name="spp_ga4_id" id="spp_ga4_id"
                 value="<?php echo esc_attr( get_option('spp_ga4_id','') ); ?>"
                 class="regular-text" placeholder="G-XXXXXXX">
        </td>
      </tr>

      <tr><th>Accent colour</th>
          <td><input type="text" class="spp-col" name="spp_accent"
                     value="<?php echo esc_attr( get_option('spp_accent','#000') ); ?>"></td></tr>

      <tr><th>Waveform base colour</th>
          <td><input type="text" class="spp-col" name="spp_wave"
                     value="<?php echo esc_attr( get_option('spp_wave','#c7c7c7') ); ?>"></td></tr>

      <tr><th>Progress colour</th>
          <td><input type="text" class="spp-col" name="spp_progress"
                     value="<?php echo esc_attr( get_option('spp_progress','#1f6bff') ); ?>"></td></tr>

      <tr><th>Title colour</th>
          <td><input type="text" class="spp-col" name="spp_title"
                     value="<?php echo esc_attr( get_option('spp_title','#000') ); ?>"></td></tr>

      <tr><th>Subtitle colour</th>
          <td><input type="text" class="spp-col" name="spp_subtitle"
                     value="<?php echo esc_attr( get_option('spp_subtitle','#666') ); ?>"></td></tr>

      <tr>
        <th>Default artwork</th>
        <td>
          <?php $art = get_option('spp_default_art',''); ?>
          <img id="spp_art_prev" src="<?php echo esc_url($art); ?>" style="max-width:120px;height:auto;">
          <input type="hidden" id="spp_default_art" name="spp_default_art" value="<?php echo esc_attr($art); ?>">
          <button type="button" class="button" id="spp_pick_art">Choose Image</button>
        </td>
      </tr>

    </table>
    <?php submit_button(); ?>
  </form>
</div>
<?php }
