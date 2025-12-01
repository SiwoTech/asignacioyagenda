# Asignación de Operadores y Agenda

Sistema de gestión de operadores con integración de agenda para asignación de tareas.

## Descripción

Esta aplicación permite gestionar operadores y sus asignaciones, incluyendo:
- Alta, baja y edición de operadores
- Asignación de agenda (servicios, horarios, ubicaciones)
- Visualización de calendario por centro de trabajo (Cancún/Playa)

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web Apache/Nginx
- jQuery 3.6+
- Bootstrap 5.x

## Estructura del Proyecto

```
├── api/
│   └── create_agenda.php      # Endpoint API para crear agendas
├── assets/
│   ├── css/
│   │   └── assign-agenda.css  # Estilos del modal de asignación
│   └── js/
│       └── assign-agenda.js   # Lógica JS del modal de asignación
├── agenda/                     # Módulo de calendario
│   ├── cancun/
│   ├── playa/
│   └── locale/
├── componentes/
│   ├── tabla.php              # Tabla de operadores
│   ├── tabla_eliminados.php   # Tabla de operadores eliminados
│   └── buscador.php           # Buscador de operadores
├── control/                    # Control de operaciones
├── php/                        # Scripts PHP backend
├── sql/
│   └── create_agendas_table.sql # Migración SQL para tabla agendas
└── index.php                   # Página principal
```

## Instalación

1. Clonar el repositorio en el servidor web
2. Configurar las credenciales de base de datos en:
   - `php/conexion.php`
   - `agenda/locale/db.php`
   - `control/dbcon.php`
   - `api/create_agenda.php`

3. Ejecutar la migración SQL para crear la tabla de agendas:
   ```sql
   source sql/create_agendas_table.sql
   ```
   O ejecutar manualmente el contenido del archivo en MySQL.

4. Asegurarse de que el servidor web tenga permisos de lectura/escritura.

## Configuración de Base de Datos

El sistema utiliza dos bases de datos:
- `u826340212_orangedb`: Para operadores y datos principales
- `u826340212_agenda`: Para calendario (tablas cancun, playa)

### Tabla de Agendas

La tabla `agendas` almacena las asignaciones con los siguientes campos:
- `id`: ID auto-incremental
- `operator_id`: ID del operador (FK a operadores.id)
- `operator_name`: Nombre del operador
- `date`: Fecha de la asignación
- `start_time`: Hora de inicio
- `end_time`: Hora de fin
- `subject`: Asunto/servicio
- `notes`: Notas adicionales
- `location`: Ubicación (opcional)
- `centro`: Centro de trabajo (Cancun/Playa/Xcaret)
- `color`: Color para el calendario
- `created_at`: Fecha de creación
- `updated_at`: Fecha de actualización

## Funcionalidad de Asignación de Agenda

### Flujo de Uso

1. El usuario navega a la lista de operadores
2. Hace clic en el botón "Asignar Agenda" (icono de calendario) junto al operador
3. Se abre un modal con el formulario de agenda
4. Completa los campos requeridos:
   - Fecha
   - Hora de inicio
   - Asunto/Nota
5. Opcionalmente completa:
   - Hora de fin
   - Ubicación
   - Notas adicionales
   - Centro de trabajo
   - Color
6. Hace clic en "Guardar Agenda y Asignar"
7. El sistema:
   - Valida los campos en frontend
   - Envía la petición AJAX al endpoint
   - Crea la entrada en la tabla `agendas`
   - También inserta en la tabla de calendario correspondiente (cancun/playa)
   - Muestra mensaje de éxito
   - Cierra el modal automáticamente
   - Actualiza la tabla de operadores

### Endpoint API

**POST** `/api/create_agenda.php`

**Content-Type**: `application/json` o `application/x-www-form-urlencoded`

**Campos requeridos**:
- `operator_id` (int): ID del operador
- `operator_name` (string): Nombre del operador
- `date` (string): Fecha en formato YYYY-MM-DD
- `start_time` (string): Hora de inicio en formato HH:MM
- `subject` (string): Asunto o descripción

**Campos opcionales**:
- `end_time` (string): Hora de fin
- `notes` (string): Notas adicionales
- `location` (string): Ubicación
- `centro` (string): Centro de trabajo
- `color` (string): Color en formato hexadecimal

**Respuesta exitosa** (200):
```json
{
  "success": true,
  "message": "Agenda creada exitosamente.",
  "agenda": {
    "id": 1,
    "operator_id": 5,
    "operator_name": "Juan Pérez",
    "date": "2025-01-15",
    "start_time": "09:00",
    "end_time": "12:00",
    "subject": "Limpieza profunda",
    "notes": "",
    "location": "Hotel Iberostar",
    "centro": "Cancun",
    "color": "#E55B26"
  }
}
```

**Respuesta de error** (400/500):
```json
{
  "success": false,
  "error": "Mensaje de error descriptivo"
}
```

## Pruebas Manuales (QA)

### Caso 1: Crear agenda exitosamente

1. Acceder a la aplicación con parámetros de sesión:
   `index.php?a=CWO&b=TestUser&c=10`

2. En la tabla de operadores, localizar un operador activo

3. Hacer clic en el botón de calendario (Asignar Agenda)

4. Verificar que el modal se abre correctamente con:
   - Nombre del operador visible
   - Fecha actual como predeterminada
   - Campos del formulario accesibles

5. Completar el formulario:
   - Fecha: seleccionar una fecha futura
   - Hora inicio: 09:00
   - Hora fin: 12:00
   - Asunto: "Servicio de prueba"
   - Ubicación: "Hotel Test"
   - Notas: "Notas de prueba"

6. Hacer clic en "Guardar Agenda y Asignar"

7. Verificar:
   - Mensaje de éxito aparece
   - Modal se cierra automáticamente
   - La tabla se recarga

8. Verificar en la base de datos:
   ```sql
   SELECT * FROM agendas ORDER BY id DESC LIMIT 1;
   SELECT * FROM cancun ORDER BY id DESC LIMIT 1;
   ```

### Caso 2: Validación de campos requeridos

1. Abrir el modal de asignación para un operador

2. Dejar los campos vacíos y hacer clic en "Guardar"

3. Verificar que aparecen mensajes de validación para:
   - Fecha (requerida)
   - Hora inicio (requerida)
   - Asunto (requerido)

### Caso 3: Validación de hora fin

1. Abrir el modal de asignación

2. Completar hora inicio: 14:00
   Completar hora fin: 10:00

3. Hacer clic en "Guardar"

4. Verificar mensaje de error: "La hora de fin debe ser posterior..."

### Caso 4: Error de conexión

1. Modificar temporalmente las credenciales en `api/create_agenda.php`

2. Intentar crear una agenda

3. Verificar que aparece mensaje de error adecuado

4. Restaurar credenciales correctas

## Solución de Problemas

### El modal no se abre
- Verificar que jQuery está cargado antes de Bootstrap
- Verificar que `assign-agenda.js` está incluido
- Revisar la consola del navegador por errores JS

### Error al guardar agenda
- Verificar credenciales de base de datos
- Verificar que la tabla `agendas` existe
- Revisar permisos de escritura en la BD

### Los estilos no se aplican
- Verificar que `assign-agenda.css` está incluido
- Limpiar caché del navegador

## Revertir Cambios

Si necesita revertir los cambios de esta funcionalidad:

1. Eliminar archivos creados:
   ```bash
   rm -rf api/
   rm -rf assets/js/assign-agenda.js
   rm -rf assets/css/assign-agenda.css
   rm -rf sql/
   ```

2. Restaurar `componentes/tabla.php` desde el respaldo o git:
   ```bash
   git checkout HEAD~1 -- componentes/tabla.php
   ```

3. Restaurar `index.php`:
   ```bash
   git checkout HEAD~1 -- index.php
   ```

4. Eliminar la tabla de agendas (si se desea):
   ```sql
   DROP TABLE IF EXISTS agendas;
   ```

## Archivos Modificados/Creados

| Archivo | Acción | Descripción |
|---------|--------|-------------|
| `api/create_agenda.php` | Nuevo | Endpoint API para crear agendas |
| `assets/js/assign-agenda.js` | Nuevo | Lógica JS del modal |
| `assets/css/assign-agenda.css` | Nuevo | Estilos del modal |
| `sql/create_agendas_table.sql` | Nuevo | Migración SQL |
| `componentes/tabla.php` | Modificado | Agregado botón "Asignar Agenda" |
| `index.php` | Modificado | Incluidos CSS/JS nuevos |
| `README.md` | Nuevo | Documentación del proyecto |

## Contribución

Para contribuir al proyecto:
1. Crear una rama desde `main`
2. Realizar los cambios
3. Probar localmente
4. Crear un Pull Request

## Licencia

Proyecto privado - Clean Work Orange México
