<?php
/**
 * Plugin Name: LH Buddypress Multi Network
 * Plugin URI: https://lhero.org/portfolio/lh-buddypress-multi-network/
 * Version: 1.01
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com
 * Description: A better way to enable multiple BuddyPress networks on a WordPress Multisite/BuddyPress Install
 * Text Domain: lh_bmn
 * Domain Path: /languages
 */
 
if (!class_exists('LH_Buddypress_multi_network_plugin')) {



class LH_Buddypress_multi_network_plugin {
    
    private static $instance; 
    
 static function return_plugin_namespace(){
    
    return 'lh_bmn';
    
    } 
    
static function return_roles() {
    $all_roles = wp_roles()->roles;
    

    
    $return_roles = array();
    
    foreach ( $all_roles as $key => $value) {
        $return_roles[] = esc_attr($key);
    }
    

    return $return_roles;
}
    
    /**
     * Get the list of users member of this network
     * @param type $network_id
     * @return type 
     */
static function get_users( $network_id = null ) {
        
if (!empty($network_id ) && (get_current_blog_id() !== $network_id )){

switch_to_blog($blog_id);    
    
}


     $args = array(
    'role__in'    => self::return_roles(),
    'orderby' => 'ID',
    'fields' => 'ID'
);
$users = get_users( $args );

restore_current_blog();

if (!empty($users)){

        return $users;
        
} else {
    
    return false;
}
        
    }

    
static function get_network_users_count( $network_id = null ) {
        
  $users = self::get_users($network_id);
					
					
					
	if (!empty($users)){
	    
	    return count($users);
	    
	} else {
        
    false;
        
	}
  } 
  
static function get_user_role_by_id($user_id) {
    
$user_data = get_userdata($user_id);

    if (!empty($user_data->roles)) {

       $role = (array) $user_data->roles;
       return $role[0];
   
       } else {
           
     return false;
     
   }
}


//this function ensure all current users for the blog are active, have a name, and have been added to the blog table
  
  
static function prepare_all_users(){
    

//this section ensures all users have a last activity date

global $wpdb;
    
$sql = "SELECT t1.ID FROM ".$wpdb->users." t1 LEFT JOIN ".buddypress()->activity->table_name." t2 ON t1.ID = t2.user_id and t2.type = 'last_activity' WHERE t2.user_id is NULL";

$activity_results = $wpdb->get_results($sql);

foreach ($activity_results as $result) {
    
bp_update_user_last_activity( $result->ID);
    
}


//this section adds them to the current blog if they are not already

$the_users = self::get_users(get_current_blog_id());

foreach ($the_users as $the_user_id) {
    
$role = self::get_user_role_by_id($the_user_id);

if (!empty($role) && ($role != 'unclaimed')){
    
bp_blogs_record_blog(get_current_blog_id(), $the_user_id);    
    
}   

$the_name = xprofile_get_field_data('name', $the_user_id);

if (empty($the_name)){
    
// Get user data by user id
$nameless_user = get_userdata( $the_user_id ); 

if (!empty($nameless_user->display_name)){
    
xprofile_set_field_data( 'name', $the_user_id, $nameless_user->display_name);   
    
} elseif (!empty($nameless_user->first_name) or !empty($nameless_user->last_name)){
    
xprofile_set_field_data( 'name', $the_user_id, $nameless_user->first_name.' '.$nameless_user->last_name);      
    
    
}
    
}

}
    
    
}

static function is_user_member_of_blog( $user_id = 0, $blog_id = 0 ) {
    global $wpdb;
 
    $user_id = (int) $user_id;
    $blog_id = (int) $blog_id;
 
    if ( empty( $user_id ) ) {
        $user_id = get_current_user_id();
    }
 
    // Technically not needed, but does save calls to get_site() and get_user_meta()
    // in the event that the function is called when a user isn't logged in.
    if ( empty( $user_id ) ) {
        return false;
    } else {
        $user = get_userdata( $user_id );
        if ( ! $user instanceof WP_User ) {
            return false;
        }
    }
 
    if ( ! is_multisite() ) {
        return true;
    }
 
    if ( empty( $blog_id ) ) {
        
        echo "the blog id is ".$blog_id;
        $blog_id = get_current_blog_id();
    }
 
    $blog = get_site( $blog_id );
 
    if ( ! $blog || ! isset( $blog->domain ) || $blog->archived || $blog->spam || $blog->deleted ) {
        return false;
    }
 
    $keys = get_user_meta( $user_id );
    if ( empty( $keys ) ) {
        return false;
    }
 
    // No underscore before capabilities in $base_capabilities_key.
    $base_capabilities_key = $wpdb->base_prefix . 'capabilities';
    $site_capabilities_key = $wpdb->base_prefix . $blog_id . '_capabilities';
 
    if ( isset( $keys[ $base_capabilities_key ] ) && 1 == $blog_id ) {
        return true;
    }
 
    if ( isset( $keys[ $site_capabilities_key ] ) ) {
        return true;
    }
 
    return false;
}
  
    public function filter_bp_table_prefix( $prefix ) {
        
        $filter_table_prefix = true;
        
        if (!empty(apply_filters(self::return_plugin_namespace().'_filter_bp_table_prefix',$filter_table_prefix))){

        global $wpdb;
        return $wpdb->prefix; //return current blog database prefix instead of site prefix
        
        } else {
            
          return $prefix;
            
        }
    }
    
    public function filter_user_meta_key( $key ) {
        
        $meta_key_array = array(
            'total_friend_count' => true,
            'bp_latest_update' => true,
            'last_activity' => true,
            );
            
        $meta_key_array = apply_filters(self::return_plugin_namespace().'_filter_user_meta_key', $meta_key_array);
        
        if (!empty($meta_key_array[$key])){
            
            $network_id = get_current_blog_id();
            $key_prefix = 'network_'.$network_id.'_';
            return $key_prefix . $key;
            
        } else {
            
            return $key;
        }
       
       
        }

    

    //filter update site option to save the bp-db-version in blog meta and not in the site meta, it will make it per blog instead of per MS install
    public function filter_bpdb_update_version( $value, $oldvalue, $option, $network_id) {
        update_option( 'bp-db-version', $value );
        return $value;
    }
    
    //filter bp-db-version and use get_option insdead of get_site_option
    //this will force bp to consider each blog as having their own db
    public function filter_bpdb_get_version( $val ) {

        $version = get_option( 'bp-db-version' );
        return $version;
    }
    
        /** for 1.7+ user filtering*/
    
    public function users_filter( $query_obj ) {
        
        $filter_users_filter = true;
         
       if (!empty(apply_filters(self::return_plugin_namespace().'_users_filter',$filter_users_filter))){
         
        $uid_where = $query_obj->uid_clauses['where'];
        
        
        $users = self::get_users( get_current_blog_id() );
        
        if( empty( $users ) ) {
            //if no users found, let us fake it
            $users = array( 0 => 0 );
        }
            
         
         $list = "(" . join( ',', $users ) . ")";

         if( $uid_where )
             $uid_where .= " AND u.{$query_obj->uid_name} IN {$list}";
        else
            $uid_where = "WHERE u.{$query_obj->uid_name} IN {$list}";//we are treading a hard line here

         $query_obj->uid_clauses['where'] = $uid_where;   
         
       } else {
           
           return $query_obj;
       }

    }


    //bp1.7+ total user count
    public function filter_total_user_count( $count ) {
            
            $filter_user_count_filter = true;
         
       if (!empty(apply_filters(self::return_plugin_namespace().'_filter_total_user_count',$filter_user_count_filter))){
         
         $count = self::get_network_users_count( get_current_blog_id() );
         return $count;
         //get the total users count for current buddypress network
         
       } else {
           
           
           return $count;
       }
         
        
    }
    
    public function filter_core_userdata($userdata){
        
        
        if (!is_user_member_of_blog($userdata->ID)){
            
            return false;
            
        }
        
        
        return $userdata;
    }
    
    
    public function destroy_user_object_if_non_member(){

        
        
        
        if (function_exists('is_buddypress') && is_buddypress() && is_user_logged_in() && !is_user_member_of_blog(get_current_user_id(), get_current_blog_id()) ){
            

        do_action(self::return_plugin_namespace().'_destroy_user_object_if_non_member');
           
            
        //status_header( 404 );
        //nocache_headers();
        //include( get_query_template( '404' ) );
        //die();
        
        wp_set_current_user( 0 ); 
            
        }
        
        
        
        
    }
    
    public function kill_user_if_bp($user_id){
        
 return false;
        
        
    }
    
public function ensure_user_is_active( $user_id ) {

bp_update_user_last_activity($user_id, bp_core_current_time());


}

public function run_processes(){
    
self::prepare_all_users();    
    
}

    
    
 
 public function plugin_init(){
    
    
        //scope tables by default to the local prefix
        add_filter( 'bp_core_get_table_prefix', array( $this, 'filter_bp_table_prefix' ),10, 1 );
        
        //scope the user meta key
        add_filter( 'bp_get_user_meta_key', array( $this, 'filter_user_meta_key' ) );
        
        //use update_option instead of update_site_option for the bpdb version
        add_filter( 'pre_update_site_option_bp-db-version', array( $this, 'filter_bpdb_update_version' ), 10, 4 );
        
        //use get_option instead of get_site_option for bpdb version
        add_filter( 'site_option_bp-db-version', array( $this, 'filter_bpdb_get_version' ) );
        
        //these functions scope the users to a single site only, except on the main site
        add_action( 'bp_pre_user_query', array( $this, 'users_filter' ) );
        add_filter( 'bp_core_get_active_member_count', array( $this, 'filter_total_user_count' ) );
        
        //add_filter( 'bp_core_get_core_userdata',  array( $this, 'filter_core_userdata' ) ,10,1);
        
        
        //Force 404s for users who are not members
        add_action('template_redirect', array($this, 'destroy_user_object_if_non_member'), 11, 1);
        
        //make sure the user is active on registration
        add_action( 'user_register', array($this,'ensure_user_is_active'));
        add_action( 'personal_options_update', array($this,'ensure_user_is_active'));
        add_action( 'edit_user_profile_update', array($this,'ensure_user_is_active'));
        
        //to attach processes to the ongoing cron job
        add_action( self::return_plugin_namespace().'_process', array($this,'run_processes'));
    
    
}

    /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
    
    
static function on_activate($network_wide) {


    if ( is_multisite() && $network_wide ) { 
        
    $args = array('number' => 500, 'fields' => 'ids');
        
    $sites = get_sites($args);
    
            foreach ($sites as $blog_id) {
            switch_to_blog($blog_id);
            

            
  if (! wp_next_scheduled( self::return_plugin_namespace().'_process' )) {
	$return = wp_schedule_event(time(), 'hourly', 'lh_bmnf_process');
	
    }
    
         restore_current_blog();
        } 
        
        
    } else {


  if (! wp_next_scheduled ( self::return_plugin_namespace().'_process')) {
	wp_schedule_event(time(), 'hourly', self::return_plugin_namespace().'_process');
    }


}
    
}

    
    
    public function __construct() {
        
	 //run our hooks on bp_include loaded to as we may need checks       
    add_action( 'bp_include', array($this,'plugin_init'));
    

        
        
    }


}

$lh_buddypress_multi_network_instance = LH_Buddypress_multi_network_plugin::get_instance();
register_activation_hook(__FILE__, array('LH_Buddypress_multi_network_plugin', 'on_activate'));

}

?>