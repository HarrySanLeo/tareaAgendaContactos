# Agenda de contactos con PHP + MySQL

Aplicación web desarrollada en PHP y MySQL para manejar contactos. Cuenta con búsqueda, ordenamiento, categorización y filtro de favoritos.

# 1. URL Pública
- Enlace: `https://tareaagendaharolito.freepage.cc/`

# 2. Capturas de pantalla


# 3. Tecnologias usadas
- Backend: PHP 8
- Base de datos: MySQL
- Frontend: HTML5, Bootstrap 5
- Hosting: InfinityFree

# 4. Pasos para instalar el proyecto de forma local
1. Instalar XAMPP y despues iniciar los servicios Apache y MySQL
2. Copiar la carpeta `agenda_Contactos`
3. Abrir `http://localhost/phpmyadmin`, crear una base de datos llamada `agenda_Contactos` e importar el archivo `database.sql`.
4. Verificar las credenciales en `config/conexion.php` (`host: localhost`, `user: root`, `password: ""`).
5. Abrir `http://localhost/agenda_Contactos` en el navegador.

# 5. Mejoras respecto a la version anterior
- Por motivos personales y de salud que me impidieron completar la práctica previa a tiempo, me disculpo por no contar con una versión anterior. Para cubrir todos los puntos para esta entrega se desarrolló el sistema completo integrando  las operaciones fundamentales y las cuatro mejoras funcionales de la guía
- **Búsqueda multicriterio:** Capacidad de filtrado por nombre, correo o ciudad.
- **Ordenamiento:** Organización de los resultados por nombre, ciudad o fecha de creación.
- **Gestión de categorías:** Tabla `categorias` (Familia, Amigos, Trabajo, Universidad, Otro) vinculada con una llave foránea a cada contacto.
- **Favoritos:** Marcado sencillo con interfaz desde la tabla principal y opción de filtrado para ver solo los contactos favoritos.