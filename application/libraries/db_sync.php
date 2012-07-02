<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Database sync library
 * @author Wachiraph C.
 */
class DB_Sync {
  private $CI;

  public function __construct(){
    if (defined('ENVIRONMENT'))
    {
      if (!(ENVIRONMENT === 'development' || ENVIRONMENT === 'testing' ))
      {
        redirect();
      }
    }
    $this->CI =& get_instance();
    define('BASE_URL', base_url());
    }

    function use_test_db($use = TRUE){
    if($use){
      $this->CI->db = $this->CI->load->database('local_unit_test', TRUE);
      $this->CI->config->load('mongo_db');
      $this->CI->config->set_item('mongo_testmode', TRUE);
    } else {
      //Switch to default db
    }
    return $this;
    }

    function remove_users(){
      $tables = array('package_users', 'page_user_data', 'sessions', 'user', 'user_apps', 'user_campaigns', 'user_companies', 'user_pages', 'page');
    foreach($tables as $table){
      if($this->CI->db->empty_table($table)) {
        echo "Emptied table : {$table}<br />";
      }
    }
    echo "Remove users successfully";
    }

  function mysql_reset(){
    $this->CI->load->dbforge();
    $this->drop_tables();
    $this->CI->load->library('migrate_lib');
    $this->CI->migrate_lib->latest(FALSE);
    $this->insert_test_data();
    echo "Database reset successfully";
  }

  function drop_tables(){
    $tables = $this->CI->db->list_tables();
    foreach ($tables as $table){
      if(strpos($table, $this->CI->db->dbprefix) === 0){
        $table = str_replace($this->CI->db->dbprefix,'',$table);
        if($table === 'sessions'){
          $this->CI->db->empty_table($table);
        } else if($table === 'migrations') {
          $this->CI->db->empty_table($table);
          $this->CI->db->insert($table, array('version' => 0));
        } else {
          if($this->CI->dbforge->drop_table($table)){
            echo "Dropped table : {$table}<br />";
          }
        }
      }
    }
  }

  function insert_test_data(){
    $app = array(
      array(
        'app_id' => 1,
        'app_name' => 'Friend Get Fans',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Page Only'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Friend Get Fans',
        'app_secret_key' => 'ad3d4f609ce1c21261f45d0a09effba4',
        'app_url' => 'https://apps.socialhappen.com/beta/fgf/profile.php?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/beta/fgf/platform.php?action=install&company_id={company_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_install_page_url' => 'https://apps.socialhappen.com/beta/fgf/platform.php?action=install_to_page&app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/beta/fgf/app_config.php?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.socialhappen.com/beta/fgf/images/app_image_16.png',
        'app_image' =>  'https://apps.socialhappen.com/beta/fgf/images/app_image_o.png',
        'app_facebook_api_key' => '202663143123531'
      ),
      array(
        'app_id' => 2,
        'app_name' => '[Local]Friend Get Fans',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Page Only'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Friend Get Fans',
        'app_secret_key' => 'ad3d4f609ce1c21261f45d0a09effba4',
        'app_url' => 'https://apps.localhost.com/fgf/profile.php?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/fgf/platform.php?action=install&company_id={company_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_install_page_url' => 'https://apps.localhost.com/fgf/platform.php?action=install_to_page&app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/fgf/app_config.php?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.localhost.com/fgf/images/app_image_16.png',
        'app_image' =>  'https://apps.localhost.com/fgf/images/app_image_o.png',
        'app_facebook_api_key' => '154899207922915'
      ),
      array(
        'app_id' => 3,
        'app_name' => 'SHApp',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'SocialHappen Application',
        'app_secret_key' => 'cd14463efa98e6ee00fde6ccd51a9f6d',
        'app_url' => 'https://apps.socialhappen.com/beta/shapp?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/beta/shapp/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.socialhappen.com/beta/shapp/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/beta/shapp/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.socialhappen.com/beta/shapp/images/app_image_16.png',
        'app_image' =>  'https://apps.socialhappen.com/beta/shapp/images/app_image_o.png',
        'app_facebook_api_key' => '177890852283217'
      ),
      array(
        'app_id' => 4,
        'app_name' => '[Local]SHApp',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'SocialHappen Application',
        'app_secret_key' => 'cd14463efa98e6ee00fde6ccd51a9f6d',
        'app_url' => 'https://apps.localhost.com/shapp?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/shapp/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.localhost.com/shapp/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/shapp/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.localhost.com/shapp/images/app_image_16.png',
        'app_image' =>  'https://apps.localhost.com/shapp/images/app_image_o.png',
        'app_facebook_api_key' => '204755022911798'
      ),
      array(
        'app_id' => 5,
        'app_name' => 'FeedVideo',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Video Feed',
        'app_secret_key' => '985a868ee8aec810d3a25a3367776ea7',
        'app_url' => 'https://apps.socialhappen.com/beta/feedv?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/beta/feedv/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.socialhappen.com/beta/feedv/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/beta/feedv/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.socialhappen.com/beta/feedv/images/app_image_16.png',
        'app_image' =>  'https://apps.socialhappen.com/beta/feedv/images/app_image_o.png',
        'app_facebook_api_key' => '203741749684542'
      ),
      array(
        'app_id' => 6,
        'app_name' => '[Local]FeedVideo',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Video Feed',
        'app_secret_key' => '430203a30d65ef835f1521d70fd4e9b5',
        'app_url' => 'https://apps.localhost.com/feedv?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/feedv/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.localhost.com/feedv/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/feedv/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.localhost.com/feedv/images/app_image_16.png',
        'app_image' =>  'https://apps.localhost.com/feedv/images/app_image_o.png',
        'app_facebook_api_key' => '253512681338518'
      ),
      array(
        'app_id' => 7,
        'app_name' => 'FeedRSS',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'RSS Feed',
        'app_secret_key' => '9c867d4b57a77c46a7e8ca3830d8fb8c',
        'app_url' => 'https://apps.socialhappen.com/beta/feedr?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/beta/feedr/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.socialhappen.com/beta/feedr/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/beta/feedr/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.socialhappen.com/beta/feedr/images/app_image_16.png',
        'app_image' =>  'https://apps.socialhappen.com/beta/feedr/images/app_image_o.png',
        'app_facebook_api_key' => '249927805038578'
      ),
      array(
        'app_id' => 8,
        'app_name' => '[Local]FeedRSS',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'RSS Feed',
        'app_secret_key' => '985a868ee8aec810d3a25a3367776ea7',
        'app_url' => 'https://apps.localhost.com/feedr?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/feedr/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.localhost.com/feedr/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/feedr/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.localhost.com/feedr/images/app_image_16.png',
        'app_image' =>  'https://apps.localhost.com/feedr/images/app_image_o.png',
        'app_facebook_api_key' => '123678481062231'
      ),
      array(
        'app_id' => 9,
        'app_name' => 'CMS',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Content management system',
        'app_secret_key' => 'c861264d7f42636eed41612c58cc950f',
        'app_url' => 'https://apps.socialhappen.com/beta/fbcms?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/beta/fbcms/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.socialhappen.com/beta/fbcms/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/beta/fbcms/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => '?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.socialhappen.com/beta/fbcms/images/app_image_16.png',
        'app_image' =>  'https://apps.socialhappen.com/beta/fbcms/images/app_image_o.png',
        'app_facebook_api_key' => '133912883383693'
      ),
      array(
        'app_id' => 10,
        'app_name' => '[Local]CMS',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Content management system',
        'app_secret_key' => '655ab6dc53febea644ff59fb55695be5',
        'app_url' => 'https://apps.localhost.com/fbcms?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/fbcms/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.localhost.com/fbcms/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/fbcms/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => '?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.localhost.com/fbcms/images/app_image_16.png',
        'app_image' =>  'https://apps.localhost.com/fbcms/images/app_image_o.png',
        'app_facebook_api_key' => '297378976960614'
      ),array(
        'app_id' => 11,
        'app_name' => 'ExclusiveClub',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Exclusive club',
        'app_secret_key' => '7d4b57e0d9c467a77ac88ca38368fb8c',
        'app_url' => 'https://apps.socialhappen.com/beta/exclub?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/beta/exclub/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.socialhappen.com/beta/exclub/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/beta/exclub/setting/dashboard/{app_install_id}/{user_id}/{app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.socialhappen.com/beta/exclub/images/app_image_16.png',
        'app_image' =>  'https://apps.socialhappen.com/beta/exclub/images/app_image_o.png',
        'app_facebook_api_key' => '232687860130389'
      ),
      array(
        'app_id' => 12,
        'app_name' => '[Local]ExclusiveClub',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Exclusive club',
        'app_secret_key' => 'ea7988ee810d77aec863a25a33675a86',
        'app_url' => 'https://apps.localhost.com/exclub?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/exclub/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.localhost.com/exclub/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/exclub/setting/dashboard/{app_install_id}/{user_id}/{app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.localhost.com/exclub/images/app_image_16.png',
        'app_image' =>  'https://apps.localhost.com/exclub/images/app_image_o.png',
        'app_facebook_api_key' => '302231066461728'
      ),
      array(
        'app_id' => 13,
        'app_name' => '[Local]Quiz',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Page Only'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Quiz Application',
        'app_secret_key' => 'b79ebca20151427227b940b3329fff4d',
        'app_url' => 'https://apps.localhost.com/quiz?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/quiz/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.localhost.com/quiz/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/quiz/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://socialhappen.dyndns.org/socialhappen/assets/images/default/app_icon.png',
        'app_image' =>  'https://socialhappen.dyndns.org/socialhappen/assets/images/default/app.png',
        'app_facebook_api_key' => '249809481763956'
      ),
      array(
        'app_id' => 14,
        'app_name' => 'Quiz',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Page Only'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Quiz Application',
        'app_secret_key' => 'b79ebca20151427227b940b3329fff4d',
        'app_url' => 'https://apps.socialhappen.com/quiz?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/quiz/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.socialhappen.com/quiz/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/quiz/sh/config?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://beta.socialhappen.com/assets/images/default/app_icon.png',
        'app_image' =>  'https://beta.socialhappen.com/assets/images/default/app.png',
        'app_facebook_api_key' => '323031654420635'
      ),
      array(
        'app_id' => 15,
        'app_name' => 'VoteApp',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Vote App - create vote feed and other details',
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
        'app_url' => 'https://apps.socialhappen.com/beta/voteapp?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.socialhappen.com/beta/voteapp/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.socialhappen.com/beta/voteapp/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.socialhappen.com/beta/voteapp/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.socialhappen.com/beta/voteapp/assets/images/app_image_16.png',
        'app_image' =>  'https://apps.socialhappen.com/beta/voteapp/assets/images/app_image_o.png',
        'app_facebook_api_key' => '109201202537135'
      ),
      array(
        'app_id' => 16,
        'app_name' => '[Local]VoteApp',
        'app_type_id' => $this->CI->socialhappen->get_k('app_type','Support Page'),
        'app_maintainance' => 0,
        'app_show_in_list' => 1,
        'app_description' => 'Vote App - create vote feed and other details',
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
        'app_url' => 'https://apps.localhost.com/voteapp?app_install_id={app_install_id}',
        'app_install_url' => 'https://apps.localhost.com/voteapp/sh/install?company_id={company_id}&user_id={user_id}&page_id={page_id}',
        'app_install_page_url' => 'https://apps.localhost.com/voteapp/sh/install_page?app_install_id={app_install_id}&user_id={user_id}&page_id={page_id}&force=1',
        'app_config_url' => 'https://apps.localhost.com/voteapp/admin/?app_install_id={app_install_id}&user_id={user_id}&app_install_secret_key={app_install_secret_key}',
        'app_config_facebook_canvas_path' => NULL,
        'app_support_page_tab' => 1,
        'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
        'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
        'app_facebook_api_key' => '218961554869668'
      )
    );
    $this->CI->db->insert_batch('app', $app);

    $campaign = array(
              array(
                  'campaign_id' => 1,
                  'app_install_id' =>  1,
                  'campaign_name' => '1st campaign',
                  'campaign_detail' => 'This is the first campaign',
                  'campaign_status_id' => 1,
                  'campaign_start_timestamp' => '2011-05-19',
                  'campaign_end_timestamp' => '2021-05-18',
                'campaign_image' => BASE_URL.'assets/images/default/app.png',
                'campaign_end_message' => 'This campaign is ended, please wait for upcoming campaign soon'
              ),
              array(
                  'campaign_id' => 2,
                  'app_install_id' => 2,
                  'campaign_name' => '2nd campaign',
                  'campaign_detail' => 'This is the 2nd campaign',
                  'campaign_status_id' => 2,
                  'campaign_start_timestamp' => '2011-05-18',
                  'campaign_end_timestamp' => '2021-06-18',
                'campaign_image' => BASE_URL.'assets/images/default/app.png',
                'campaign_end_message' => 'This campaign is ended, please wait for upcoming campaign soon'
              ),
              array(
                  'campaign_id' => 4,
                  'app_install_id' => 2,
                  'campaign_name' => '3rd campaign',
                  'campaign_detail' => 'This is the 3rd campaign',
                  'campaign_status_id' => 2,
                  'campaign_start_timestamp' => '2011-05-18',
                  'campaign_end_timestamp' => '2011-12-31',
                'campaign_image' => BASE_URL.'assets/images/default/app.png',
                'campaign_end_message' => 'This campaign is ended, please wait for upcoming campaign soon'
              )
            );
    $this->CI->db->insert_batch('campaign', $campaign);

    $company = array(
            array(
                  'company_id' => 1,
                  'creator_user_id' => 1,
                  'company_name' => 'Figabyte Co., Ltd.',
                  'company_detail' => 'a social media agency and award-winning technology firm',
                  'company_address' => '71 G.P. House Building 3rd-floor, Sab Road, Si Phraya Road, Bangrak, Bangkok 10500',
                  'company_email' => 'contact@figabyte.com',
                  'company_telephone' => '026370286',
                  'company_register_date' => '2011-05-09 17:52:17',
                  'company_username' => '',
                  'company_password' => '',
                  'company_image' => BASE_URL.'assets/images/default/campaign.png'
              )
            );
    $this->CI->db->insert_batch('company', $company);

    $company_apps = array(
              array(
                  'company_id' => 1,
                  'app_id' => 1,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 2,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 3,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 4,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 5,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 6,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 7,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 8,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 9,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 10,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 11,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 12,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 13,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 14,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 15,
                  'available_date' => '2011-05-19 16:01:20'
              ),
              array(
                  'company_id' => 1,
                  'app_id' => 16,
                  'available_date' => '2011-05-19 16:01:20'
              )
            );
    $this->CI->db->insert_batch('company_apps', $company_apps);

    $installed_apps = array(
                array(
                    'app_install_id' => 1,
                    'company_id' => 1,
                    'app_id' => 1,
                    'app_install_status_id' => 1,
                    'app_install_date' => '2011-05-18 18:37:01',
                    'page_id' => 1,
                    'app_install_secret_key' => '457f81902f7b768c398543e473c47465',
                  'facebook_tab_url' => 'https://www.facebook.com/pages/Shtest/116586141725712?sk=app_202663143123531'
                ),
                array(
                    'app_install_id' => 2,
                    'company_id' => 1,
                    'app_id' => 2,
                    'app_install_status_id' => 1,
                    'app_install_date' => '2011-05-18 18:37:01',
                    'page_id' => 1,
                    'app_install_secret_key' => 'b4504b54bb0c27a22fedba10cca4eb55',
                  'facebook_tab_url' => 'https://www.facebook.com/pages/Shtest/116586141725712?sk=app_154899207922915'
                ),
                array(
                    'app_install_id' => 3,
                    'company_id' => 1,
                    'app_id' => 3,
                    'app_install_status_id' => 1,
                    'app_install_date' => '2011-05-18 18:37:01',
                    'page_id' => 1,
                    'app_install_secret_key' => '1dd5a598414f201bc521348927c265c3',
                  'facebook_tab_url' => ''
                ),
                array(
                    'app_install_id' => 4,
                    'company_id' => 1,
                    'app_id' => 4,
                    'app_install_status_id' => 1,
                    'app_install_date' => '2011-05-18 18:37:01',
                    'page_id' => 1,
                    'app_install_secret_key' => '19323810aedbbc8384b383fa21904626',
                  'facebook_tab_url' => 'https://www.facebook.com/pages/Shtest/116586141725712?sk=app_204755022911798'
                )
              );
    $this->CI->db->insert_batch('installed_apps', $installed_apps);

    $page = array(
          array(
                'page_id' => 1,
                'facebook_page_id' => '116586141725712',
                'company_id' => 1,
                'page_name' => 'SocialHappen Test',
                'page_detail' => 'This is socialhappen test page',
              'page_installed' => 0,
                'page_image' => BASE_URL.'assets/images/default/page.png',
              'page_user_fields' => json_encode(array(
                1 => array(
                  'name' => 'size',
                  'label' => 'Shirt size',
                  'type' => 'radio',
                  'required' => FALSE,
                  'rules' => NULL,
                  'items' => array(1=>'S',2=>'M',3=>'L',4=>'XL'),
                  'order' => 1,
                  'options' => NULL,
                  ),
                2 => array(
                  'name' => 'color',
                  'label' => 'Shirt color',
                  'type' => 'text',
                  'required' => FALSE,
                  'rules' => NULL,
                  'items' => NULL,
                  'order' => 2,
                  'options' => NULL,
                )
              )),
              'facebook_tab_url' => 'https://www.facebook.com/pages/Shtest/116586141725712?sk=app_108290455924296'
          ),
          array(
            'page_id' => 2,
            'facebook_page_id' => '135287989899131',
            'company_id' => 1,
            'page_name' => 'SH Beta',
            'page_detail' => 'This is socialhappen beta page',
            'page_installed' => 1,
            'page_image' => BASE_URL.'assets/images/default/page.png',
            'page_user_fields' => json_encode(array(
              1 => array(
                'name' => 'size',
                'label' => 'Shirt size',
                'type' => 'radio',
                'required' => TRUE,
                'rules' => NULL,
                'items' => array(1=>'S',2=>'M',3=>'L',4=>'XL'),
                'order' => 1,
                'verify_message' => '---',
                'options' => NULL,
                ),
              2 => array(
                'name' => 'color',
                'label' => 'Shirt color',
                'type' => 'text',
                'required' => FALSE,
                'rules' => NULL,
                'items' => NULL,
                'order' => 2,
                'verify_message' => '---',
                'options' => NULL,
              ),
              3 => array(
                'name' => 'checkbox_name',
                'label' => 'Checkbox',
                'type' => 'checkbox',
                'required' => FALSE,
                'rules' => NULL,
                'items' => array('value1', 'value2', 'value3'),
                'order' => 3,
                'verify_message' => '---',
                'options' => NULL,
              )
            )),
            'facebook_tab_url' => ''
          )
        );
    $this->CI->db->insert_batch('page', $page);

    $user = array(
          array(
              'user_id' => 1,
              'user_first_name' => 'Noom',
              'user_last_name' => 'Narze',
              'user_email' => 'noom@figabyte.com',
              'user_phone' => '0812345678',
              'user_password' => '64cdb985d7fee7c3d5bcd6972e65ca41093fdce9',
              'user_image' => 'https://graph.facebook.com/713558190/picture',
              'user_facebook_id' => 713558190,
              'user_register_date' => '2011-05-09 17:36:14',
              'user_last_seen' => '2011-05-18 12:57:24',
              'user_twitter_name' => '',
              'user_twitter_access_token' => '',
              'user_twitter_access_token_secret' => '',
              'user_timezone_offset' => 420,
              'user_is_developer' => 1
            ),
          array(
              'user_id' => 2,
              'user_first_name' => 'Pop',
              'user_last_name' => 'Prachya',
              'user_email' => 'pop@figabyte.com',
              'user_phone' => '0812345678',
              'user_password' => '64cdb985d7fee7c3d5bcd6972e65ca41093fdce9',
              'user_image' => 'https://graph.facebook.com/637741627/picture',
              'user_facebook_id' => 637741627,
              'user_register_date' => '2011-05-09 17:36:14',
              'user_last_seen' => '2011-05-18 12:57:24',
              'user_twitter_name' => '',
              'user_twitter_access_token' => '',
              'user_twitter_access_token_secret' => '',
              'user_timezone_offset' => 420,
              'user_is_developer' => 1
          ),
          array(
              'user_id' => 3,
              'user_first_name' => 'หลินปิง',
              'user_last_name' => 'จริงๆนะเออ',
              'user_email' => 'wachiraph.c@gmail.com',
              'user_phone' => '0812345678',
              'user_password' => '64cdb985d7fee7c3d5bcd6972e65ca41093fdce9',
              'user_image' => 'https://graph.facebook.com/631885465/picture',
              'user_facebook_id' => 631885465,
              'user_register_date' => '2011-05-09 17:36:14',
              'user_last_seen' => '2011-05-18 12:57:24',
              'user_twitter_name' => '',
              'user_twitter_access_token' => '',
              'user_twitter_access_token_secret' => '',
              'user_timezone_offset' => 420,
              'user_is_developer' => 1
          ),
          array(
              'user_id' => 4,
              'user_first_name' => 'Metwara',
              'user_last_name' => 'Narksook',
              'user_email' => 'hybridknight@gmail.com',
              'user_phone' => '0812345678',
              'user_password' => '64cdb985d7fee7c3d5bcd6972e65ca41093fdce9',
              'user_image' => 'https://graph.facebook.com/755758746/picture',
              'user_facebook_id' => 755758746,
              'user_register_date' => '2011-05-09 17:36:14',
              'user_last_seen' => '2011-05-18 12:57:24',
              'user_twitter_name' => '',
              'user_twitter_access_token' => '',
              'user_twitter_access_token_secret' => '',
              'user_timezone_offset' => 420,
              'user_is_developer' => 1
          ),
          array(
              'user_id' => 5,
              'user_first_name' => 'Charkrid',
              'user_last_name' => 'Thanhachartyothin',
              'user_email' => 'charkrid@figabyte.com',
              'user_phone' => '0812345678',
              'user_password' => '64cdb985d7fee7c3d5bcd6972e65ca41093fdce9',
              'user_image' => 'https://graph.facebook.com/508840994/picture',
              'user_facebook_id' => 508840994,
              'user_register_date' => '2011-05-09 17:36:14',
              'user_last_seen' => '2011-05-18 12:57:24',
              'user_twitter_name' => '',
              'user_twitter_access_token' => '',
              'user_twitter_access_token_secret' => '',
              'user_timezone_offset' => 420,
              'user_is_developer' => 1
          ),
          array(
              'user_id' => 6,
              'user_first_name' => 'Weerapat',
              'user_last_name' => 'Poosri',
              'user_email' => 'tong@figabyte.com',
              'user_phone' => '0812345678',
              'user_password' => '64cdb985d7fee7c3d5bcd6972e65ca41093fdce9',
              'user_image' => 'https://graph.facebook.com/688700832/picture',
              'user_facebook_id' => 688700832,
              'user_register_date' => '2011-08-03 19:00:00',
              'user_last_seen' => '2011-08-18 09:27:04',
              'user_twitter_name' => '',
              'user_twitter_access_token' => '',
              'user_twitter_access_token_secret' => '',
              'user_timezone_offset' => 420,
              'user_is_developer' => 1
          )
        );
    $this->CI->db->insert_batch('user', $user);

    $user_apps = array(
              array(
                  'user_id' => 1,
                  'app_install_id' => 1,
                  'user_apps_register_date' => '2011-05-19 19:12:20',
                  'user_apps_last_seen' => '0000-00-00 00:00:00'
              ),
              array(
                  'user_id' => 2,
                  'app_install_id' => 2,
                  'user_apps_register_date' => '2011-05-19 19:12:20',
                  'user_apps_last_seen' => '0000-00-00 00:00:00'
              )
            );
    $this->CI->db->insert_batch('user_apps', $user_apps);

    $user_campaigns = array(
                array(
                    'user_id' => 1,
                    'campaign_id' => 1
                ),
                array(
                    'user_id' => 2,
                    'campaign_id' => 2
                ),
                array(
                    'user_id' => 3,
                    'campaign_id' => 1
                ),
                array(
                    'user_id' => 4,
                    'campaign_id' => 2
                ),
                array(
                    'user_id' => 5,
                    'campaign_id' => 2
                ),
                array(
                    'user_id' => 6,
                    'campaign_id' => 1
                )
              );
    $this->CI->db->insert_batch('user_campaigns', $user_campaigns);

    $user_companies = array(
                array(
                    'user_id' => 1,
                    'company_id' => 1,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 2,
                    'company_id' => 1,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 3,
                    'company_id' => 1,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 4,
                    'company_id' => 1,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 5,
                    'company_id' => 1,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 6,
                    'company_id' => 1,
                    'user_role' => 1
                )
              );
    $this->CI->db->insert_batch('user_companies', $user_companies);


    $sessions = array( //for testing only
            array(
              'session_id' => 1111,
              'ip_address' => 0,
              'user_agent' => 0,
              'last_activity' => 0,
              'user_data' => 'a:3:{s:7:"user_id";s:5:"55555";s:16:"user_facebook_id";s:9:"713558190";s:9:"logged_in";b:1;}',
              'user_id' => 55555
              ),
            array(
              'session_id' => 2222,
              'ip_address' => 0,
              'user_agent' => 0,
              'last_activity' => 0,
              'user_data' => 'a:3:{s:7:"user_id";s:5:"55555";s:16:"user_facebook_id";s:9:"713558190";s:9:"logged_in";b:1;}',
              'user_id' => 1
              )
            );
    // if(!$this->CI->db->get_where('sessions', array('session_id'=>1111))){
      $this->CI->db->insert_batch('sessions', $sessions);
    // }

    $user_role = array(
                array(
                    'user_role_id' => 1,
                    'user_role_name' => 'Company Admin',
                  'role_all' => 1,
                  //'role_company_view' => 0,
                  'role_company_add' => 0,
                  'role_company_edit' => 0,
                  'role_company_delete' => 0,
                  //'role_all_company_pages_view' => 0,
                  'role_all_company_pages_edit' => 0,
                  'role_all_company_pages_delete' => 0,
                  //'role_all_company_apps_view' => 0,
                  'role_all_company_apps_edit' => 0,
                  'role_all_company_apps_delete' => 0,
                  //'role_all_company_campaigns_view' => 0,
                  'role_all_company_campaigns_edit' => 0,
                  'role_all_company_campaigns_delete' => 0,
                  //'role_page_view' => 0,
                  'role_page_add' => 0,
                  'role_page_edit' => 0,
                  'role_page_delete' => 0,
                  //'role_app_view' => 0,
                  'role_app_add' => 0,
                  'role_app_edit' => 0,
                  'role_app_delete' => 0,
                  //'role_campaign_view' => 0,
                  'role_campaign_add' => 0,
                  'role_campaign_edit' => 0,
                  'role_campaign_delete' => 0
                ),
                array(
                    'user_role_id' => 2,
                    'user_role_name' => 'Page Admin',
                  'role_all' => 0,
                  //'role_company_view' => 1,
                  'role_company_add' => 0,
                  'role_company_edit' => 0,
                  'role_company_delete' => 0,
                  //'role_all_company_pages_view' => 0,
                  'role_all_company_pages_edit' => 0,
                  'role_all_company_pages_delete' => 0,
                  //'role_all_company_apps_view' => 0,
                  'role_all_company_apps_edit' => 0,
                  'role_all_company_apps_delete' => 0,
                  //'role_all_company_campaigns_view' => 0,
                  'role_all_company_campaigns_edit' => 0,
                  'role_all_company_campaigns_delete' => 0,
                  //'role_page_view' => 1,
                  'role_page_add' => 0,
                  'role_page_edit' => 1,
                  'role_page_delete' => 1,
                  //'role_app_view' => 1,
                  'role_app_add' => 0,
                  'role_app_edit' => 0,
                  'role_app_delete' => 0,
                  //'role_campaign_view' => 1,
                  'role_campaign_add' => 0,
                  'role_campaign_edit' => 0,
                  'role_campaign_delete' => 0
                ),
                array(
                    'user_role_id' => 3,
                    'user_role_name' => 'Test admin',
                  'role_all' => 0,
                  //'role_company_view' => 1,
                  'role_company_add' => 1,
                  'role_company_edit' => 1,
                  'role_company_delete' => 1,
                  //'role_all_company_pages_view' => 0,
                  'role_all_company_pages_edit' => 1,
                  'role_all_company_pages_delete' => 1,
                  //'role_all_company_apps_view' => 0,
                  'role_all_company_apps_edit' => 1,
                  'role_all_company_apps_delete' => 1,
                  //'role_all_company_campaigns_view' => 0,
                  'role_all_company_campaigns_edit' => 1,
                  'role_all_company_campaigns_delete' => 1,
                  //'role_page_view' => 1,
                  'role_page_add' => 1,
                  'role_page_edit' => 1,
                  'role_page_delete' => 1,
                  //'role_app_view' => 1,
                  'role_app_add' => 1,
                  'role_app_edit' => 1,
                  'role_app_delete' => 1,
                  //'role_campaign_view' => 1,
                  'role_campaign_add' => 1,
                  'role_campaign_edit' => 1,
                  'role_campaign_delete' => 1
                )
              );
    $this->CI->db->insert_batch('user_role', $user_role);

    $user_pages = array(
                array(
                    'user_id' => 1,
                    'page_id' => 1,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 2,
                    'page_id' => 1,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 3,
                    'page_id' => 1,
                    'user_role' => 0
                ),
                array(
                    'user_id' => 4,
                    'page_id' => 1,
                    'user_role' => 2
                ),
                array(
                    'user_id' => 5,
                    'page_id' => 1,
                    'user_role' => 3
                ),
                array(
                    'user_id' => 6,
                    'page_id' => 1,
                    'user_role' => 3
                ),
                array(
                    'user_id' => 1,
                    'page_id' => 2,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 2,
                    'page_id' => 2,
                    'user_role' => 1
                ),
                array(
                    'user_id' => 3,
                    'page_id' => 2,
                    'user_role' => 0
                ),
                array(
                    'user_id' => 4,
                    'page_id' => 2,
                    'user_role' => 2
                ),
                array(
                    'user_id' => 5,
                    'page_id' => 2,
                    'user_role' => 3
                ),
                array(
                    'user_id' => 6,
                    'page_id' => 2,
                    'user_role' => 3
                ),
              );
    $this->CI->db->insert_batch('user_pages', $user_pages);

    $package = array(
      array(
        'package_name' => 'Normal package',
        'package_detail' => 'For normal user',
        'package_image' => BASE_URL.'images/package_icon_free.png',
        'package_max_companies' => 1,
        'package_max_pages' => 3,
        'package_max_users' => 10000,
        'package_price' => 0,
        'package_custom_badge' => 0,
        'package_duration' => 'unlimited'
      ),
      array(
        'package_name' => 'Standard package',
        'package_detail' => 'For SMEs',
        'package_image' => BASE_URL.'images/package_icon_standard.png',
        'package_max_companies' => 3,
        'package_max_pages' => 7,
        'package_max_users' => 50000,
        'package_price' => 49,
        'package_custom_badge' => 1,
        'package_duration' => '1month'
      ),
      array(
        'package_name' => 'Enterprise package',
        'package_detail' => 'For Enterprise',
        'package_image' => BASE_URL.'images/package_icon_enterprise.png',
        'package_max_companies' => 3,
        'package_max_pages' => 10,
        'package_max_users' => 100000,
        'package_price' => 99,
        'package_custom_badge' => 1,
        'package_duration' => '1month'
      ),
    );
    $this->CI->db->insert_batch('package', $package);

    $package_users = array(
      array(
        'package_id' => 1,
        'user_id' => 1,
        'package_expire' => '2012-06-19 19:12:20'
      ),
      array(
        'package_id' => 1,
        'user_id' => 2,
        'package_expire' => '2012-07-19 20:12:20'
      ),
      array(
        'package_id' => 1,
        'user_id' => 3,
        'package_expire' => '2012-08-19 23:59:59'
      ),
      array(
        'package_id' => 1,
        'user_id' => 4,
        'package_expire' => '2012-09-19 19:12:20'
      ),
      array(
        'package_id' => 1,
        'user_id' => 5,
        'package_expire' => '2012-01-19 23:59:59'
      ),
      array(
        'package_id' => 2,
        'user_id' => 6,
        'package_expire' => '2012-02-19 23:59:59'
      )
    );
    $this->CI->db->insert_batch('package_users', $package_users);

    $package_apps = array(
      array(
        'package_id' => 1,
        'app_id' => 1
      ),
      array(
        'package_id' => 1,
        'app_id' => 2
      ),
      array(
        'package_id' => 1,
        'app_id' => 3
      ),
      array(
        'package_id' => 1,
        'app_id' => 4
      ),
      array(
        'package_id' => 1,
        'app_id' => 5
      ),
      array(
        'package_id' => 1,
        'app_id' => 6
      ),
      array(
        'package_id' => 1,
        'app_id' => 7
      ),
      array(
        'package_id' => 1,
        'app_id' => 8
      ),
      array(
        'package_id' => 1,
        'app_id' => 9
      ),
      array(
        'package_id' => 1,
        'app_id' => 10
      ),
      array(
        'package_id' => 1,
        'app_id' => 11
      ),
      array(
        'package_id' => 1,
        'app_id' => 12
      ),
      array(
        'package_id' => 1,
        'app_id' => 13
      ),
      array(
        'package_id' => 2,
        'app_id' => 1
      ),
      array(
        'package_id' => 2,
        'app_id' => 2
      ),
      array(
        'package_id' => 2,
        'app_id' => 3
      ),
      array(
        'package_id' => 2,
        'app_id' => 4
      ),
      array(
        'package_id' => 2,
        'app_id' => 5
      ),
      array(
        'package_id' => 2,
        'app_id' => 6
      ),
      array(
        'package_id' => 2,
        'app_id' => 7
      ),
      array(
        'package_id' => 2,
        'app_id' => 8
      ),
      array(
        'package_id' => 2,
        'app_id' => 9
      ),
      array(
        'package_id' => 2,
        'app_id' => 10
      ),
      array(
        'package_id' => 2,
        'app_id' => 11
      ),
      array(
        'package_id' => 2,
        'app_id' => 12
      ),
      array(
        'package_id' => 3,
        'app_id' => 1
      ),
      array(
        'package_id' => 3,
        'app_id' => 2
      ),
      array(
        'package_id' => 3,
        'app_id' => 3
      ),
      array(
        'package_id' => 3,
        'app_id' => 4
      ),
      array(
        'package_id' => 3,
        'app_id' => 5
      ),
      array(
        'package_id' => 3,
        'app_id' => 6
      ),
      array(
        'package_id' => 3,
        'app_id' => 7
      ),
      array(
        'package_id' => 3,
        'app_id' => 8
      ),
      array(
        'package_id' => 3,
        'app_id' => 9
      ),
      array(
        'package_id' => 3,
        'app_id' => 10
      ),
      array(
        'package_id' => 3,
        'app_id' => 11
      ),
      array(
        'package_id' => 3,
        'app_id' => 12
      )
    );
    $this->CI->db->insert_batch('package_apps', $package_apps);

    $order = array(
      array(
        'order_id' => 1,
        'order_date' => '2011-08-18 16:33:00',
        'order_status_id' => 2,
        'order_net_price' => 999,
        'user_id' => 1,
        'payment_method' => 'paypal',
        'billing_info' => 'a:7:{s:15:"user_first_name";s:8:"Weerapat";s:14:"user_last_name";s:6:"Poosri";s:10:"user_email";s:17:"tong@figabyte.com";s:18:"credit_card_number";s:0:"";s:24:"credit_card_expire_month";s:0:"";s:23:"credit_card_expire_year";s:0:"";s:15:"credit_card_csc";s:0:"";}'
      ),
      array(
        'order_id' => 2,
        'order_date' => '2011-08-18 17:12:00',
        'order_status_id' => 1,
        'order_net_price' => 999,
        'user_id' => 1,
        'payment_method' => 'paypal',
        'billing_info' => 'a:12:{s:15:"user_first_name";s:8:"Weerapat";s:14:"user_last_name";s:6:"Poosri";s:10:"user_email";s:17:"tong@figabyte.com";s:18:"credit_card_number";s:0:"";s:24:"credit_card_expire_month";s:0:"";s:23:"credit_card_expire_year";s:0:"";s:15:"credit_card_csc";s:0:"";s:8:"payer_id";s:13:"GEYCL6WB86N62";s:6:"txn_id";s:17:"9CP746008S6070136";s:14:"payment_status";s:9:"Completed";s:14:"pending_reason";s:4:"None";s:11:"reason_code";s:4:"None";}'
      )
    );
    $this->CI->db->insert_batch('order', $order);

    $order_items = array(
      array(
        'order_id' => 1,
        'item_id' => 2,
        'item_type_id' => 1,
        'item_name' => 'Enterprise package',
        'item_description' => 'For enterprise',
        'item_price' => 999,
        'item_unit' => 1,
        'item_discount' => 0
      ),
      array(
        'order_id' => 2,
        'item_id' => 2,
        'item_type_id' => 1,
        'item_name' => 'Enterprise package',
        'item_description' => 'For enterprise',
        'item_price' => 999,
        'item_unit' => 1,
        'item_discount' => 0
      )
    );
    $this->CI->db->insert_batch('order_items', $order_items);

    $page_user_data = array(
      array(
        'user_id' => 1,
        'page_id' => 1,
        'user_data' => json_encode(array(1 => 'L', 2 => 'red'))
      ),
      array(
        'user_id' => 2,
        'page_id' => 1,
        'user_data' => json_encode(array(1 => 'S', 2 => 'blue'))
      ),
      array(
        'user_id' => 3,
        'page_id' => 1,
        'user_data' => json_encode(array(1 => 'M', 2 => 'red'))
      ),
      array(
        'user_id' => 4,
        'page_id' => 1,
        'user_data' => json_encode(array(1 => 'S', 2 => 'blue'))
      ),
      array(
        'user_id' => 5,
        'page_id' => 1,
        'user_data' => json_encode(array(1 => 'L', 2 => 'blue'))
      ),
      array(
        'user_id' => 6,
        'page_id' => 1,
        'user_data' => json_encode(array(1 => 'L', 2 => 'red'))
      )
    );
    $this->CI->db->insert_batch('page_user_data', $page_user_data);

    //Data for use in development & not test db
    if((ENVIRONMENT === 'development' || ENVIRONMENT === 'testing') && !$this->CI->config->item('mongo_testmode')) {
    //Add app 10001+
      $app = array(
        array(
          'app_id' => 10001,
          'app_name' => 'Ghost',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'ล่าท้าผี คุณจะเจอผีแบบไหน',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/ghost/images/start.jpg'
        ),
        array(
          'app_id' => 10002,
          'app_name' => 'Songkran',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'สงกรานต์นี้ คุณจะโดนกี่น้ำ',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/songkran/images/start.gif'
        ),
        array(
          'app_id' => 10003,
          'app_name' => 'PostItLove',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'คุณอยากจะบอก ... ว่า ...',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/postit/images/start.jpg'
        ),
        array(
          'app_id' => 10004,
          'app_name' => 'PoseTonight',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'ท่ายากของคุณในคืนนี้คืออะไร เราไปดูกันเลยครับ!',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/posetonight/images/start.jpg'
        ),
        array(
          'app_id' => 10005,
          'app_name' => 'WeddingPlace',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'คุณจะได้กันที่ไหน',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/weddingplace/images/start.jpg'
        ),
        array(
          'app_id' => 10006,
          'app_name' => 'KhmerName',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'ชื่อเขมรของคุณคืออะไร',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/khmername/images/start.jpg'
        ),
        array(
          'app_id' => 10007,
          'app_name' => 'LoveStatus',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'สถานะโดนใจ มือกดไลท์ ไฟเปิดเลย',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/lovestatus/images/start.jpg'
        ),
        array(
          'app_id' => 10008,
          'app_name' => 'HiddenProfile',
          'app_type_id' => $this->CI->socialhappen->get_k('app_type','Standalone'),
          'app_maintainance' => 0,
          'app_show_in_list' => 0,
          'app_description' => 'อะไรแอบในรูปโปรไฟล์ของคุณ',
          'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214',
          'app_url' => 'https://apps.localhost.com/shapp/',
          'app_install_url' => 'https://apps.localhost.com/shapp/?install',
          'app_install_page_url' => 'https://apps.localhost.com/shapp/?install_page',
          'app_config_url' => 'https://apps.localhost.com/shapp/?config',
          'app_config_facebook_canvas_path' => NULL,
          'app_support_page_tab' => 1,
          'app_icon' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_16.png',
          'app_image' =>  'https://apps.localhost.com/voteapp/assets/images/app_image_o.png',
          'app_facebook_api_key' => '204755022911798',
          'app_banner' => 'https://apps.localhost.com/apps/hiddenprofile/images/start.jpg'
        )
      );
      $this->CI->db->insert_batch('app', $app);
      echo "Development test data added<br />";
    }

    echo "Test data added<br />";
  }





  function drop_mongo_collections($collections){
    foreach($collections as $collection){
      echo $this->CI->mongodb->drop_collection($collection) ? "Dropped {$collection}<br />" : "Cannot drop {$collection}<br />";
    }
  }

  function mongodb_reset(){
    $mongo_db_prefix = NULL;
    $this->CI->load->config('mongo_db');
    if($this->CI->config->item('mongo_testmode') == TRUE){
      $mongo_db_prefix = $this->CI->config->item('mongo_testmode_prefix');
    }
    $collections = array(
      'achievement_info',
      'achievement_stat',
      'achievement_user',
      'achievement_stat_page',
      'achievement_stat_company',
      'action_data',
      'action_user_data',
      'app_component',
      'app_component_homepage',
      'app_component_page',
      'audits',
      'audit_actions',
      'audit_stats',
      'get_started_info',
      'get_started_stat',
      'invites',
      'invite_pending',
      'reward',
      'reward_item',
      'stat_apps',
      'stat_campaigns',
      'stat_pages',
      'notification',
      'challenge',
      'user',
      'coupon'
    );
    $mongo_db_name = $this->CI->config->item('mongo_db');
    $this->CI->load->library('mongo_db', NULL, 'mongodb');
    $this->CI->mongodb->switch_db($mongo_db_prefix.$mongo_db_name);
    $this->drop_mongo_collections($collections);
    echo 'Dropped collections<br />';

    $this->CI->load->library('audit_lib');
    $this->CI->load->library('achievement_lib');

    $platform_audit_actions = array(
      array(
        'app_id' => 0,
        'action_id' => 1,
        'description' => 'Install App',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has installed {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 2,
        'description' => 'Install App To Page',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has installed {app:object} in {page:page_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 3,
        'description' => 'Remove App',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has removed {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 4,
        'description' => 'Update Config',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has updated {app:app_id} configuration',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 5,
        'description' => 'Install Page',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has installed {page:page_id} in {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 6,
        'description' => 'Create Company',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has created company {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 7,
        'description' => 'Buy Package',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has bought package {package:object}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 8,
        'description' => 'Buy Most Expensive Package',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has bought the most expensive package {package:object}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 101,
        'description' => 'User Register SocialHappen',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} has registered SocialHappen',
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'action_id' => 102,
        'description' => 'User Register App',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} has registered {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 103,
        'description' => 'User Visit',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} visited {app_install:app_install_id} in {page:page_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 104,
        'description' => 'User Action',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} has action', //@TODO What action?
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 105,
        'description' => 'User Join Campaign',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} has joined {campaign:campaign_id} in {app:app_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 106,
        'description' => 'User Register Page',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} registered {page:page_id}',
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'action_id' => 107,
        'description' => 'User Share Profile',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} shared profile',
        'score' => 5
      ),
      array(
        'app_id' => 0,
        'action_id' => 108,
        'description' => 'User Share For Star',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} shared on {app_install:app_install_id}',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 109,
        'description' => 'User Login',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} logged in',
        'score' => 5
      ),
      array(
        'app_id' => 0,
        'action_id' => 110,
        'description' => 'User Link to Twitter',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} linked with Twitter account',
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'action_id' => 111,
        'description' => 'User Link to Facebook',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} linked with Facebook account',
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'action_id' => 112,
        'description' => 'User Link to Foursquare',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} linked with Foursquare account',
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'action_id' => 113,
        'description' => 'User Invite Friend',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} invited a friend',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 114,
        'description' => 'Invitee Accept Page Invite',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} accepted page invite from {user:subject}',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 115,
        'description' => 'Invitee Accept Campaign Invite',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => TRUE,
        'format_string' => 'User {user:user_id} accepted campaign invite from {user:subject}',
        'score' => 1
      ),
      array(
        'app_id' => 0,
        'action_id' => 116,
        'description' => 'User Receive Coupon',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} received a reward coupon from company {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 117,
        'description' => 'User Join Challenge',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} joined challenge {challenge:objecti}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 118,
        'description' => 'User Complete Challenge',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} completed challenge {challenge:objecti}',
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'action_id' => 119,
        'description' => 'User Redeem Reward',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} redeemed {string:object} from company {company:company_id}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 201,
        'description' => 'QR',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} entered QR code in challenge {challenge:objecti}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 202,
        'description' => 'Feedback',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} gave feedback and rated {string:object} in challenge {challenge:objecti}',
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'action_id' => 203,
        'description' => 'Check-In',
        'stat_app' => FALSE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} check-in at {string:object} in challenge {challenge:objecti}',
        'score' => 0
      ),
    );

    $audit_actions = array(
      array(
        'app_id' => 5,
        'action_id' => 1001,
        'description' => 'View video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has viewed video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 5,
        'action_id' => 1002,
        'description' => 'Share video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has shared video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 6,
        'action_id' => 1001,
        'description' => 'View video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has viewed video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 6,
        'action_id' => 1002,
        'description' => 'Share video',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has shared video {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 7,
        'action_id' => 1001,
        'description' => 'Share feed',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has shared feed {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 8,
        'action_id' => 1001,
        'description' => 'Share feed',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => FALSE,
        'format_string' => 'User {user:user_id} has shared feed {string:object} in {app:app_id}',
        'score' => 1
      ),
      array(
        'app_id' => 13,
        'action_id' => 2000,
        'description' => 'User answer a question',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} answered question {string: object} with answer {string: objecti} in {page: page_id}',
        'score' => 0
        ),
      array(
        'app_id' => 15,
        'action_id' => 1001,
        'description' => 'User votes an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} votes item {string: object} in {page: page_id}',
        'score' => 0
        ),
      array(
        'app_id' => 15,
        'action_id' => 1002,
        'description' => 'User shares an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} answered question {string: object} in {page: page_id}',
        'score' => 0
        ),
      array(
        'app_id' => 16,
        'action_id' => 1001,
        'description' => 'User votes an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} votes item {string: object} in {page: page_id}',
        'score' => 1
        ),
      array(
        'app_id' => 16,
        'action_id' => 1002,
        'description' => 'User shares an item',
        'stat_app' => TRUE,
        'stat_page' => TRUE,
        'stat_campaign' => TRUE,
        'format_string' => '{user: user_id} answered question {string: object} in {page: page_id}',
        'score' => 1
        ),
    );
    foreach(array_merge($platform_audit_actions,$audit_actions) as $audit_action){
      $this->CI->audit_lib->add_audit_action($audit_action['app_id'], $audit_action['action_id'],
        $audit_action['description'], $audit_action['stat_app'], $audit_action['stat_page'],
        $audit_action['stat_campaign'], $audit_action['format_string'], $audit_action['score']);
    }
    echo 'Added '.count(array_merge($platform_audit_actions,$audit_actions)).' audit actions<br />';

    $achievement_infos = array(
      // array(
        // 'app_id' => 5,
        // 'app_install_id' => NULL,
        // 'info' => array(
          // 'name' => 'First share',
          // 'description' => 'Shared video for the first time',
          // 'criteria_string' => array('Share = 1')
        // ),
        // 'criteria' => array(
          // 'action.1002.count' => 1
        // )
      // ),
      // array(
        // 'app_id' => 6,
        // 'app_install_id' => NULL,
        // 'info' => array(
          // 'name' => 'First share',
          // 'description' => 'Shared video for the first time',
          // 'criteria_string' => array('Share = 1')
        // ),
        // 'criteria' => array(
          // 'action.1002.count' => 1
        // )
      // ),
      // array(
        // 'app_id' => 7,
        // 'app_install_id' => NULL,
        // 'info' => array(
          // 'name' => 'First share',
          // 'description' => 'Shared feed for the first time',
          // 'criteria_string' => array('Share = 1')
        // ),
        // 'criteria' => array(
          // 'action.1001.count' => 1
        // )
      // ),
      // array(
        // 'app_id' => 8,
        // 'app_install_id' => NULL,
        // 'info' => array(
          // 'name' => 'First share',
          // 'description' => 'Shared feed for the first time',
          // 'criteria_string' => array('Share = 1')
        // ),
        // 'criteria' => array(
          // 'action.1001.count' => 1
        // )
      // ),
    );
    $platform_achievements = array(
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'I\'m using SocialHappen',
          'description' => 'Share profile the 1st time',
          'criteria_string' => array('Share Profile = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.107.count' => 1
        ),
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Bragger',
          'description' => 'Share profile 10 times',
          'criteria_string' => array('Share Profile = 10'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.107.count' => 10
        ),
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Hello World',
          'description' => 'Share the 1st time',
          'criteria_string' => array('Share = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/50-helloworld.png'
        ),
        'criteria' => array(
          'action.108.count' => 1
        ),
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Speaker',
          'description' => 'Share 10 times',
          'criteria_string' => array('Share = 10'),
          'badge_image' => BASE_URL.'assets/images/badges/50-speaker.png'
        ),
        'criteria' => array(
          'action.108.count' => 10
        ),
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Crazy Reporter',
          'description' => 'Share 50 times',
          'criteria_string' => array('Share = 50'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.108.count' => 50
        ),
        'score' => 250
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'News Channel',
          'description' => 'Share 100 times',
          'criteria_string' => array('Share = 100'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.108.count' => 100
        ),
        'score' => 500
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Share Troll',
          'description' => 'Share 250 times',
          'criteria_string' => array('Share = 250'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.108.count' => 250
        ),
        'score' => 1000
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Just Arrived',
          'description' => 'Sign Up SocialHappen',
          'criteria_string' => array('Signup = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/50-arrived.png'
        ),
        'criteria' => array(
          'action.101.count' => 1
        ),
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Hello the Club',
          'description' => 'First time register to any page',
          'criteria_string' => array('Register Page = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/50-helloclub.png'
        ),
        'criteria' => array(
          'action.106.count' => 1
        ),
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Club Newbie',
          'description' => 'Joined 3 SocialHappen Pages',
          'criteria_string' => array('Register Page = 3'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.106.count' => 3
        ),
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Club Master',
          'description' => 'Joined 10 SocialHappen Pages',
          'criteria_string' => array('Register Page = 10'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.106.count' => 10
        ),
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Hello Old Friend',
          'description' => 'Login 5 times',
          'criteria_string' => array('Login Count = 5'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.109.count' => 5
        ),
        'score' => 10
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Kudos For Coming Back',
          'description' => 'Login 10 times',
          'criteria_string' => array('Login Count = 10'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.109.count' => 10
        ),
        'score' => 50
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Loyal Friend',
          'description' => 'Login 50 times',
          'criteria_string' => array('Login Count = 50'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.109.count' => 50
        ),
        'score' => 100
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'The Tweet Bird',
          'description' => 'Connect to Twitter',
          'criteria_string' => array('Connect Twitter Count = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/50-tw.png'
        ),
        'criteria' => array(
          'action.110.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'The Mark Zuckerburg Network Effect',
          'description' => 'Connect to Facebook',
          'criteria_string' => array('Connect Facebook Count = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/50-fb.png'
        ),
        'criteria' => array(
          'action.111.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'I\'m a Mayor',
          'description' => 'Connect to Foursquare',
          'criteria_string' => array('Connect Foursquare Count = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.112.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'You\'re Not Alone',
          'description' => 'Invite your 1st friend',
          'criteria_string' => array('Invite = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'I Have A Team',
          'description' => 'Invite 10 friends',
          'criteria_string' => array('Invite = 10'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 10
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Social Animal',
          'description' => 'Invite 50 friends',
          'criteria_string' => array('Invite = 50'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 50
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Celebrity',
          'description' => 'Invite 100 Friends',
          'criteria_string' => array('Invite = 100'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 100
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'The Invitation Engine',
          'description' => 'Invite 500 Friends',
          'criteria_string' => array('Invite = 500'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.113.count' => 500
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'You Are Admin',
          'description' => 'Buy a package',
          'criteria_string' => array('Package Bought = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.7.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Nobel',
          'description' => 'Buy the most expensive package',
          'criteria_string' => array('Package Bought = Most Expensive'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.8.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Admin Newbie',
          'description' => 'Install SocialHappen to Facebook page',
          'criteria_string' => array('Install page = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.5.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'Page Admin Newbie', //Temp name
          'description' => 'Install apps to SocialHappen page',
          'criteria_string' => array('Install app to page = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.2.count' => 1
        ),
        'score' => 0
      ),
      array(
        'app_id' => 0,
        'app_install_id' => NULL,
        'info' => array(
          'name' => 'First Challenge Done',
          'description' => 'Completed challenge the first time',
          'criteria_string' => array('Challenge completed = 1'),
          'badge_image' => BASE_URL.'assets/images/badges/default.png'
        ),
        'criteria' => array(
          'action.118.count' => 1
        ),
        'score' => 0
      ),
    );
    foreach(array_merge($achievement_infos, $platform_achievements) as $achievement_info){
      $this->CI->achievement_lib->add_achievement_info(
        $achievement_info['app_id'], $achievement_info['app_install_id'],
        $achievement_info['info'], $achievement_info['criteria']);
    }
    echo 'Added '.count(array_merge($achievement_infos, $platform_achievements)).' achievement infos<br />';

    $get_started_infos = array(
      array('id'=>101, 'type' =>'page', 'group' =>'config_page', 'link' => '{base_url}settings/page_apps/{page_id}', 'name' => 'Configure Your Own Sign-Up Form'),
      array('id'=>102, 'type' =>'page', 'group' =>'config_page', 'link' => '#', 'name' => 'View How Your Members See The Sign-Up Form'),
      array('id'=>103, 'type' =>'all', 'group' =>'install_app', 'link' => '{base_url}home/apps?pid={page_id}', 'name' => 'Go To Application List'),
      array('id'=>104, 'type' =>'all', 'group' =>'install_app', 'link' => '#', 'name' => 'See Where I Can Manage My Applications'),
      array('id'=>105, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How to Manage Your Page and Applications'),
      array('id'=>106, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How Your Members See SocialHappen Tab'),
      array('id'=>107, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How Your Members Interact With Your Page'),
      array('id'=>108, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How to View Members Profiles and Their Activities'),
      array('id'=>109, 'type' =>'all', 'group' =>'tour', 'link' => '#', 'name' => 'Learn How to Manage Campaign')
    );

    $this->CI->load->model('get_started_model', 'get_started');
    foreach($get_started_infos as $get_started_info){
      $this->CI->get_started->add_get_started_info($get_started_info);
    }
    echo 'Added '.count($get_started_infos).' get-started infos<br />';

    /*
    $get_started_stats = array(
      array( 'id' => 1, 'type' => 'page', 'items' => array(101,102)),
      array( 'id' => 2, 'type' => 'page', 'items' => array(102, 105)),
      array( 'id' => 3, 'type' => 'page', 'items' => array(109))
    );

    foreach($get_started_stats as $get_started_stat){
      $this->CI->get_started->add_get_started_stat($get_started_stat['id'], $get_started_stat['type'], $get_started_stat['items']);
    }
    echo 'Added '.count($get_started_stats).' get-started stats<br />';
    */

    $app_component_page_data = array(
      array(
        'page_id' => 1,
        'classes' => array(
        array('name' => 'New Comer',
            'invite_accepted' => 0),
        array('name' => 'Founding',
            'invite_accepted' => 3),
        array('name' => 'VIP',
            'invite_accepted' => 10),
        array('name' => 'Prime',
            'invite_accepted' => 50)
        )
      ),
      array(
        'page_id' => 2,
        'classes' => array(
        array('name' => 'New Comer',
            'invite_accepted' => 0),
        array('name' => 'Founding',
            'invite_accepted' => 3),
        array('name' => 'VIP',
            'invite_accepted' => 10),
        array('name' => 'Prime',
            'invite_accepted' => 50)
        )
      ),
      array(
        'page_id' => 3,
        'classes' => array(
        array('name' => 'New Comer',
            'invite_accepted' => 0),
        array('name' => 'Founding',
            'invite_accepted' => 3),
        array('name' => 'VIP',
            'invite_accepted' => 10),
        array('name' => 'Prime',
            'invite_accepted' => 50)
        )
        )
    );

    $this->CI->load->library('app_component_lib');
    foreach($app_component_page_data as $app_component_page){
      $this->CI->app_component_lib->add_page($app_component_page);
    }

    //Data for use in development & not test db
    if((ENVIRONMENT === 'development' || ENVIRONMENT === 'testing') && !$this->CI->config->item('mongo_testmode')) {
      //Play app id 10001 to add audit
      $this->CI->load->library('apiv2_lib');
      $input = array(
        'user_facebook_id' => '713558190',
        'app_id' => 10001,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $input = array(
        'user_facebook_id' => '637741627',
        'app_id' => 10001,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $input = array(
        'user_facebook_id' => '631885465',
        'app_id' => 10001,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $input = array(
        'user_facebook_id' => '755758746',
        'app_id' => 10001,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $input = array(
        'user_facebook_id' => '508840994',
        'app_id' => 10001,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $input = array(
        'user_facebook_id' => '688700832',
        'app_id' => 10001,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);


      //Play app id 10003 2 times to add audit
      $this->CI->load->library('apiv2_lib');
      $input = array(
        'user_facebook_id' => '713558190',
        'app_id' => 10003,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $this->CI->apiv2_lib->play_app($input);

      $input = array(
        'user_facebook_id' => '637741627',
        'app_id' => 10003,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $this->CI->apiv2_lib->play_app($input);

      $input = array(
        'user_facebook_id' => '631885465',
        'app_id' => 10003,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $this->CI->apiv2_lib->play_app($input);

      $input = array(
        'user_facebook_id' => '755758746',
        'app_id' => 10003,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $this->CI->apiv2_lib->play_app($input);

      $input = array(
        'user_facebook_id' => '508840994',
        'app_id' => 10003,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $this->CI->apiv2_lib->play_app($input);

      $input = array(
        'user_facebook_id' => '688700832',
        'app_id' => 10003,
        'app_secret_key' => 'ae25b2c54e89d224de554de6a5edd214'
      );
      $this->CI->apiv2_lib->play_app($input);
      $this->CI->apiv2_lib->play_app($input);

      echo "Development test data added<br />";
    }

    echo 'MongoDB reset successfully';
  }
}
/* End of file db_sync.php */
/* Location: ./application/controllers/libraries/db_sync.php */