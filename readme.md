# 12.back.ev3 - Proxy API PHP + Swagger UI

Este proyecto es un **proxy PHP** que permite consumir una API externa desde `localhost` evitando problemas de CORS, y además incluye documentación interactiva con Swagger UI.

## 🚀 ¿Qué hace este proyecto?

- Expone endpoints locales bajo `/12.back.ev3/api/*` que redirigen a la API remota de Clínica Tecnológica.
- Permite probar la API desde Swagger UI en tu navegador.
- Permite autenticación Bearer para endpoints protegidos.
- Incluye endpoints locales como `/about-us` y `/faq` si los defines en tu proxy.

## 📦 Instalación

1. Clona o copia este repositorio en tu carpeta de XAMPP:  
   `c:\xampp\htdocs\12.back.ev3\`

2. Asegúrate de tener habilitado `mod_rewrite` y `AllowOverride All` en Apache.

3. Verifica que el archivo `.htaccess` esté presente y contenga:
    ```apache
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^api/.*$ index.php [QSA,L]
    ```

4. Reinicia Apache desde el panel de XAMPP.

## 🛠 Uso

- Accede a la documentación Swagger UI en:  
  [http://localhost/12.back.ev3/swagger-ui/](http://localhost/12.back.ev3/swagger-ui/)

- Realiza peticiones a la API local, por ejemplo:  
  ```
  GET http://localhost/12.back.ev3/api/products-services
  GET http://localhost/12.back.ev3/api/about-us
  GET http://localhost/12.back.ev3/api/faq
  ```

- Para endpoints protegidos, usa el botón **Authorize** en Swagger UI e ingresa tu token Bearer.

## 📝 Estructura principal

- `index.php` — Proxy PHP que reenvía las peticiones y maneja CORS.
- `swagger.json` — Documentación OpenAPI de tus endpoints.
- `.htaccess` — Reglas de Apache para redirigir todas las rutas `/api/*` a `index.php`.

## 🔒 Seguridad

- No expongas este proxy a internet sin protección.
- El token Bearer se reenvía tal cual al backend remoto.

## 🧩 Personalización

- Puedes agregar endpoints locales (por ejemplo, `/about-us`) directamente en `index.php`.
- Modifica `swagger.json` para documentar tus endpoints y ejemplos.

## 🆘 Problemas comunes

- **404 Not Found:**  
  Verifica que `.htaccess` esté correcto y que Apache tenga `mod_rewrite` y `AllowOverride All` habilitados.

- **CORS:**  
  El proxy PHP ya incluye los headers necesarios.

- **401 Unauthorized:**  
  Asegúrate de usar el token correcto en el header `Authorization`.

---

**Desarrollado para pruebas y documentación local de APIs.**