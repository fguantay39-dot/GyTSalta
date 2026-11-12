# Base de usuarios con XAMPP

## Puesta en marcha

1. Copiá la carpeta del proyecto a `C:\xampp\htdocs\gyt-nuevo`.
2. Abrí el panel de XAMPP e iniciá **Apache** y **MySQL**.
3. Entrá a `http://localhost/phpmyadmin`, seleccioná **Importar** y cargá `database.sql`.
4. Si tu usuario MySQL no es `root` sin contraseña, actualizá `$dbUser` y `$dbPassword` en `db.php`.
5. Abrí `http://localhost/gyt-nuevo/`.

El registro guarda el hash de la contraseña, nunca la contraseña original. Las tablas se crean en la base `gyt_comunidad`. No abras `index.html` con `file://`: PHP debe ejecutarse desde Apache.