# Exit Intent Mailchimp Popup

Plugin de WordPress que muestra un formulario de suscripción de Mailchimp cuando el usuario intenta abandonar el sitio o cuando se hace clic en un elemento con el ID `#popactive`.

## Configuración

1. Instala y activa el plugin.
2. Ve a **Ajustes > Exit Intent Mailchimp** y añade tu *API Key* y *List ID* de Mailchimp.

## Personalización

- Puedes editar el HTML del popup en `templates/popup.php`.
- Puedes modificar los estilos en `assets/popup.css`.

## Integración

El formulario sólo solicita el correo electrónico y envía los datos directamente a Mailchimp mediante su API.

## Comportamiento

- El popup aparece una sola vez por sesión al detectar un intento de salida.
- Puede cerrarse y no volverá a mostrarse automáticamente hasta la siguiente visita.
- Cada clic sobre un elemento con el ID `#popactive` mostrará el popup nuevamente, sin importar si ya se cerró previamente.
