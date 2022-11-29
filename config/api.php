<?php

$billingUrl = env('BILLING_BASE_URL', env('API_URL', 'http://billing-service.test'));
$billingApi = env('BILLING_BASE_API', 'api/billing');
return [
    'billing_url' => "$billingUrl/$billingApi/",
];
