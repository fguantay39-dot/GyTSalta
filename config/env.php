<?php
/**
 * config/env.php
 * Cargador de Variables de Entorno y Configuración Global (.env)
 * Trabajo Práctico N° 4 - Programación Web
 */

if (!function_exists('cargar_variables_entorno')) {
    function cargar_variables_entorno(string $archivo_env): void {
        if (!file_exists($archivo_env)) {
            return;
        }

        $lineas = file($archivo_env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lineas === false) {
            return;
        }

        foreach ($lineas as $linea) {
            $linea = trim($linea);

            // Ignorar líneas vacías y comentarios
            if ($linea === '' || str_starts_with($linea, '#') || str_starts_with($linea, ';')) {
                continue;
            }

            // Separar clave=valor
            if (str_contains($linea, '=')) {
                [$clave, $valor] = explode('=', $linea, 2);
                $clave = trim($clave);
                $valor = trim($valor);

                // Quitar comillas simples o dobles envolventes
                if (
                    (str_starts_with($valor, '"') && str_ends_with($valor, '"')) ||
                    (str_starts_with($valor, "'") && str_ends_with($valor, "'"))
                ) {
                    $valor = substr($valor, 1, -1);
                }

                // Guardar en putenv, $_ENV y $_SERVER si no existen
                if (!array_key_exists($clave, $_SERVER) && !array_key_exists($clave, $_ENV)) {
                    putenv("{$clave}={$valor}");
                    $_ENV[$clave] = $valor;
                    $_SERVER[$clave] = $valor;
                }
            }
        }
    }
}

// Cargar archivo .env desde la raíz del proyecto
cargar_variables_entorno(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

// Constantes globales de la aplicación
if (!defined('APP_NAME')) {
    define('APP_NAME', getenv('APP_NAME') ?: 'Club Atlético Gimnasia y Tiro de Salta');
}
if (!defined('APP_EMAIL')) {
    define('APP_EMAIL', getenv('APP_EMAIL') ?: 'contacto@gimnasiaytiro.com.ar');
}
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'local');
}
if (!defined('APP_URL')) {
    define('APP_URL', getenv('APP_URL') ?: 'http://localhost/gyt-nuevo');
}

// Variables globales para renderizado SSR
$nombre_sitio   = APP_NAME;
$email_contacto = APP_EMAIL;
$entorno_app    = APP_ENV;
$url_base       = APP_URL;
