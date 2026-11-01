<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dokumentasi API - Swagger</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body {
            margin: 0;
            background: #f8fafc;
        }

        .topbar {
            display: none;
        }

        #swagger-ui {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        function readCookie(name) {
            const cookie = document.cookie
                .split('; ')
                .find(row => row.startsWith(name + '='));

            return cookie ? decodeURIComponent(cookie.split('=').slice(1).join('=')) : null;
        }

        window.onload = function () {
            SwaggerUIBundle({
                url: @json(asset('docs/openapi.json')),
                dom_id: '#swagger-ui',
                deepLinking: true,
                persistAuthorization: false,
                displayRequestDuration: true,
                withCredentials: true,
                requestInterceptor: function (request) {
                    if (request.url) {
                        request.url = request.url.replace(/^https?:\/\/(127\.0\.0\.1|localhost):8000/i, window.location.origin);
                    }

                    request.credentials = 'same-origin';
                    request.headers = request.headers || {};

                    const pathname = new URL(request.url, window.location.origin).pathname;
                    const csrfExempt = ['/api/auth/login', '/api/auth/logout'].includes(pathname);

                    if (!csrfExempt && !['GET', 'HEAD', 'OPTIONS'].includes((request.method || '').toUpperCase())) {
                        request.headers['X-CSRF-TOKEN'] = readCookie('XSRF-TOKEN') || @json(csrf_token());
                        request.headers['X-Requested-With'] = 'XMLHttpRequest';
                    }

                    request.headers['Accept'] = 'application/json';

                    return request;
                },
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: 'BaseLayout'
            });
        };
    </script>
</body>
</html>
