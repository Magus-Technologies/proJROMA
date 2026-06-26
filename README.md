# Molitalia — Laravel 13 + PHP 8.3

Sistema de facturación electrónica, ventas y almacén para abarrotes.
Migrado de PHP 7.4 puro → **Laravel 13 / PHP 8.3** con Tailwind CSS + Alpine.js.

---

## ✅ Requisitos del VPS

| Requisito | Versión mínima |
|---|---|
| PHP | **8.3** (requerido por Laravel 13) |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Composer | 2.x |
| Nginx / Apache | Cualquier versión reciente |
| Extensiones PHP | `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `xml`, `zip`, `gd`, `intl`, `fileinfo`, `curl` |

---

## 🚀 Instalación paso a paso en VPS

### 1. Verificar PHP 8.3

```bash
php -v
# Debe mostrar: PHP 8.3.x

# Si no lo tienes, en Ubuntu 24.04:
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring \
     php8.3-xml php8.3-zip php8.3-curl php8.3-bcmath php8.3-gd php8.3-intl
```

### 2. Subir el proyecto al VPS

```bash
# Opción A: git clone
cd /var/www/
git clone https://tu-repo/molitalia-l13.git molitalia
cd molitalia

# Opción B: subir ZIP via SFTP y extraer
unzip molitalia-l13.zip -d /var/www/molitalia
cd /var/www/molitalia
```

### 3. Instalar dependencias

```bash
composer install --optimize-autoloader --no-dev
```

### 4. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` con tus datos reales:

```dotenv
APP_URL=https://molitalia.com

DB_DATABASE=magusqao_titanic
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_nueva_clave_segura     # ⚠️ Cambiar la del sistema anterior

MAIL_HOST=matrixsistem.com
MAIL_USERNAME=informes@matrixsistem.com
MAIL_PASSWORD=tu_clave_smtp_real

SUNAT_ENV=production
```

### 5. Ejecutar migraciones

> ✅ Las migraciones **solo agregan** columnas e índices — NO eliminan datos existentes.

```bash
php artisan migrate --force
```

Si preguntan si deseas crear la tabla `sessions`, responde `yes`.

### 6. Permisos de directorios

```bash
chown -R www-data:www-data /var/www/molitalia
chmod -R 755 /var/www/molitalia
chmod -R 775 /var/www/molitalia/storage
chmod -R 775 /var/www/molitalia/bootstrap/cache
```

### 7. Optimizar para producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 8. Configurar Nginx

```nginx
server {
    listen 80;
    server_name molitalia.com www.molitalia.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name molitalia.com www.molitalia.com;

    root /var/www/molitalia/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/molitalia.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/molitalia.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    # Logs
    access_log /var/log/nginx/molitalia.access.log;
    error_log  /var/log/nginx/molitalia.error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass   unix:/run/php/php8.3-fpm.sock;
        fastcgi_index  index.php;
        include        fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param  PHP_VALUE "upload_max_filesize=50M \n post_max_size=50M";
    }

    # Bloquear archivos sensibles
    location ~ /\.(env|git|htaccess) { deny all; }
    location ~ /storage/             { deny all; }

    # Cache de assets
    location ~* \.(css|js|png|jpg|gif|ico|woff2|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Recargar Nginx:
```bash
sudo nginx -t && sudo systemctl reload nginx
```

---

## 🔐 Seguridad implementada (Laravel 13)

| Vulnerabilidad anterior | Solución implementada |
|---|---|
| SQL Injection directo | **Eloquent ORM + bindings** — cero SQL concatenado |
| XSS en vistas PHP | **Blade `{{ }}`** escapa automáticamente |
| Sin CSRF | **`@csrf`** en todos los forms, header automático |
| Login sin límite | **Rate Limiting: 5 intentos / 60s** por IP+usuario |
| Contraseñas sha1 | **Migración silenciosa → bcrypt** en próximo login |
| Sesión sin timeout | **Timeout automático a 8 horas** de inactividad |
| Credenciales en código | **Variables `.env`** — nunca en el repositorio |
| Sin headers HTTP | **X-Frame-Options, HSTS, X-Content-Type-Options** |
| Sin roles | **Spatie Permission** (ADMIN, VENDEDOR, CAJERO, etc.) |
| Archivos backup expuestos | Eliminados del proyecto (solo código limpio) |
| PHP 7.4 sin soporte | **PHP 8.3 + Laravel 13** con soporte hasta 2028 |

---

## ⚡ Novedades de Laravel 13 usadas

- **`#[Middleware]`** en clases y métodos de controllers
- **`#[Table]`** attribute en modelos
- **`getAuthPasswordName()`** para campo custom `clave`
- **JSON exception handling** mejorado en `bootstrap/app.php`
- **`trustProxies(at:'*')`** para VPS detrás de Nginx
- **Soporte PHP 8.3**: Typed class constants, readonly properties mejoradas

---

## 📁 Estructura del proyecto

```
molitalia-l13/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php          ← Login seguro bcrypt/sha1
│   │   │   ├── Api/VentasApiController.php        ← CRUD ventas + stock
│   │   │   ├── Api/ClientesApiController.php      ← CRUD clientes
│   │   │   ├── Api/ProductosApiController.php     ← CRUD productos + import
│   │   │   ├── Api/ArqueoApiController.php        ← Arqueo diario
│   │   │   ├── DashboardController.php            ← KPIs + gráficos
│   │   │   └── VentasController.php               ← Vistas ventas
│   │   ├── Middleware/
│   │   │   ├── CheckEmpresa.php                   ← Valida sesión empresa
│   │   │   ├── SessionTimeout.php                 ← Auto-logout 8 horas
│   │   │   └── SecurityHeaders.php                ← Headers HTTP seguros
│   │   └── Requests/Ventas/GuardarVentaRequest.php
│   └── Models/
│       ├── User.php                               ← Auth con campo 'clave'
│       └── Models.php                             ← 18 modelos Eloquent
├── bootstrap/app.php                              ← Config Laravel 13
├── database/migrations/                           ← Solo agrega, no destruye
├── resources/views/
│   ├── auth/login.blade.php                       ← Login moderno
│   ├── layouts/app.blade.php                      ← Layout responsive sidebar
│   ├── dashboard/index.blade.php                  ← KPIs + Chart.js
│   ├── ventas/index.blade.php                     ← DataTables server-side
│   └── components/nav-link.blade.php
├── routes/web.php                                 ← 45+ rutas organizadas
├── routes/api.php                                 ← API endpoints
├── composer.json                                  ← Laravel ^13.0 / PHP ^8.3
└── .env.example                                   ← Template sin credenciales
```

---

## 🔧 Comandos útiles post-instalación

```bash
# Limpiar y re-optimizar caché
php artisan optimize:clear && php artisan optimize

# Ver todas las rutas registradas
php artisan route:list

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Migrar contraseñas sha1 → bcrypt de un usuario específico
php artisan tinker
>>> $u = \App\Models\User::where('email','admin@molitalia.com')->first();
>>> $u->update(['clave' => \Illuminate\Support\Facades\Hash::make('nueva_clave_segura')]);

# Verificar configuración de BD
php artisan db:show

# Crear tabla de jobs para queue (si usas jobs SUNAT)
php artisan queue:table && php artisan migrate
```

---

## ⚠️ Post-instalación importante

1. **Cambiar contraseña de BD** — la del sistema original estaba expuesta en config.php
2. **Cambiar contraseñas SMTP** — igual que arriba
3. **Configurar SSL** con Let's Encrypt: `certbot --nginx -d molitalia.com`
4. **Verificar PHP 8.3-FPM** esté corriendo: `systemctl status php8.3-fpm`
5. **Backup automático de BD** — configura un cron diario para `mysqldump`
