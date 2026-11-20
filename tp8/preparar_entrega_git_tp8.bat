@echo off
title Preparar Entrega TP8 - Gimnasia y Tiro de Salta
echo ============================================================================
echo   TRABAJO PRACTICO N 8: AUTENTICACION, SESIONES Y ROLES (OWASP)
echo   Club Atletico Gimnasia y Tiro de Salta - Alumno: Franco Guantay
echo ============================================================================

cd /d "C:\xampp\htdocs\gyt-nuevo"

echo.
echo [1/6] Creando o cambiando a la rama feature/tp8-autenticacion-sesiones...
git checkout -b feature/tp8-autenticacion-sesiones 2>nul || git checkout feature/tp8-autenticacion-sesiones

echo.
echo [2/6] Commit 1: Base de datos y esquema de usuarios para TP8...
git add tp8/schema_usuarios.sql
git commit -m "feat(db): agrega esquema de usuarios con soporte para roles y hashing bcrypt (TP8)"

echo.
echo [3/6] Commit 2: Modulos de Registro, Login, Logout y Guardian de Rutas...
git add auth_guard.php includes/auth_guard.php registro.php login.php logout.php
git commit -m "feat(auth): implementa registro seguro, login con session_regenerate_id, logout y auth_guard (TP8)"

echo.
echo [4/6] Commit 3: Vistas protegidas dashboard y admin_panel con control RBAC...
git add dashboard.php admin_panel.php includes/header.php includes/nav.php
git commit -m "feat(views): anade dashboard de socio, admin_panel restringido e integracion en SSI (TP8)"

echo.
echo [5/6] Commit 4: Documentacion completa del TP8 y actualizacion de README...
git add tp8/README.md README.md tp8/preparar_entrega_git_tp8.bat
git commit -m "docs: documentacion tecnica completa de seguridad OWASP y guia de pruebas para TP8"

echo.
echo [6/6] Fusionando con la rama main y subiendo a GitHub...
git checkout main
git merge feature/tp8-autenticacion-sesiones -m "Merge branch 'feature/tp8-autenticacion-sesiones' into main - TP8 Completo"

echo.
echo Subiendo ramas al repositorio remoto...
git push origin main
git push origin feature/tp8-autenticacion-sesiones

echo.
echo ============================================================================
echo   Listo! Todo el codigo y commits del TP8 fueron preparados con exito.
echo ============================================================================
pause
