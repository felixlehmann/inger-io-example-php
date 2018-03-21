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

$full_url = buildURL($base_url, $inger_version, $vendor, $service, $version, $method);

print_r(callAPI($full_url));

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