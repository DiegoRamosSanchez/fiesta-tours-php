<?php
// testimonios/index.php

require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/supabase.php';

setCors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ── Parámetros ────────────────────────────────────────
$lang  = $_GET['lang']  ?? 'es';
$limit = min(max((int)($_GET['limit'] ?? 20), 1), 50);

if (!in_array($lang, ['es', 'en', 'pt'])) $lang = 'es';

$textCol = "text_$lang";

// ── Consulta ──────────────────────────────────────────
try {
    $rows = supabaseGet('testimonios', [
        'visible' => 'eq.true',
        'order'   => 'created_at.asc',
        'limit'   => $limit,
        'select'  => "slug,author,company,role_key,role_default,{$textCol},rating",
    ]);

    $data = array_map(fn($r) => [
        'slug'         => $r['slug'],
        'author'       => $r['author'],
        'company'      => $r['company'] ?? null,
        'role_key'     => $r['role_key'] ?? null,
        'role_default' => $r['role_default'] ?? '',
        'text'         => $r[$textCol] ?? $r['text_es'] ?? '',  // fallback a ES si columna vacía
        'rating'       => (int)($r['rating'] ?? 5),
    ], $rows);

    echo json_encode([
        'data'  => $data,
        'total' => count($data),
        'lang'  => $lang,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}