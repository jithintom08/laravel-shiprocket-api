<?php
namespace Seshac\Shiprocket\Clients;

class ShiprocketClient implements Client
{
    protected $url;
    
    protected $endpoint;

    protected $headers;

    protected $responseType;

    public function __construct()
    {
        $config = (object) config('shiprocket');

        $this->url = $config->url . $config->version .'/';

        $this->responseType = $config->responseType;
    }

    /**
     * set the endpoint
     *
     * @param string $endpoint
     * @return object $this
     */
    public function setEndpoint(string $endpoint) :object
    {
        $this->endpoint = $this->url . $endpoint;

        return $this;
    }

    /**
     * set the header
     *
     * @param string $token
     * @return object
     */
    public function setHeaders(string $token) :object
    {
        $this->headers = [ "Content-Type: application/json" ];
        if ($token != 'login') {
            array_push($this->headers, "Authorization: Bearer {$token}");
        }
        
        return $this;
    }

    /**
     * Send the data using post request
     *
     * @param array $data
     * @return mixed
     */
    public function post(array $data, $type = "POST")
    {
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $this->headers,
        ]);

        $response = curl_exec($curl);

        if (! $this->isValid($response)) {
            $response = json_encode([ 'curl_error' => curl_error($curl) ]);
        }

        curl_close($curl);

        return $this->responseType($response);
    }

    /**
     * Send a data using PATCH Request
     *
     * @param array $data
     * @return void
     */
    public function patch(array $data)
    {
        return $this->post($data, 'PATCH');
    }

    /**
     * get the requested data using get request
     *
     * @return mixed
     */
    public function get()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $this->headers,
        ]);

        $response = curl_exec($curl);

        if (! $this->isValid($response)) {
            $response = json_encode(['curl_error' => curl_error($curl)]);
        }

        curl_close($curl);

        return $this->responseType($response);
    }

    /**
     * Check the return data is valid
     *
     * @param string $string
     * @return bool
     */
    private function isValid(string $string) :bool
    {
        if (! $string) {
            return false;
        }
        
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    
    private function responseType($response)
    {
        if ($this->responseType == 'collection') {
            return collect(json_decode($response, true));
        }
        
        if ($this->responseType == 'object') {
            return json_decode($response);
        }

     
        return json_decode($response, true);
    }
}
