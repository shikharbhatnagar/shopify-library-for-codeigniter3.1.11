<?php

class App extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
    }

    public function access(){

        $shop = $this->input->get('shop'); //SHOP URL
       
        if(isset($shop)){
            $this->session->set_userdata($shop);
        }

        if(($this->session->userdata('access_token'))){
            $data = array(
                'api_key' => $this->config->item('shopify_api_key'),
                'shop' => $shop
            );

            $this->load->view('welcome',$data);
        }
        else{

            $this->auth($shop);
        }
    }

    public function auth($shop){

        $data = array(
            'API_KEY' => $this->config->item('shopify_api_key'),
            'API_SECRET' => $this->config->item('shopify_secret'),
            'SHOP_DOMAIN' => $shop,
            'ACCESS_TOKEN' => ''
        );
        
        $this->load->library('Shopify' , $data); //load shopify library and pass values in constructor

        //what app can do 
        $scopes = array('read_content', 'write_content', 'read_themes', 'write_themes', 'read_script_tags', 'write_script_tags','read_products','read_customers','write_customers','write_products','read_products','read_orders','write_orders','read_product_listings','write_product_listings','read_product_listings','write_product_listings'); ,'read_publications','write_publications'

        //URL
        $redirect_url = 'https://Ng_Rock_Url/App/authCallback';
        
        $paramsforInstallURL = array(
            'scopes'    =>  $scopes,
            'redirect'  =>  $redirect_url
        );

        $permission_url = $this->shopify->installURL($paramsforInstallURL);

        $this->load->view('escapeIframe', ['installUrl' => $permission_url]);

    }

    public function authCallback(){

        $code = $this->input->get('code');
        $shop = $this->input->get('shop');

        if(isset($code)){

            $data = array(
                'API_KEY' => $this->config->item('shopify_api_key'),
                'API_SECRET' => $this->config->item('shopify_secret'),
                'SHOP_DOMAIN' => $shop,
                'ACCESS_TOKEN' => ''
            );
            
            $this->load->library('Shopify' , $data); //load shopify library and pass values in constructor
        
        }

        $accessToken = $this->shopify->getAccessToken($code);

        if(!empty($accessToken)){

            $_SESSION['oauth_token']  = $accessToken;
            
            $this->createWebhook($shop);

            echo "Access token received: ".$accessToken;
        }

    }

    public function createWebhook($shop){
        
        $token = $_SESSION['oauth_token'];
        
        $url = 'https://' . $shop . '/admin/api/2022-23/webhooks.json';
        
        $headers = array('X-Shopify-Access-Token: '. $token,'Content-Type: application/json;');
        
        $reg_store_webhook = "";
        
        $orderscreate_webhook    =   array(
                            "webhook"   =>  array (
                            "topic"     => "orders/create",
                            "address"   => base_url()."Auth/getOrderDetailsWhenOrderCreatedAtShopify/".$shop,
                            "format"    =>  "json"
                            )
        );

        $result = shopify_curl(json_encode($orderscreate_webhook), 'POST', $url, $token);

        $webhook_response = json_decode($result, TRUE);
     
        echo " : <pre>"; print_r($webhook_response); echo "</pre>"; 

    }

}