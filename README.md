# CloudDrive · Apple 风格网盘

一个具备完整文件管理流程的轻量云盘演示项目，提供 Apple 风格玻璃质感界面、目录浏览、创建文件夹、上传文件、路径面包屑与存储统计。后端使用 Laravel 风格的 API，前端为 Vue 3 + Tailwind CSS。

## 功能特性

- **目录浏览**：支持根目录与任意子目录的文件/文件夹列表。
- **面包屑导航**：实时显示路径层级，点击可快速跳转。
- **创建文件夹**：输入名称即可在当前目录创建。
- **文件上传**：选择文件即可上传并自动刷新列表。
- **空间统计**：显示文件数、文件夹数与容量占用。
- **多存储驱动**：本地存储默认可用，WebDAV 与 S3 提供同结构演示目录（可用于接入真实驱动）。
- **状态反馈**：加载、上传、错误提示完整。

## 技术栈

- 后端：Laravel 风格路由 + 服务层（PHP 8.2+）
- 前端：Vue 3、Vite、Tailwind CSS

## 目录说明

- `app/Http/Controllers`：接口控制器
- `app/Services`：存储服务实现与驱动路由
- `config/icloud.php`：存储相关配置
- `resources/js/components`：前端主界面
- `resources/css`：Tailwind 及组件样式

## 环境变量

```env
# 使用的驱动：local | webdav | s3
ICLOUD_STORAGE=local

# 本地存储根目录（相对 storage/app）
ICLOUD_LOCAL_ROOT=cloud-drive

# WebDAV（演示目录可通过 WEBDAV_LOCAL_ROOT 指定）
WEBDAV_BASE_URI=
WEBDAV_USERNAME=
WEBDAV_PASSWORD=
WEBDAV_LOCAL_ROOT=cloud-drive/webdav

# S3（演示目录可通过 S3_LOCAL_ROOT 指定）
AWS_DEFAULT_REGION=
AWS_BUCKET=
S3_LOCAL_ROOT=cloud-drive/s3
```

> WebDAV 与 S3 当前以本地目录模拟，用于演示目录结构与接口统一性。如需对接真实 WebDAV/S3，可在 `app/Services` 中替换对应实现。

## 本地开发

1. 安装依赖

```bash
composer install
npm install
```

2. 配置环境

```bash
cp .env.example .env
php artisan key:generate
```

3. 启动开发服务

```bash
php artisan serve
npm run dev
```

访问 `http://127.0.0.1:8000` 查看界面。

## 生产部署（推荐流程）

### 1. 服务器环境

- Ubuntu 22.04 / Debian 12
- PHP 8.2+、Composer、Node.js 18+
- Nginx + PHP-FPM

### 2. 拉取项目

```bash
git clone <your-repo-url> /var/www/clouddrive
cd /var/www/clouddrive
```

### 3. 安装依赖并构建

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 4. 配置环境变量

```bash
cp .env.example .env
php artisan key:generate
```

在 `.env` 中设置存储驱动与账号信息，例如：

```env
ICLOUD_STORAGE=local
ICLOUD_LOCAL_ROOT=cloud-drive
```

### 5. 目录权限

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

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

完成后重启服务：

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
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
