<?php

require plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
use  Orhanerday\OpenAi\OpenAi ;

class WP_Rest_Route {
    public function __construct() {
        add_action('rest_api_init', [$this, 'create_rest_routes']);        
        $this->api_key = get_option('wpwrtblk_api_key');    
    }

    public function create_rest_routes(){          
        register_rest_route('wpwrtblk/v1','/api_key', [
            'methods' => 'GET',
            'callback' => [$this, 'get_api_key'],
            'permission_callback' => function() {                                 
                return current_user_can( 'manage_options' );
            }
        ]);
        
        register_rest_route('wpwrtblk/v1','/api_key', [
            'methods' => 'POST',
            'callback' => [$this, 'save_api_key'],
            'permission_callback' => function() {                
                return current_user_can( 'manage_options' );
            }
        ]);

        register_rest_route('wpwrtblk/v1','/settings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_settings'],
            'permission_callback' => function() {                                 
                return current_user_can( 'edit_posts' );
            }
        ]);

        register_rest_route('wpwrtblk/v1','/settings', [
            'methods' => 'POST',
            'callback' => [$this, 'save_settings'],
            'permission_callback' => function() {                
                return current_user_can( 'manage_options' );                
            }
        ]);

        register_rest_route('wpwrtblk/v1','/check_key', [
            'methods' => 'POST',
            'callback' => [$this, 'check_key'],
            'permission_callback' => function() {                                
                return current_user_can( 'edit_posts' );
            }
        ]);

        register_rest_route('wpwrtblk/v1','/playground', [
            'methods' => 'GET',
            'callback' => [$this, 'playground'],
            'permission_callback' => function() {                                                     
                return true;
                //return current_user_can( 'edit_posts' );
            }
        ]);
    }

    public function get_api_key (){
        $api_key = get_option('wpwrtblk_api_key');
        $response = [
            'apiKey' => $api_key
        ];                     
        return rest_ensure_response($response);
    }

    public function save_api_key ($req){
        $api_key = sanitize_text_field($req['apiKey']);
        update_option('wpwrtblk_api_key',$api_key);
        return rest_ensure_response('success');        
    }

    public function get_settings (){
        $settings = get_option('wpwrtblk_api_settings');
        $response = [
            'settings' => $settings
        ];                     
        return rest_ensure_response($response);
    }

    public function save_settings ($req){        
        $model = sanitize_text_field($req['model']);
        $settings = array(                        
            '0' => $model,	                # model -- will need to fix this when chatgpt model comes out
            '1' => intval($req['tokens']), 				# tokens
            '2' => floatval($req['temp']),			    # temp
            '3' => floatval($req['top_p']), 			# top_p
            '4' => floatval($req['presence_penalty']), 	# presence_penalty
            '5' => floatval($req['frequency_penalty']), # frequency_penalty
            '6' => 1, 					                # n - hard coded on purpose
            '7' => 1			                        # best_of - hard coded on purpose
        );
        update_option('wpwrtblk_api_settings',$settings);
        return rest_ensure_response('success');        
    }

    public function check_key ($req){                        
        $open_ai = new OpenAi( $req['apiKey'] );                                        
        $response = $open_ai->listModels();               
        $res = json_decode($response);
        return $res;
    }

    public function playground ($req){        
        $open_ai = new OpenAi( $this->api_key );                        
        $keywords = $req->get_param( 'keywords' );
        $text = $keywords;
        $tokens = intval($req->get_param( 'tokens' ));
        $model = $req->get_param('model');

        // if ($model == 'gpt-3.5-turbo' or $model == 'gpt-4'){
        $complete = $open_ai->chat([
            'messages' => [
                [
                    "role" => "user", 
                    "content" => $req->get_param('prompt')
                ]
            ],
            'model' => $req->get_param('model'),  
            'max_tokens' => intval($req->get_param('tokens')),          
            'temperature' => floatval($req->get_param('temp')),
            'top_p' => floatval($req->get_param('top_p')),
            'presence_penalty' => floatval($req->get_param('presence_penalty')),
            'frequency_penalty' => floatval($req->get_param('frequency_penalty')),
            'n' => 1, # the plugin will only return 1 option
            //'best_of' => intval($req->get_param('best_of'))
            ]);  
        $res = json_decode($complete);                    
        return $res;
        // } else {
        //     $complete = $open_ai->completion([
        //         'prompt' => $req->get_param('prompt'),            
        //         'model' => $req->get_param('model'),  
        //         'max_tokens' => intval($req->get_param('tokens')),          
        //         'temperature' => floatval($req->get_param('temp')),
        //         'top_p' => floatval($req->get_param('top_p')),
        //         'presence_penalty' => floatval($req->get_param('presence_penalty')),
        //         'frequency_penalty' => floatval($req->get_param('frequency_penalty')),
        //         'n' => 1, # the plugin will only return 1 option
        //         'best_of' => intval($req->get_param('best_of'))
        //      ]);
        //     $res = json_decode($complete);        
        //     return $res;
        // }
    }
}       
new WP_Rest_Route();