# CloudDrive · Apple 风格网盘

CloudDrive 是一个具备完整文件管理流程的轻量云盘项目，提供 Apple 风格玻璃质感界面、目录浏览、创建文件夹、上传文件、路径面包屑与存储统计。后端基于 Laravel 10，前端为 Vue 3 + Tailwind CSS。

## 功能特性

- **目录浏览**：支持根目录与任意子目录的文件/文件夹列表。
- **面包屑导航**：实时显示路径层级，点击可快速跳转。
- **创建文件夹**：输入名称即可在当前目录创建。
- **文件上传**：选择文件即可上传并自动刷新列表。
- **空间统计**：显示文件数、文件夹数与容量占用。
- **稳定本地存储**：使用本地目录作为存储后端，适合生产部署与权限管理。
- **状态反馈**：加载、上传、错误提示完整。

## 技术栈

- 后端：Laravel 10（PHP 8.2+）
- 前端：Vue 3、Vite、Tailwind CSS

## 目录说明

- `app/Http/Controllers`：接口控制器
- `app/Services`：存储服务实现与驱动路由
- `config/icloud.php`：存储相关配置
- `resources/js/components`：前端主界面
- `resources/css`：Tailwind 及组件样式
- `public/`：HTTP 入口与静态资源

## 环境变量（完整模板）

查看 `.env.example` 并按需修改：

```env
APP_NAME="CloudDrive"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Shanghai
APP_LOCALE=zh_CN
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=zh_CN

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

CORS_ALLOWED_ORIGINS=*
ICLOUD_LOCAL_ROOT=cloud-drive
ASSET_URL=
```

## 本地开发（开箱即用版）

> 适合第一次启动或本地调试：包含 Laravel 启动文件、配置与默认目录。

1. **安装依赖**

```bash
composer install
npm install
```

2. **生成环境文件**

```bash
cp .env.example .env
php artisan key:generate
```

> 如需切换数据库，请更新 `.env` 的 `DB_CONNECTION` 与 `DB_DATABASE`。

3. **启动服务**

```bash
php artisan serve
npm run dev
```

访问 `http://127.0.0.1:8000` 查看界面。

## 生产部署（全流程检查版）

> 目标：确保依赖、构建、权限与性能优化全部就绪。

### 1. 环境准备

```bash
php -v
composer -V
node -v
npm -v
```

建议版本：PHP 8.2+ / Node 18+ / Nginx + PHP-FPM。

### 2. 拉取代码并安装依赖

```bash
git clone <your-repo-url> /var/www/clouddrive
cd /var/www/clouddrive
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 3. 配置环境变量

```bash
cp .env.example .env
php artisan key:generate
```

修改 `.env`：

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `ICLOUD_LOCAL_ROOT=cloud-drive`（或你希望的目录）

### 4. 目录权限

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 5. 缓存与性能优化

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

> 如需回滚缓存：`php artisan optimize:clear`。

### 6. Nginx 示例配置

```nginx
server {
    listen 80;
    server_name your-domain.com;

    root /var/www/clouddrive/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 7. 重新加载服务并验证

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

验证：

```bash
curl -I https://your-domain.com
curl -I https://your-domain.com/api/storage/items
```

## API 说明

### 获取目录内容

```
GET /api/storage/items?path=/设计稿
```

响应示例：

```json
{
  "provider": "local",
  "path": "/设计稿",
  "items": [
    {
      "type": "folder",
      "name": "交付",
      "path": "/设计稿/交付",
      "updated_at": "2024-04-03T08:12:30+00:00"
    }
  ],
  "stats": {
    "folders": 1,
    "files": 0,
    "size": 0
  }
}
```

### 创建文件夹

```
POST /api/storage/folders
```

请求体：

```json
{
  "path": "/设计稿",
  "name": "交付"
}
```

### 上传文件

```
POST /api/storage/upload
```

`multipart/form-data`：

- `path`：目录路径
- `file`：上传文件

## 许可

MIT
