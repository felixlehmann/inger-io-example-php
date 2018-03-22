# inger-io-example-php
Inger.io example for php usage

Uses curl within PHP.
Performs an automatic retry when the service is unavailable.

Usage:
php run.php {inger_version} {vendor} {api} {version} {method} {days_treshold}

{inger_version}
Inger.io API Version

{vendor}
Vendorname like facebook, google, microsoft

{api}
Apiname like graph, for the facebook graph api

{version}
Apiversion like v2.5, for the graph api of facebook

{method}
Either "deprecated" or "published", to get the deprecation date or published date

{days_treshold}
Show warning if deprecation date is closer than {days_threshold}

```
git clone https://github.com/felixlehmann/inger-io-example-php.git
cd example && php run.php
cd example && php run.php v1 google adwords v201710 deprecated 30
```