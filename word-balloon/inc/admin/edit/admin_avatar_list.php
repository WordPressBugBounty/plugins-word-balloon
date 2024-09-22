<?php
defined( 'ABSPATH' ) || exit;




function word_balloon_admin_avatar_list($w_b_ListTable) {
  ?>

  <div class="w_b_avatar_list_wrapper w_b_box_design">
    <span class="w_b_headding_text"><?php esc_html_e('Avatars LIST','word-balloon'); ?> <span style="font-size:12px;">(<?php esc_html_e('Up to five avatars','word-balloon'); ?>)</span></span>
    <?php
    $total_items = $w_b_ListTable->prepare_items();
    if($total_items >= 5 ){
      ?>
      <label for="w_b_menu_tab_word_balloon_pro" class="" style="color: #0073aa;" ><?php esc_html_e('need more avatar ?','word-balloon'); ?></label>
    <?php } ?>
    <hr>
    <?php


    ?>
    <div class="wrap">
      <form id="avatars-filter" method="post" action="<?php print esc_url( wp_nonce_url( admin_url('options-general.php?page=word-balloon') , 'w_b_nonce_field_action', 'w_b_nonce_name' ) );?>">
        <?php wp_nonce_field( 'w_b_nonce_field_action',"w_b_nonce_name" ); ?>

        <?php $w_b_ListTable->display(); ?>


      </form>
    </div>
  </div>

  <?php
}
