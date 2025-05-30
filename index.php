<?php
// Habilitar CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With");

// Responder a preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuración de rutas base
$localBase = '/12.back.ev3/api';
$remoteBase = 'https://www.clinicatecnologica.cl/ipss/tejelanasVivi/api/v1';

// Obtener la ruta solicitada
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Fix para Authorization en Apache bajo Windows
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if (isset($_SERVER['Authorization'])) {
        $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['Authorization'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $headers['Authorization'];
        }
    }
}

// Verificar si la ruta comienza con la base local
if (strpos($path, $localBase) === 0) {
    $subroute = ltrim(substr($path, strlen($localBase)), '/');
    $externalUrl = rtrim($remoteBase, '/') . ($subroute ? '/' . $subroute : '');

    // Construir headers para la petición externa
    $headers = ["Content-Type: application/json"];
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers[] = "Authorization: " . $_SERVER['HTTP_AUTHORIZATION'];
    }

    $opts = [
        "http" => [
            "method" => $method,
            "header" => implode("\r\n", $headers) . "\r\n",
            "ignore_errors" => true,
            "timeout" => 15
        ]
    ];

    if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
        $input = file_get_contents('php://input');
        $opts["http"]["content"] = $input;
    }

    $context = stream_context_create($opts);
    $response = @file_get_contents($externalUrl, false, $context);

    if ($response === false) {
        http_response_code(502);
        header("Content-Type: application/json");
        echo json_encode([
            'error' => 'No se pudo obtener respuesta del servidor externo.',
            'detalle' => error_get_last()['message'] ?? 'Error desconocido.'
        ]);
        exit;
    }

    // Reenviar headers relevantes de la respuesta externa
    if (isset($http_response_header)) {
        foreach ($http_response_header as $headerLine) {
            if (stripos($headerLine, 'HTTP/') === 0) {
                header($headerLine, true, null);
            } elseif (
                stripos($headerLine, 'Set-Cookie') !== 0 &&
                stripos($headerLine, 'Server') !== 0
            ) {
                header($headerLine, false);
            }
        }
    }

    echo $response;
    exit;
}

// Si la ruta no coincide, error 404
http_response_code(404);
header("Content-Type: application/json");
echo json_encode(['error' => 'Endpoint no encontrado.']);