<?php
require_once __DIR__ . '/../../dbconnect/load_env.php';

$google_client_id = getenv('GOOGLE_CLIENT_ID');
$google_client_secret = getenv('GOOGLE_CLIENT_SECRET');
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$google_redirect_uri = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/HOTEL-MANAGEMENT-SYSTEM/integrations/gmail/googleCallback.php';

if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', $google_client_id);
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', $google_client_secret);
if (!defined('GOOGLE_REDIRECT_URI')) define('GOOGLE_REDIRECT_URI', $google_redirect_uri);

function google_is_configured()
{
    return !empty(GOOGLE_CLIENT_ID) && !empty(GOOGLE_CLIENT_SECRET);
}

?>
