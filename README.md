# Event Calendar Plugin para WordPress con Divi

Un plugin completo para WordPress que permite administrar eventos de tu compañía con integración total con Divi. Diseñado con un estilo moderno inspirado en Globant.

## Características Principales

### 🎯 Gestión de Eventos

- **Crear y editar eventos** con título, descripción, fecha, hora y lugar
- **Subir flyers** para cada evento
- **Links de registro** e información adicional
- **Estados de eventos** (activo/inactivo)
- **Creación automática de entradas de blog** con Divi

### 👥 Sistema de Attendees

- **Registro de attendees** con nombre, apellido, posición y empresa
- **Validación de email** para evitar registros duplicados
- **Exportación a CSV** de la lista de attendees
- **Emails de confirmación** automáticos

### 🎨 Integración con Divi

- **Módulos personalizados** para Divi Builder
- **Shortcodes** para mostrar eventos en cualquier página
- **Diseño responsive** y moderno
- **Animaciones** y efectos visuales

### 📱 Frontend Moderno

- **Diseño inspirado en Globant** con colores y tipografía profesionales
- **Filtros y búsqueda** de eventos
- **Modales** para detalles y registro
- **Formularios de registro** integrados

## Instalación

1. **Subir el plugin** a la carpeta `/wp-content/plugins/`
2. **Activar el plugin** desde el panel de administración de WordPress
3. **Configurar** el plugin desde el menú "Eventos" en el admin

## Uso

### Panel de Administración

1. **Acceder al menú "Eventos"** en el admin de WordPress
2. **Crear un nuevo evento** con todos los detalles
3. **Subir el flyer** del evento
4. **Configurar links** de registro e información
5. **Activar el evento** para que aparezca en el frontend

### Shortcodes Disponibles

#### Lista de Eventos

```
[ecp_events_list limit="12" future_only="true" show_registration="true" layout="grid" columns="3"]
```

#### Tarjeta de Evento Específico

```
[ecp_event_card event_id="1" show_registration="true"]
```

#### Próximos Eventos

```
[ecp_upcoming_events limit="3" show_registration="true" layout="horizontal"]
```

#### Formulario de Registro

```
[ecp_event_registration event_id="1"]
```

### Módulos de Divi

El plugin incluye tres módulos personalizados para Divi:

1. **Lista de Eventos** - Muestra una grilla de eventos
2. **Tarjeta de Evento** - Muestra un evento específico
3. **Próximos Eventos** - Muestra los próximos eventos en formato compacto

### Configuración

#### Configuración General

- **Eventos por página**: Número de eventos a mostrar por defecto
- **Estado por defecto**: Estado inicial de nuevos eventos
- **Crear blog automáticamente**: Crear entrada de blog automáticamente para nuevos eventos

#### Personalización de Colores

El plugin usa variables CSS que puedes personalizar:

```css
:root {
  --ecp-primary-color: #00d4aa;
  --ecp-secondary-color: #1a1a1a;
  --ecp-accent-color: #ff6b35;
  --ecp-text-color: #333333;
}
```

## Estructura de Archivos

```
event-calendar-plugin/
├── event-calendar-plugin.php          # Archivo principal del plugin
├── includes/
│   ├── class-database.php             # Manejo de base de datos
│   ├── class-admin.php                # Panel de administración
│   ├── class-public.php               # Frontend público
│   ├── class-shortcodes.php           # Shortcodes
│   ├── class-divi-integration.php     # Integración con Divi
│   └── divi/
│       ├── class-et-pb-ecp-events.php
│       ├── class-et-pb-ecp-event-card.php
│       └── class-et-pb-ecp-upcoming-events.php
├── assets/
│   ├── css/
│   │   ├── style.css                  # Estilos del frontend
│   │   └── admin.css                  # Estilos del admin
│   └── js/
│       ├── script.js                  # JavaScript del frontend
│       ├── public.js                  # JavaScript público
│       ├── admin.js                   # JavaScript del admin
│       └── divi.js                    # JavaScript para Divi
└── README.md                          # Este archivo
```

## Base de Datos

El plugin crea dos tablas:

### `wp_ecp_events`

- `id` - ID único del evento
- `title` - Título del evento
- `description` - Descripción del evento
- `event_date` - Fecha y hora del evento
- `location` - Lugar del evento
- `registration_link` - Link de registro
- `info_links` - Links de información adicional
- `flyer_url` - URL del flyer
- `blog_post_id` - ID de la entrada de blog asociada
- `status` - Estado del evento (active/inactive)
- `created_at` - Fecha de creación
- `updated_at` - Fecha de actualización

### `wp_ecp_attendees`

- `id` - ID único del attendee
- `event_id` - ID del evento
- `first_name` - Nombre
- `last_name` - Apellido
- `position` - Posición
- `company` - Empresa
- `email` - Email
- `phone` - Teléfono
- `registration_date` - Fecha de registro
- `status` - Estado del registro

## Hooks y Filtros

### Acciones

- `ecp_event_created` - Se ejecuta cuando se crea un evento
- `ecp_event_updated` - Se ejecuta cuando se actualiza un evento
- `ecp_attendee_registered` - Se ejecuta cuando se registra un attendee

### Filtros

- `ecp_events_query_args` - Modificar argumentos de consulta de eventos
- `ecp_event_display_data` - Modificar datos del evento antes de mostrar
- `ecp_registration_email_subject` - Modificar asunto del email de confirmación
- `ecp_registration_email_message` - Modificar mensaje del email de confirmación

## Personalización

### Agregar Campos Personalizados

```php
// Agregar campo personalizado a eventos
add_filter('ecp_event_fields', function($fields) {
    $fields['custom_field'] = array(
        'label' => 'Campo Personalizado',
        'type' => 'text',
        'required' => false
    );
    return $fields;
});
```

### Personalizar Templates

Puedes sobrescribir los templates creando archivos en tu tema:

```
tu-tema/
├── ecp/
│   ├── event-card.php
│   ├── event-modal.php
│   ├── registration-form.php
│   └── events-list.php
```

## Soporte y Contribuciones

Para soporte técnico o reportar bugs, por favor contacta al desarrollador.

## Changelog

### Versión 1.0.0

- Lanzamiento inicial
- Gestión completa de eventos
- Sistema de attendees
- Integración con Divi
- Shortcodes y módulos personalizados
- Diseño responsive y moderno

## Licencia

Este plugin está licenciado bajo GPL v2 o posterior.

## Créditos

Diseño inspirado en [Globant Events](https://www.globant.com/es/stay-relevant/events)
