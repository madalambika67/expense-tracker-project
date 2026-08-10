<?php
// Base URL of the Python (Flask) backend API
define('API_BASE_URL', 'http://localhost:5000');

/**
 * Makes an HTTP request to the Python backend API and returns the decoded response.
 *
 * @param string $method   GET, POST, PUT, DELETE
 * @param string $endpoint e.g. '/expenses'
 * @param array|null $data payload to send as JSON
 * @return array decoded JSON response (or an 'error' key on failure)
 */
function api_request($method, $endpoint, $data = null) {
    $ch = curl_init(API_BASE_URL . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['error' => 'Could not reach the backend API. Make sure the Python server is running on ' . API_BASE_URL . '. (' . $error . ')'];
    }

    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : ['error' => 'Invalid response from API'];
}

function format_money($amount) {
    return '$' . number_format((float)$amount, 2);
}
