<?php

require_once('inger.php');

Use Inger\Inger;

/**
 * @author Felix Lehmann
 * Examples
 *     php run.php
 *     php run.php v1 google adwords v201710 deprecated 30
 */

$apis_to_query = [];

// override by parameters, if arguments are set correctly
if (isset($argv) && count($argv) == 7) {
    $apis_to_query[] = [
        'inger_version' => $argv[1],
        'vendor' => $argv[2],
        'service' => $argv[3],
        'version' => $argv[4],
        'method' => $argv[5],
        'notify_days_in_advance' => $argv[6]
    ];
}

// get default values, when cli arguments are incorrect
if (isset($argv) && count($argv) != 7) {
    $apis_to_query[] = [
        'inger_version' => "v1",
        'vendor' => "google",
        'service' => "adwords",
        'version' => "v201710",
        'method' => "deprecated",
        'notify_days_in_advance' => "30"
    ];

    $apis_to_query[] = [
        'inger_version' => "v1",
        'vendor' => "facebook",
        'service' => "graph",
        'version' => "v2.5",
        'method' => "deprecated",
        'notify_days_in_advance' => "30"
    ];

    $apis_to_query[] = [
        'inger_version' => "v1",
        'vendor' => "facebook",
        'service' => "marketing",
        'version' => "v2.5",
        'method' => "published",
        'notify_days_in_advance' => "30"
    ];

    $apis_to_query[] = [
        'inger_version' => "unknown",
        'vendor' => "unknown",
        'service' => "unknown",
        'version' => "unknown",
        'method' => "unknown",
        'notify_days_in_advance' => "30"
    ];
}

foreach ($apis_to_query as $api) {
    try {
        $inger_instance = new \Inger\Inger(
            $api["inger_version"],
            $api["vendor"],
            $api["service"],
            $api["version"],
            $api["method"],
            $api["notify_days_in_advance"]
        );

        $inger_instance->callAPI();
        $result = $inger_instance->evaluateResponse();

        // TODO: Do something with the result - it's your own decision
        // Send email
        // Print output
        print($api["vendor"] . " - " . $api["service"] . " - " . $api["version"] . ": " . $result . "\n");
        // Push Notification
        // ...
    } catch (Exception $e) {
        print($e->getMessage() . "\n");
    }
}

exit(0);
