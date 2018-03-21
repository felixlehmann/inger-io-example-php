<?php

/**
 * @author Felix Lehmann
 * Examples
 *     php inger.php
 *     php inger.php v1 google adwords v201710 deprecated
 */

$base_url = "https://inger.io";
// default example values
$inger_version = "v1";
$vendor = "google";
$service = "adwords";
$version = "v201710";
$method = "deprecated";
$notify_days_in_advance = 30;

if (isset($argv) && count($argv) == 6) {
    $inger_version = $argv[1];
    $vendor = $argv[2];
    $service = $argv[3];
    $version = $argv[4];
    $method = $argv[5];
}

$full_url = buildURL($base_url, $inger_version, $vendor, $service, $version, $method);

$data = json_decode(callAPI($full_url), true);
evaluateData($data, $notify_days_in_advance);

exit(0);

/**
 * Builds the full URL string
 *
 * @param  $base_url      string Base URL
 * @param  $inger_version string Inger Version, e.g. v1
 * @param  $vendor        string Vendor Name, e.g. google
 * @param  $service       string Service Name, e.g. adwords
 * @param  $version       string Version of Service, e.g. v201710
 * @param  $method        string Method, either "deprecated" or "published"
 * @return string
 */
function buildURL($base_url, $inger_version, $vendor, $service, $version, $method)
{
    $separator = "/";
    return $base_url . $separator . $inger_version . $separator . $vendor . $separator . $service . $separator . $version . $separator . $method;
}

/**
 * Executes call
 *
 * @param  $url string Full URL
 * @return mixed
 */
function callAPI($url)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

/**
 * Evaluate result
 *
 * @param  array $data Result data
 * @param  int   $days Days
 * @return void
 */
function evaluateData($data, $days)
{
    if (!is_array($data) || !isset($data[0]) || strlen($data[0]) != 10) {
	print("No valid date found");
    }

    $api_date = date_create($data[0]);
    $now = date_create();

    $interval = date_diff($now, $api_date);
    $diff = $interval->days;

    if ($interval->invert == 1) {
	$diff = -1 * abs($diff);
    }

    if ($diff > $days) {
	printf("No action required.");
    }

    if ($diff <= 0) {
	printf("API deprecated. Immediate action required.");
    }

    if ($diff > 0 && $diff <= $days) {
	printf("API gets deprecated in $diff days. Action required.");
    }
}