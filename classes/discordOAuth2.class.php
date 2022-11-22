<?php

require_once('dotEnv.php');

// start dotEnv reader class
(new DotEnv(__DIR__ . '../../.env'))->load();

class DiscordOauth2
{
    public function __construct()
    {
        // discord url, header & payload
        $this->base_url = getenv('CLIENT_BASE_URL');
        $this->base_header = getenv('CLIENT_BASE_HEADER');
        $this->state = $this->generateState();

        // local redirect url
        $this->redirect_uri = getenv('REDIRECT_URI');

        //client datas
        $this->client_id = getenv('CLIENT_ID');
        $this->client_scope = getenv('CLIENT_SCOPE');
        $this->client_secret = getenv('CLIENT_SECRET');

        $this->guild_id = getenv('CLIENT_GUILD_ID');

        // session data
        $_SESSION['state'] = $this->state;

        $this->generateOauth2URI();
    }

    private function generateOauth2URI()
    {
        try {
            if (!isset($_GET['code'])) return;

            // setting up Discord Oauth2 payload
            $this->rawToken = $_GET['code'];
            $this->oauth2_url = $this->base_url . '/oauth2/token';
            $this->header = '\'Content-Type\': \'application/x-www-form-urlencoded\'';

            $this->data = array(
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "grant_type" => 'authorization_code',
                "code" => $this->rawToken,
                "redirect_uri" => "$this->redirect_uri",
                "scope" => 'identify, guilds',
            );

            // make a request to Discord
            $this->rawData = $this::request(
                $this->oauth2_url,
                'QUERY',
                $this->data
            );

            // set Discord access token
            $this->token_type = $this->rawData->token_type;
            $this->access_token = $this->rawData->access_token;

            $_SESSION['access_token'] = $this->rawData->access_token;

            return 'https://discord.com/api/oauth2/authorize?response_type=code&client_id='
                . $this->client_id . '&redirect_uri='
                . $this->redirect_uri . '&scope='
                . $this->client_scope . '&state='
                . $this->state;
                
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function generateState()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    private static function request()
    {
        extract(func_get_args(), EXTR_PREFIX_ALL, "arg");
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $arg_0);
        if (!isset($arg_1)) return;

        switch ($arg_1) {
            case 'QUERY':
                curl_setopt($connection, CURLOPT_POST, true);
                curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($arg_2));
                break;

            case 'HEADER':
                curl_setopt($connection, CURLOPT_HTTPHEADER, $arg_2);
                break;

            default:
                return;
        }

        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($connection);
        curl_close($connection);
        return json_decode($response);
    }
}
