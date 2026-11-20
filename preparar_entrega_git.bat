@echo off
echo ========================================================
echo Preparando rama y commits para el Trabajo Practico N 4
echo ========================================================

cd /d "C:\xampp\htdocs\gyt-nuevo"

echo 1. Creando y cambiando a la rama feature/migracion-php-ssi...
git checkout -b feature/migracion-php-ssi 2>nul || git checkout feature/migracion-php-ssi

echo 2. Agregando archivos al area de preparacion...
git add .gitignore .env.example config/ includes/ *.php style.css script.js README.md index.html tp4/

echo 3. Realizando commits atomicos para TP4...
git commit -m "feat: implementa SSI en includes (header, nav, footer) y configuracion .env con soporte SSR"

echo 4. Volviendo a la rama main y fusionando la rama...
git checkout main
git merge feature/migracion-php-ssi -m "Merge branch 'feature/migracion-php-ssi' into main"

echo 5. Subiendo cambios a GitHub...
git push origin main
git push origin feature/migracion-php-ssi

echo ========================================================
echo Listo! Los cambios del TP4 estan preparados y commiteados.
echo ========================================================
pause
