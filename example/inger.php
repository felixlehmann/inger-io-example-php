<?php

Namespace Inger;

/**
 * @author Felix Lehmann
 */
class Inger
{
    private $base_url = "https://inger.io";

    private $inger_version;
    private $vendor;
    private $service;
    private $version;
    private $method;
    private $notify_days_in_advance;

    private $full_url;
    private $response;

    /**
     * Constructor
     *
     * @param  string $inger_version          Inger Version, e.g. v1
     * @param  string $vendor                 Vendor Name, e.g. google
     * @param  string $service                Service Name, e.g. adwords
     * @param  string $version                Version of Service, e.g. v201710
     * @param  string $method                 Method, either "deprecated" or "published"
     * @param  int    $notify_days_in_advance 
     * @return string
     */
    function __construct($inger_version, $vendor, $service, $version, $method, $notify_days_in_advance) {
        $this->inger_version = $inger_version;
        $this->vendor = $vendor;
        $this->service = $service;
        $this->version = $version;
        $this->method = $method;
        $this->notify_days_in_advance = $notify_days_in_advance;

        $this->full_url = $this->buildURL();
    }

    /**
     * Builds the full URL string
     *
     * @return string
     */
    private function buildURL()
    {
        $separator = "/";
        return $this->base_url . $separator . $this->inger_version . $separator . $this->vendor . $separator . $this->service . $separator . $this->version . $separator . $this->method;
    }

    /**
     * Executes call
     *
     * @return void
     */
    public function callAPI()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->full_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($curl);
        $response_code = curl_getinfo($curl)["http_code"];

        curl_close($curl);

        $this->response = [
            "code" => $response_code,
            "data" => $data
        ];
    }

    /**
     * Evaluate response data
     *
     * @param  array  $data Result data
     * @param  int    $days Days
     * @return string
     */
    private function evaluateData($data, $days)
    {
        if (!is_array($data) || !isset($data[0]) || strlen($data[0]) != 10) {
            throw new \Exception("No valid date found");
        }

        if ($this->method == "published") {
            return "Published date: " . $data[0];
        }

        $api_date = date_create($data[0]);
        $now = date_create();

        $interval = date_diff($now, $api_date);
        $diff = $interval->days;

        if ($interval->invert == 1) {
            $diff = -1 * abs($diff);
        }

        if ($diff > $days) {
            return "No action required.";
        }

        if ($diff <= 0) {
            return "API deprecated. Immediate action required.";
        }

        if ($diff > 0 && $diff <= $days) {
            return "API gets deprecated in $diff days. Action required.";
        }
    }

    /**
     * Evaluate Response
     *
     * @return string
     */
    public function evaluateResponse()
    {
        if (!in_array($this->response["code"], [200, 404, 503])) {
            throw new \Exception("Service returned unexpected response code");
        }

        if ($this->response["code"] == 503) {
            // simple retry after one minute
            sleep(60);
            $this->response = $this->callAPI($this->full_url);

            if ($this->response["code"] == 503) {
                throw new \Exception("Service unavailable.");
            }
        }

        if ($this->response["code"] == 404) {
            throw new \Exception(json_decode($this->response["data"], true));
        }

        if ($this->response["code"] == 200) {
            return $this->evaluateData(json_decode($this->response["data"], true), $this->notify_days_in_advance);
        }
    }
}