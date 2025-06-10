<?php
/* Meta box UI */
add_action('add_meta_boxes', function () {
  add_meta_box('spp_meta', 'Episode Details', 'spp_meta_box', 'spp_episode', 'normal', 'high');
});

function spp_meta_box($post) {
  $url = get_post_meta($post->ID, 'spp_audio_url', true);
  $sub = get_post_meta($post->ID, 'spp_subtitle', true);
  ?>
  <p>
    <label>MP3 URL<br>
      <input type="url" name="spp_audio_url" value="<?php echo esc_attr($url); ?>" style="width:100%" required>
    </label>
  </p>
  <p>
    <label>Subtitle<br>
      <input type="text" name="spp_subtitle" value="<?php echo esc_attr($sub); ?>" style="width:100%">
    </label>
  </p>
  <?php
}

/* Save */
add_action('save_post_spp_episode', function ($post_id) {
  if (isset($_POST['spp_audio_url']))  update_post_meta($post_id, 'spp_audio_url', esc_url_raw($_POST['spp_audio_url']));
  if (isset($_POST['spp_subtitle']))   update_post_meta($post_id, 'spp_subtitle', sanitize_text_field($_POST['spp_subtitle']));
});
