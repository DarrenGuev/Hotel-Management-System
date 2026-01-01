<?php
if (!defined('PAYPAL_SANDBOX')) define('PAYPAL_SANDBOX', true);

$paypal_client_id = getenv('PAYPAL_CLIENT_ID') ?: 'AascJ7GmBzGZX7LjWFTm_Rlusxk-UnDmrJfQw8Lq6g57DqoDBH_SXEZ2zu_lRCeaNbH6MNgIQW7OJ5SA';
$paypal_secret = getenv('PAYPAL_SECRET') ?: 'ELVyLRLYFMppa5rGskT3Wgknal1atlfBLByk3PyU13_zH8Ny93LEsCrw05BTgi2symc67JyL98nFBnkf';

if (!defined('PAYPAL_CLIENT_ID')) define('PAYPAL_CLIENT_ID', $paypal_client_id);
if (!defined('PAYPAL_SECRET')) define('PAYPAL_SECRET', $paypal_secret);

if (!defined('PAYPAL_API_BASE')) {
    define('PAYPAL_API_BASE', PAYPAL_SANDBOX ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com');
}

function paypal_get_access_token() {
    $clientId = PAYPAL_CLIENT_ID;
    $secret = PAYPAL_SECRET;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_API_BASE . '/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $secret);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err) {
        return ['error' => true, 'message' => $err, 'status' => 500];
    }

    $data = json_decode($response, true);
    if (!$data || !isset($data['access_token'])) {
        return ['error' => true, 'message' => 'Unable to obtain access token', 'status' => $status, 'raw' => $data];
    }

    return ['error' => false, 'access_token' => $data['access_token'], 'expires_in' => $data['expires_in']];
}
