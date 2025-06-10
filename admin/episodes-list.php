<?php
require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
require_once __DIR__ . '/ga4-report.php';

class SPP_Episodes_Table extends WP_List_Table {

  function prepare_items() {
    $query = new WP_Query(['post_type'=>'spp_episode','posts_per_page'=>-1]);
    $data  = [];
    foreach ($query->posts as $p) {
      $url = get_post_meta($p->ID,'spp_audio_url',true);
      $ga  = spp_ga4_totals($url);
      $data[] = [
        'ID'        => $p->ID,
        'title'     => $p->post_title,
        'duration'  => $ga['duration'] ?? '—',
        'plays'     => $ga['plays'] ?? 0,
        'complete'  => $ga['complete'] ?? 0,
        'download'  => $ga['download'] ?? 0,
        'shortcode' => '[podcast_player id="'.$p->ID.'"]',
      ];
    }
    $this->items = $data;
    $this->_column_headers = [['title','Plays','Complete','Download','Shortcode'],[],[]];
  }

  function column_default($item,$col){
    return $item[$col] ?? '';
  }

  function column_title($item){
    return '<strong>'.$item['title'].'</strong>';
  }
}

/* Add submenu */
add_action('admin_menu', function(){
  add_submenu_page('edit.php?post_type=spp_episode','Episode Analytics','Analytics','manage_options',
                   'spp-analytics','spp_render_table');
});
function spp_render_table(){
  echo '<div class="wrap"><h1>Podcast Analytics</h1>';
  $table = new SPP_Episodes_Table();
  $table->prepare_items();
  $table->display();
  echo '</div>';
}
