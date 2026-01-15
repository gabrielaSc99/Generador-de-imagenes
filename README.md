# 🎨 Generador de Imágenes con IA

¡Bienvenido al Generador de Imágenes! Una aplicación web sencilla pero potente construida con PHP que te permite dar vida a tus ideas, transformando descripciones de texto en imágenes únicas gracias a la inteligencia artificial.

La aplicación gestiona usuarios, galerías personales y se conecta a la API de **Pollinations.ai** para la generación de las imágenes.

***

## ✨ Características Principales

-   **👤 Autenticación de Usuarios:** Sistema completo de registro e inicio de sesión. ¡Cada usuario tiene su propio espacio!
-   **🖼️ Generador de Imágenes:** Describe la imagen que deseas, elige un estilo y deja que la IA haga su magia.
-   **🎨 Selección de Estilo:** Aplica estilos predefinidos a tus creaciones (Realista, Anime, Pixel Art, Fantasía y más) para darles un toque único.
-   **갤러리 Galería Personal:** Todas las imágenes que generas se guardan en una galería privada, donde puedes verlas y gestionarlas.
-   **💡 Hub de Inspiración:** ¿No sabes qué crear? La sección de inspiración te ofrece ideas de prompts, frases célebres y fondos aleatorios para despertar tu creatividad.
-   **⚙️ Instalador Automático:** Un script de instalación que configura la base de datos y las carpetas necesarias con un solo clic. ¡Puesta en marcha en menos de un minuto!
-   **🗑️ Gestión de Imágenes:** Elimina las imágenes que ya no necesites de tu galería.

## 🛠️ Stack Tecnológico

-   **Backend:** PHP 8+
-   **Base de Datos:** MySQL
-   **Frontend:** HTML5, CSS3 (sin frameworks)
-   **Servidor:** Apache (Recomendado a través de XAMPP/WAMP)
-   **APIs Externas:**
    -   `pollinations.ai` para la generación de imágenes.
    -   `api.quotable.io` para frases de inspiración.
    -   `picsum.photos` para imágenes de fondo aleatorias.

## 🚀 Instalación y Puesta en Marcha

Sigue estos sencillos pasos para instalar el proyecto en tu entorno local.

### Prerrequisitos

Necesitas un entorno de desarrollo local con Apache y MySQL. La forma más fácil de conseguirlo es instalando **[XAMPP](https://www.apachefriends.org/index.html)** o un software similar (WAMP, MAMP, LAMP).

### Pasos de Instalación

1.  **Descargar el Proyecto**
    -   Clona este repositorio o descarga el archivo ZIP y descomprímelo.

2.  **Mover a la Carpeta del Servidor**
    -   Copia la carpeta del proyecto (`GeneradorIMG`) dentro del directorio raíz de tu servidor web. En XAMPP, esta carpeta suele ser `C:/xampp/htdocs/`.

3.  **Iniciar el Servidor**
    -   Abre el panel de control de XAMPP y asegúrate de que los módulos de **Apache** y **MySQL** estén iniciados.

4.  **Ejecutar el Instalador Automático**
    -   Abre tu navegador web y ve a la siguiente URL:
        ```
        http://localhost/GeneradorIMG/instalar.php
        ```
    -   Este script mágico se encargará de todo:
        -   Comprobará la conexión con la base de datos.
        -   Creará la base de datos `generador_imagenes`.
        -   Creará las tablas `usuarios` y `imagenes_generadas`.
        -   Verificará los permisos de las carpetas y las extensiones necesarias como cURL.

5.  **¡Listo!**
    -   Si el instalador muestra todos los checks en verde (✓), la instalación ha sido un éxito. Haz clic en el botón **"Ir a la aplicación"** para empezar a usarla.

## 🔧 Configuración (Opcional)

-   Las credenciales de la base de datos por defecto son `root` sin contraseña, que es lo estándar en XAMPP.
-   Si tu configuración de MySQL es diferente, puedes ajustar las credenciales en dos lugares:
    -   `configuracion.php`: Para el funcionamiento normal de la aplicación.
    -   `instalar.php`: Para que el script de instalación pueda conectarse.

## 📂 Estructura del Proyecto

```
GeneradorIMG/
├── inicio.php              # Punto de entrada principal y enrutador.
├── configuracion.php       # Constantes y ajustes globales.
├── instalar.php            # Script de instalación automática.
├── README.md               # Este archivo.
│
├── mi_base_de_datos/
│   ├── conexion.php        # Funciones para conectar y operar con la BD.
│   └── base_de_datos.sql   # Script SQL de la estructura (para referencia).
│
├── mis_controladores/
│   ├── controlador_usuarios.php   # Lógica de registro, login y sesiones.
│   ├── controlador_imagenes.php   # Lógica para generar, guardar y obtener imágenes.
│   └── controlador_api.php        # Lógica para consumir APIs externas de inspiración.
│
├── mis_vistas/
│   ├── plantillas/         # Partes reutilizables de la web (cabecera, pie).
│   ├── bienvenida.php      # Página de inicio.
│   ├── login.php           # Vista del formulario de login.
│   └── ...                 # Resto de vistas (registro, galería, etc.).
│
├── mis_estilos/
│   └── estilos.css         # Hoja de estilos principal.
│
└── mis_imagenes_generadas/   # Directorio donde se almacenan las imágenes generadas.
```