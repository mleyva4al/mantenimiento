# Sistema de Dictámenes – Proyecto Académico

Este proyecto corresponde a un **Sistema de Dictámenes** desarrollado como parte de un proyecto de clase de la carrera de **Ingeniería en Tecnologías del Internet**.

El sistema fue diseñado para **gestionar dictámenes por delegaciones** (por ejemplo: Colima, Manzanillo y Tecomán), simulando un entorno real de trabajo administrativo y técnico.

---

## Objetivo del proyecto

Desarrollar una aplicación web funcional que permita:

* Gestionar dictámenes de forma centralizada.
* Controlar accesos mediante **roles de usuario**.
* Mantener **seguridad, trazabilidad y persistencia de datos**.
* Exportar información en distintos formatos.

---

## Roles de usuario

El sistema implementa **control estricto de perfiles**, con separación total de funcionalidades y vistas:

### Administrador

* Gestión completa del sistema.
* Alta, edición y eliminación lógica de dictámenes.
* Visualización de:

  * Fecha y hora de creación de dictámenes.
  * Motivos de modificación o eliminación.
  * Historial de acciones.
* Gestión de usuarios.
* Exportación de datos a **PDF, Excel y TXT**.

### Técnico

* Acceso **únicamente** a las funciones asignadas a su perfil.
* Interfaz completamente diferente a la del administrador.
* **Nunca** puede visualizar ni acceder a las opciones del administrador bajo ninguna circunstancia.

> La separación de roles es total: pantallas, permisos y rutas son distintas para cada perfil.

---

## Seguridad

* Sistema de **login con usuario y contraseña**.
* Contraseñas cifradas mediante **hash**.
* Control de sesiones.
* Persistencia de datos:

  * Los registros no se eliminan físicamente de la base de datos.
  * Incluso si un dictamen es eliminado, permanece almacenado para auditoría.

---

## Funcionalidades principales

* CRUD completo (Crear, Leer, Actualizar y Eliminar).
* Gestión por delegaciones.
* Exportación de información a:

  * PDF
  * Excel
  * Archivo de texto
* Generación de reportes.
* Registro de acciones y sesiones.

---

## Tecnologías utilizadas

* **PHP** (backend)
* **HTML** integrado mediante scripts PHP
* **SQL** (base de datos)
* **XAMPP** (entorno local)
* **Composer** (dependencias)
* **TCPDF** (generación de PDFs)

> 🎨 Los estilos visuales fueron generados con apoyo de **inteligencia artificial**.

---

## Instalación (entorno local)

1. Clonar el repositorio:

   ```bash
   git clone https://github.com/tu-usuario/tu-repositorio.git
   ```
2. Colocar el proyecto en la carpeta `htdocs` de XAMPP.
3. Importar la base de datos SQL.
4. Configurar el archivo de conexión a la base de datos.
5. Acceder desde el navegador:

   ```
   http://localhost/mantenimiento
   ```

---

##  Notas

Este proyecto tiene fines **académicos**, pero está inspirado en escenarios reales de gestión administrativa y técnica, aplicando buenas prácticas de seguridad, control de acceso y persistencia de datos.

---

**Desarrollado por:** Mireya Leyva
