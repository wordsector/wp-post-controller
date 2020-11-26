<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WPPC_Db_Query {
    
    public function get_total_count_by_post($post_id){

        try {
                     
            global $wpdb;
            
            $count = $wpdb->get_var($wpdb->prepare( "SELECT count_number FROM {$wpdb->prefix}wppc_post_views WHERE post_id = %d AND count_period = %s ", $post_id, 'total' ) );
            
            return $count;

        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

    }

    public function get_count_by_period($post_id, $count_type, $count_period){

        try {
                     
            global $wpdb;
            
            $count = $wpdb->get_var($wpdb->prepare( "SELECT count_number FROM {$wpdb->prefix}wppc_post_views WHERE post_id = %d AND count_period = %s AND count_type = %s ", $post_id, $count_period, $count_type ) );
            
            return $count;

        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

    }
    
    public function insert_post_count($post_id, $post_type, $count_type, $count_period, $count_number){

        try{
    
            global $wpdb;
                    
            $wpdb->insert( 
                "{$wpdb->prefix}wppc_post_views", 
                array( 
                    'post_id'         => $post_id,                 
                    'post_type'       => $post_type, 
                    'count_type'      => $count_type, 
                    'count_period'    => $count_period, 
                    'count_number'    => $count_number, 
                ), 
                array('%d', '%s','%s','%s','%d')              
            );
    
            if($wpdb->last_error){            
                return array('status' => 'error', 'message' => $wpdb->last_error);
            }else{
                return array('status' => 'inserted', 'id' => $wpdb->insert_id);            
            }
            
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }

    }

    public function update_count_by_period($post_id, $count_type, $count_period, $count_number){

        try{

            global $wpdb;            
        
            $result = $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->prefix}wppc_post_views SET `count_number` = '{$count_number}' WHERE (`post_id` = %d AND `count_type` = %s AND `count_period` = %s)",
                $post_id,
                $count_type,
                $count_period
            ));

            return $result;

        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

    }

    public function set_post_view($post_id){        

        try {

            $period = array();

            $post_type       = get_post_type();

            $period['year']   = date("Y");
            $period['month']  = date("Y").date("m");
            $period['week']   = date("Y").date("W");
            $period['day']    = date("Y").date("m").date("d");
            $period['all']    = 'total';
            
            foreach ($period as $count_type => $count_period) {

                $count_number    = (int) $this->get_count_by_period($post_id, $count_type, $count_period);
                                                
                if($count_number > 0){
                    $count_number++;
                    $result = $this->update_count_by_period($post_id, $count_type, $count_period, $count_number);
                }else{
                    $count_number++;
                    $result = $this->insert_post_count($post_id, $post_type, $count_type, $count_period, $count_number);
                }
                
            }                        


        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

    }
    
}