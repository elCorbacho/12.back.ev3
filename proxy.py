from flask import Flask, request, Response
import requests

app = Flask(__name__)

# Configuración
LOCAL_BASE = '/12.back.ev3/api'
REMOTE_BASE = 'https://www.clinicatecnologica.cl/ipss/tejelanasVivi/api/v1'

@app.route(LOCAL_BASE + '/<path:subroute>', methods=['GET', 'OPTIONS'])
def proxy(subroute):
    # Construir la URL remota completa
    remote_url = f'{REMOTE_BASE}/{subroute}'

    # Reenviar encabezado Authorization si existe
    headers = {'Accept': 'application/json'}
    if 'Authorization' in request.headers:
        headers['Authorization'] = request.headers['Authorization']

    # Reenviar parámetros GET si existen
    params = request.args

    # Enviar la solicitud a la API remota
    try:
        resp = requests.get(remote_url, headers=headers, params=params, timeout=15)
    except requests.RequestException as e:
        return Response(
            response='{"error": "Error al conectar con la API remota."}',
            status=502,
            mimetype='application/json'
        )

    # Devolver la respuesta original
    return Response(
        response=resp.content,
        status=resp.status_code,
        content_type=resp.headers.get('Content-Type', 'application/json')
    )

if __name__ == '__main__':
    app.run(port=8000, debug=True)
