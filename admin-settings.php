<?php
/* Admin page */
add_action('admin_menu', function () {
  add_options_page('Podcast Player', 'Podcast Player', 'manage_options', 'spp-settings', 'spp_settings_page');
});

/* register + colour picker JS */
add_action('admin_init', function () {
  foreach (['spp_ga4_id','spp_default_art','spp_accent','spp_wave','spp_progress','spp_title','spp_subtitle'] as $opt){
      register_setting('spp_opts',$opt);
  }
  wp_enqueue_style ('wp-color-picker');
  wp_enqueue_script('spp-admin-col', plugins_url('assets/js/admin-color.js', __FILE__), ['wp-color-picker'], '1.0', true );
});

function spp_settings_page(){ ?>
<div class="wrap"><h1>Podcast Player Settings</h1>
<form method="post" action="options.php">
<?php settings_fields('spp_opts'); ?>
<table class="form-table">
<tr><th>GA4 Measurement ID</th>
<td><input name="spp_ga4_id" value="<?php echo esc_attr(get_option('spp_ga4_id','')); ?>" class="regular-text"></td></tr>

<?php
function colour_row($label,$opt,$def){
  printf('<tr><th>%1$s</th><td><input class="spp-col" name="%2$s" value="%3$s"></td></tr>',
    esc_html($label), esc_attr($opt), esc_attr(get_option($opt,$def)));
}
colour_row('Accent colour','spp_accent','#000');
colour_row('Wave colour','spp_wave','#c7c7c7');
colour_row('Progress colour','spp_progress','#1f6bff');
colour_row('Title colour','spp_title','#000');
colour_row('Subtitle colour','spp_subtitle','#666');
?>

<tr><th>Default artwork</th><td>
<?php $art=get_option('spp_default_art',''); ?>
<img id="spp_art_prev" src="<?php echo esc_url($art); ?>" style="max-width:120px">
<input type="hidden" id="spp_default_art" name="spp_default_art" value="<?php echo esc_attr($art); ?>">
<button type="button" class="button" id="spp_pick_art">Choose Image</button>
</td></tr>
</table>
<?php submit_button(); ?>
</form></div>
<?php }
