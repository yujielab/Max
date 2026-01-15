# CloudDrive · Apple 风格网盘

一个具备完整文件管理流程的轻量云盘项目，提供 Apple 风格玻璃质感界面、目录浏览、创建文件夹、上传文件、路径面包屑与存储统计。后端使用 Laravel 风格的 API，前端为 Vue 3 + Tailwind CSS。

## 功能特性

- **目录浏览**：支持根目录与任意子目录的文件/文件夹列表。
- **面包屑导航**：实时显示路径层级，点击可快速跳转。
- **创建文件夹**：输入名称即可在当前目录创建。
- **文件上传**：选择文件即可上传并自动刷新列表。
- **空间统计**：显示文件数、文件夹数与容量占用。
- **稳定本地存储**：使用本地目录作为存储后端，适合生产部署与权限管理。
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
# 本地存储根目录（相对 storage/app）
ICLOUD_LOCAL_ROOT=cloud-drive
```

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

> 本仓库提供的 `artisan` 会在缺少 Laravel 框架文件时给出提示。若需完整运行后端，请在完整的 Laravel 项目中使用这些代码，或先安装 Laravel 依赖（`composer install`）并补齐 `vendor/` 与 `bootstrap/` 目录。

3. 启动开发服务

```bash
php artisan serve
npm run dev
```

访问 `http://127.0.0.1:8000` 查看界面。

## 生产部署（逐项检查与优化版）

> 目标：一步一步确认环境、依赖、构建产物、权限与服务都可用，避免线上部署遗漏。

### 1. 服务器前置检查

```bash
lsb_release -a
php -v
composer -V
node -v
npm -v
```

确保系统与版本符合要求：

- Ubuntu 22.04 / Debian 12
- PHP 8.2+、Composer、Node.js 18+
- Nginx + PHP-FPM

### 2. 拉取代码并确认分支

```bash
git clone <your-repo-url> /var/www/clouddrive
cd /var/www/clouddrive
git status -sb
```

### 3. 安装后端依赖（生产优化）

```bash
composer install --no-dev --optimize-autoloader
```

### 4. 安装前端依赖并构建静态资源

```bash
npm install
npm run build
```

> 若你只在服务器做一次构建，可将 `node_modules` 缓存到部署流程中，减少重复安装。

### 5. 生成配置并完善环境变量

```bash
cp .env.example .env
php artisan key:generate
```

> 若缺少 Laravel 框架文件，请先在项目中安装依赖并确保存在 `vendor/` 与 `bootstrap/` 目录。

在 `.env` 中设置存储根目录，例如：

```env
ICLOUD_LOCAL_ROOT=cloud-drive
```

如需调整日志级别、环境模式，可继续补充：

```env
APP_ENV=production
APP_DEBUG=false
```

### 6. 目录权限与可写目录核对

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

可选验证（确保可写）：

```bash
sudo -u www-data test -w storage && echo "storage writable"
sudo -u www-data test -w bootstrap/cache && echo "cache writable"
```

### 7. Nginx 示例配置（生产建议）

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

### 8. 重载服务并验证

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

验证接口与前端资源：

```bash
curl -I http://your-domain.com
curl -I http://your-domain.com/api/storage/items
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
