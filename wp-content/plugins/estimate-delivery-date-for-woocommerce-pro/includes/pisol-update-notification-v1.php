<?php
/**
 * v1.0.1
 */
if(!class_exists('pisol_update_notification_v1')){

class pisol_update_notification_v1{
  public $update_path;
 
  public $plugin_slug;

    function __construct($plugin_slug, $current_version)
    {
      $this->current_version = $current_version;
      $this->update_path = 'https://www.piwebsolution.com/update-server-v1/';
      $this->plugin_slug = $plugin_slug;

      list ($t1, $t2) = explode('/', $plugin_slug);

      $this->plugin_folder = $t1;
      
      $this->site_url = get_site_url();
    
      add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));

      add_action( 'in_plugin_update_message-'.$this->plugin_slug, array(&$this, 'extraMessage'), 10, 2 );

      add_filter( 'auto_update_plugin', array($this, 'removeAutoUpdate'),10,2);
    }

    function removeAutoUpdate($actions, $plugin_data){
      if(!empty($plugin_data->plugin) && $plugin_data->plugin == $this->plugin_slug){
        return false;
      }
      return $actions;
    }
  
    public function check_update($transient)
    {
      
      if (empty($transient->checked)) {
        return $transient;
      }
    
      $remote_response = $this->getRemote_version();

      if(is_object($remote_response) && isset($remote_response->{$this->plugin_folder})){
        $plugin_response = $remote_response->{$this->plugin_folder};
      
        if (is_object($plugin_response) && !empty($plugin_response->version) && version_compare($this->current_version, $plugin_response->version, '<')) {
          $obj = new stdClass();
          $obj->slug = $this->plugin_folder;
          $obj->url = $this->update_path;
          $obj->plugin = $this->plugin_slug;
          $obj->new_version = !empty($plugin_response->version) ? $plugin_response->version : "";
          $obj->package = !empty($plugin_response->download_link) ? $plugin_response->download_link : "";
          $obj->message = !empty($plugin_response->message) ? $plugin_response->message : "";
          $transient->response[$this->plugin_slug] = $obj;
          $transient->no_update[$this->plugin_slug] = $obj;
        }
      }
      
      return $transient;
    }

    public function getRemote_version()
    {
      $request = wp_remote_get($this->update_path, array('plugin_folder'=> $this->plugin_folder, 'current_version'=>$this->current_version, 'website'=> $this->site_url));
      
      if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
        return json_decode($request['body']);
      }
      return false;
    }

    function extraMessage($data, $response){
      if(isset( $data['message'] ) && !empty($data['message'] )){
        echo '<br><span>'.$data['message'] .'</span>';
      }
    }
}
}