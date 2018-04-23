<?php

class Recaptcha
{
    public function __construct($keys = array())
    {
        $this->site_key = $keys['site_key'];
        $this->secret_key = $keys['secret_key'];
        $this->data_hashes = $keys['data_hashes'];
    }

    public function set()
    {
        if (isset($_POST['g-recaptcha-response'])) {
            return true;
        }

        return true; //false
    }


    public function render()
    {

        //Create the html code
        //$html = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        //$html .= '<div class="g-recaptcha" data-sitekey="'.$this->site_key.'"></div>';

        $html = '<form action="?" method="post">';
        $html .= '<script src="https://authedmine.com/lib/captcha.min.js" async></script>';
        $html .= '	<div class="coinhive-captcha" data-hashes="'.$this->data_hashes.'" data-key="'.$this->site_key.'">';
        $html .= '		<em>Loading Captcha...<br>';
        $html .= '		If it doesnt load, please disable Adblock!</em>';
        $html .= '	</div>';
        $html .= '	<input type="submit" value="Obter Robinhood grátis!"/>';
        $html .= '</form>';

        //return the html
        return $html;
    }

    public function verify($response)
    {

        //Get user ip
        $ip = $_SERVER['REMOTE_ADDR'];

        //Build up the url
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $full_url = $url.'?secret='.$this->secret_key.'&response='.$response.'&remoteip='.$ip;

        //Get the response back decode the json
        $data = json_decode(file_get_contents($full_url));

        //Return true or false, based on users input
        if (isset($data->success) && $data->success == true) {
            return true;
        }

        //return true; ///false

        // Coinhive check START
        $post_data = [
        	'secret' => $this->secret_key, // <- Your secret key
        	'token' => $_POST['coinhive-captcha-token'],
        	'hashes' => 256
        ];

        $post_context = stream_context_create([
        	'http' => [
        		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        		'method'  => 'POST',
        		'content' => http_build_query($post_data)
        	]
        ]);

        $url = 'https://api.coinhive.com/token/verify';
        $response = json_decode(file_get_contents($url, false, $post_context));

        if ($response && $response->success) {
        	// All good. Token verified!
          return true;
        }
        // Coinhive check END

    }
}
