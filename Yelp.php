<?php
(! defined('BASEPATH')) and exit('No direct script access allowed');

/*
 * Yelp Client for v3 API (named Yelp Fusion)
 *
 * @author radone@gmail.com 2017
 */
class Yelp
{

    /**
     *
     * @var string
     */
    protected $app_id;

    /**
     *
     * @var string
     */
    protected $app_secret;

    /**
     *
     * @var string
     */
    protected $api_url = 'https://api.yelp.com/v3/';

    /**
     *
     * @var string
     */
    protected $access_token;

    /**
     *
     * @var string
     */
    protected $error = '';

    /**
     *
     * @var string
     */
    protected $response_type = 'json';

    /**
     *
     * @var string
     */
    protected $last_response = '';

    /**
     *
     * @param string $app_id
     * @param string $app_secret
     * @throws Exception
     */
    public function __construct($app_id, $app_secret) 
    {
        if (empty($app_id)) {
            throw new Exception('Yelp: invalid app_id');
        }

        if (empty($app_secret)) {
            throw new Exception('Yelp: invalid app_secret');
        }

        $this->app_id = $app_id;
        $this->app_secret = $app_secret;

        $this->oauth();
    }

    /**
     *
     * @param array $params
     *            [term, location, latitude, longitude, radius ...]
     *            See documentation for full list of params
     */
    public function search(array $params)
    {
        return $this->get('businesses/search', $params);
    }

    /**
     * This endpoint returns the detail information of a business
     *
     * @param string $id
     *            The business id
     * @return mixed|boolean
     */
    public function business($id)
    {
        return $this->get('businesses/' . $id);
    }

    /**
     * This endpoint returns the up to three reviews of a business
     *
     * @param string $id
     *            The business id
     * @return mixed|boolean
     */
    public function reviews($id)
    {
        return $this->get('businesses/' . $id . '/reviews');
    }

    /**
     *
     * @return boolean
     */
    protected function oauth()
    {
        $response = $this->exec('https://api.yelp.com/oauth2/token', 'post', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->app_id,
            'client_secret' => $this->app_secret
        ], [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        if ($response === false) {
            return false;
        }

        if (empty($response['access_token'])) {
            $this->error = 'Invalid access token';
            return false;
        }

        $this->access_token = $response['access_token'];
    }

    /**
     *
     * @param string $cmd
     *            Endpoint uri param for a command e.g. businesess/search
     * @param array $data
     *            (optional)
     * @param array $headers
     *            (optional)
     * @return mixed|boolean
     */
    protected function get($cmd, array $data = [], array $headers = [])
    {
        $headers[] = 'Authorization: Bearer ' . $this->access_token;

        return $this->exec($this->api_url . $cmd, 'get', $data, $headers);
    }

    /**
     *
     * @param string $cmd
     *            Endpoint uri param for a command
     * @param array $data
     *            (optional)
     * @param array $headers
     *            (optional)
     * @return mixed|boolean
     */
    protected function post($cmd, array $data = [], array $headers = [])
    {
        $headers[] = 'Authorization: Bearer ' . $this->access_token;

        return $this->exec($enpoint, $cmd, 'post', $data, $headers);
    }

    /**
     *
     * @param string $url
     *            The URL
     * @param string $method
     *            Allowed values "get" or "post"
     * @param array $data
     *            (optional) Associative array with parameters & values to send
     * @param array $headers
     *            (optional) Extra headers if needed
     * @return mixed array|false
     */
    protected function exec($url, $method = 'get', array $data = [], array $headers = [])
    {
        $this->error = '';

        $ch = curl_init();

        if ($method == 'get') {
            $url .= '?' . http_build_query($data);
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_RETURNTRANSFER => true
        ];

        if (! empty($headers)) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        if ($method == 'post') {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        $this->last_response = $response;

        if ($errno = curl_errno($ch)) {
            $error_message = curl_error($ch);
            $this->error = 'Error: ' . $errno . ': ' . $error_message;
            return false;
        }

        if (! $response) {
            $this->error = 'Error: Invalid response from API';
            return false;
        }

        curl_close($ch);

        if ($this->response_type == 'json') {
            $response = json_decode($response, true);
        }

        if ($response === null) {
            $this->error = 'Error: Could not parse response from API';
            return false;
        }

        return $response;
    }

    /**
     *
     * @return string
     */
    public function get_error()
    {
        return $this->error;
    }

    /**
     *
     * @return string
     */
    public function last_response()
    {
        return $this->last_response;
    }
}