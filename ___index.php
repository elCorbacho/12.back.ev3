<?php
// Permitir solicitudes CORS desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos GET, POST, OPTIONS, PUT, DELETE
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// Permitir los encabezados Content-Type y Authorization
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Comprobar si la solicitud es de tipo OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Finalizar la ejecución y enviar las cabeceras
    exit(0);
}

// URL de la API externa
$apiUrl = "https://www.clinicatecnologica.cl/ipss/tejelanasVivi/api/v1";

// Inicializar cURL para la solicitud
$ch = curl_init($apiUrl);

// Configurar opciones de cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    // Agregar aquí el token si la API lo requiere:
    // 'Authorization: Bearer TU_TOKEN'
]);

// Ejecutar la solicitud
$response = curl_exec($ch);

// Comprobar errores en cURL
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al conectar con la API externa.",
        "detalle" => curl_error($ch)
    ]);
    exit();
}

// Cerrar la conexión cURL
curl_close($ch);

// Mostrar la respuesta JSON al cliente
header('Content-Type: application/json; charset=utf-8');
echo $response;
?>