<?php
// config/supabase.php

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

function supabaseGet(string $table, array $params = []): array {
    $url = rtrim($_ENV['SUPABASE_URL'], '/') . '/rest/v1/' . $table;
    $key = $_ENV['SUPABASE_ANON_KEY'];

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "apikey: $key",
            "Authorization: Bearer $key",
            "Content-Type: application/json",
        ],
    ]);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        throw new Exception("Supabase error $status: $response");
    }

    return json_decode($response, true) ?? [];
}