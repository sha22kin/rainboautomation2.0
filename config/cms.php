<?php

return [
    // Public website address, used for "Visit Website" links of the CMS. Website and CMS are one app,
    // so empty = this app's own address (AppServiceProvider). Set WEBSITE_URL to force a domain.
    'website_url' => rtrim((string) env('WEBSITE_URL', ''), '/'),
];
