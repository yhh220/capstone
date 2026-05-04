<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="380" alt="Laravel Logo">

# Win Win Car Studio

**马来西亚汽车配件电商与预约管理平台**

*A full-stack e-commerce & booking platform for a Malaysian car accessories showroom*

<br>

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4.2-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-5-F59E0B?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-Dev-003B57?style=for-the-badge&logo=sqlite&logoColor=white)

</div>

---

## 目录 / Table of Contents

- [项目简介](#项目简介--overview)
- [核心功能](#核心功能--features)
- [技术栈](#技术栈--tech-stack)
- [系统架构](#系统架构--architecture)
- [安装部署](#安装部署--installation)
- [目录结构](#目录结构--project-structure)
- [数据库设计](#数据库设计--database-schema)
- [多语言支持](#多语言支持--i18n)
- [管理后台](#管理后台--admin-panel)
- [AI 智能助手](#ai-智能助手--ai-chatbot)
- [安全机制](#安全机制--security)
- [生产部署](#生产部署--production-deployment)
- [Windows / Herd 注意事项](#windows--herd-注意事项)

---

## 项目简介 / Overview

**Win Win Car Studio** 是一个面向马来西亚本地汽车配件门店的全功能 B2C 电商平台，采用 **TALL 技术栈**（Tailwind + Alpine + Livewire + Laravel）构建。

平台提供面向消费者的三语言店面（英语、马来语、中文），支持产品浏览、车型兼容性查询、网上下单、工坊预约及 AI 智能客服；同时提供由 Filament 5 驱动的完整管理后台，涵盖销售、预约、库存、员工及系统设置的全生命周期管理。

---

## 核心功能 / Features

### 🛒 消费者店面

| 模块 | 功能描述 |
|------|----------|
| **首页** | 英雄横幅、精选产品、品类网格、统计数据、客户评价、行动召唤区域 |
| **产品目录** | 实时搜索、品类筛选、产品详情页、图片画廊 |
| **购物车 & 结账** | 加入购物车、数量调整、安全结账流程 |
| **订单追踪** | 登录用户查看历史订单；访客凭单号查询状态 |
| **工坊预约** | 浏览可预约服务及价格、实时选时段、生成预约 Token |
| **预约追踪** | 凭手机号查询预约状态；凭 Token 链接管理/取消预约 |
| **兼容性查询** | 品牌 → 车型 → 年份三级联动，快速匹配适用产品 |
| **AI 智能助手** | 浮动客服机器人，由 Anthropic Claude API 驱动，解答汽车及产品问题 |
| **图片展示墙** | 可按品类筛选的瀑布流图片画廊，展示改装案例 |
| **用户中心** | 注册、登录、编辑个人资料、收货地址管理 |
| **联系我们** | 带蜜罐 & 频率限制的防垃圾联系表单 |
| **静态页面** | 关于我们、常见问题、隐私政策、服务条款 |

**跨模块能力：**
- 🌏 **三语言切换**：英语 (EN) / 马来语 (BM) / 中文 (ZH)，基于 Session 保存偏好
- 🌓 **深色/浅色模式**：使用 `localStorage` + `html.dark` 类，Tailwind CDN `darkMode: 'class'` 驱动
- 📱 **完全响应式**：移动端、平板、桌面端全适配
- ♿ **无障碍访问**：ARIA 标签、跳过导航链接、`focus-visible` 样式、减少动效支持
- 🔍 **SEO 优化**：逐页元数据、Open Graph 标签、自动生成 XML Sitemap

### 🛡️ 管理后台 (`/admin`)

由 **Filament 5** 驱动，提供完整的 CRUD 管理界面：

| 资源模块 | 功能 |
|----------|------|
| **仪表板** | KPI 卡片（营收、待处理预约、未读询盘、订单量）+ 营收趋势图 + 热销产品图 |
| **订单管理** | 查看并更新订单状态、发货信息、支付方式 |
| **产品管理** | 产品 CRUD、Spatie MediaLibrary 图片上传、车型兼容性绑定、库存管理 |
| **品类管理** | 层级品类创建与维护 |
| **品牌管理** | 汽车/产品品牌管理 |
| **服务管理** | 工坊可预约服务的价格、时长、排班设置 |
| **预约管理** | 审核、排程、完成/取消工坊预约 |
| **客户管理** | 查看客户资料及订单历史 |
| **询盘管理** | 处理联系表单提交，标记已读/未读 |
| **评价管理** | 审核并发布客户评价与案例图片 |
| **图片库管理** | 管理展示画廊相册，Spatie MediaLibrary 自动生成缩略图 |
| **用户管理** | 员工账户管理，角色权限分配（`owner / admin / staff / customer`） |
| **操作日志** | Spatie ActivityLog 全模型变更审计追踪 |
| **系统设置** | 全局键值对设置，带缓存加速 |

---

## 技术栈 / Tech Stack

| 分类 | 技术 |
|------|------|
| **后端框架** | Laravel 13 (PHP 8.3+) |
| **前端响应式** | Livewire 4.2 |
| **管理面板** | Filament 5 |
| **CSS 框架** | Tailwind CSS（CDN，无需编译） |
| **数据库** | SQLite（开发）/ MySQL（生产） |
| **媒体管理** | Spatie Laravel MediaLibrary 11 + Intervention Image 4 |
| **审计日志** | Spatie Laravel ActivityLog |
| **Sitemap** | Spatie Laravel Sitemap |
| **SEO** | Artesaos SEOTools |
| **AI 集成** | Anthropic Claude API (`claude-haiku-4-5`) |
| **防垃圾** | Spatie Laravel Honeypot |
| **构建工具** | Vite 8 |
| **调试工具** | Barryvdh Laravel Debugbar（仅开发环境） |

---

## 系统架构 / Architecture

```
┌──────────────────────────────────────────────────────────────────┐
│                      消费者店面 (Public)                          │
│  22 个 Livewire 组件  ·  3 种语言  ·  深色模式  ·  AI 聊天机器人  │
└──────────────────────────┬───────────────────────────────────────┘
                           │ Laravel 路由 / 中间件
┌──────────────────────────▼───────────────────────────────────────┐
│                 应用核心 (app/)                                    │
│  17 个 Model  ·  5 个 Middleware  ·  5 个 Policy                 │
│  BookingService  ·  AiService (Mock/Ollama/Claude)               │
└──────────┬─────────────────────────┬────────────────────────────┘
           │                         │
┌──────────▼──────────┐   ┌──────────▼──────────────────────────┐
│   管理后台 (/admin)  │   │            数据库层                   │
│   Filament 5        │   │  SQLite/MySQL · 36 次 Migration      │
│   13 个 Resource    │   │  Spatie MediaLibrary · Queue Jobs    │
│   6 个 Widget       │   └─────────────────────────────────────┘
└─────────────────────┘
```

**认证体系：**
- 消费者：`web` Guard（`users` 表，`customer` 角色）
- 管理员：`admin` Guard（`users` 表，`owner/admin/staff` 角色），Filament 独立登录页

---

## 安装部署 / Installation

### 系统要求

- PHP >= 8.3（需扩展：`pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`）
- Composer >= 2
- Node.js（可选，Tailwind 通过 CDN 加载，无需本地构建）

### 步骤

**1. 克隆项目**
```bash
git clone <repo-url> winwin
cd winwin
composer install
```

**2. 环境配置**
```bash
cp .env.example .env
php artisan key:generate
```

**3. 配置 `.env`**

关键环境变量：
```env
APP_NAME="Win Win Car Studio"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

# 可选：启用 AI 聊天机器人（不填则隐藏聊天入口）
ANTHROPIC_API_KEY=sk-ant-...
AI_DRIVER=claude   # 选项: claude | ollama | mock

# 店铺联系信息
STORE_NAME="Win Win Car Studio"
STORE_PHONE_DISPLAY="+60 12-345 6789"
STORE_EMAIL=info@winwincarstudio.com
```

**4. 数据库初始化**
```bash
php artisan migrate --seed
```

> 自动生成：31 款马来西亚常见车型、示例产品、客户评价、默认管理员账号。
>
> 默认管理员：`admin@example.com` / `password`

**5. 存储链接 & Sitemap**
```bash
php artisan storage:link
php artisan sitemap:generate
```

**6. 启动开发服务器**
```bash
# 仅启动 Web 服务器
php artisan serve

# 或同时启动队列 Worker（推荐，图片异步处理需要）
composer run dev
```

访问地址：
- 店面：`http://localhost:8000`
- 管理后台：`http://localhost:8000/admin`

### 图片处理队列

产品/服务/画廊的图片上传由 Spatie MediaLibrary 异步处理（自动生成缩略图）。

若图片未显示，请确保队列 Worker 正在运行：
```bash
php artisan queue:listen
```

或在 `.env` 中设置同步处理（开发环境快速测试用）：
```env
QUEUE_CONNECTION=sync
```

---

## 目录结构 / Project Structure

```
capstone/
├── app/
│   ├── Console/Commands/        # GenerateSitemap 等 Artisan 命令
│   ├── Contracts/               # AiServiceInterface 抽象
│   ├── Filament/
│   │   ├── Pages/               # Dashboard, Auth/Login
│   │   ├── Resources/           # 13 个 CRUD 资源（含 Forms/Tables/Pages）
│   │   └── Widgets/             # 6 个统计 Widget
│   ├── Http/Middleware/         # AdminMiddleware, SecurityHeaders, SetLocale, ShoppingEnabled
│   ├── Livewire/                # 22 个 Livewire 组件
│   │   ├── Auth/                # UserLogin
│   │   └── Concerns/            # SetsSeo Trait
│   ├── Mail/                    # OrderConfirmationMail
│   ├── Models/                  # 17 个 Eloquent Model
│   ├── Policies/                # 5 个授权 Policy
│   ├── Providers/               # AppServiceProvider, AiServiceProvider, AdminPanelProvider
│   └── Services/
│       ├── Ai/                  # MockDriver, OllamaDriver
│       └── Booking/             # BookingService（可用性逻辑）
├── config/                      # 17 个配置文件
├── database/
│   ├── factories/
│   ├── migrations/              # 36 次迁移
│   └── seeders/                 # DatabaseSeeder, CarModelSeeder
├── lang/
│   ├── ms.json                  # 马来语翻译
│   └── zh.json                  # 简体中文翻译
├── public/
│   └── sitemap.xml              # 自动生成
├── resources/
│   └── views/
│       ├── components/          # chatbot, compatibility-checker, empty-state, page-loader
│       ├── errors/              # 404, 419, 500, unauthorized
│       ├── filament/            # theme-toggle, scroll-to-top
│       ├── layouts/             # app.blade.php, admin.blade.php
│       ├── livewire/            # 21 个 Blade 视图
│       └── mail/                # order-confirmation.blade.php
└── routes/
    └── web.php                  # 24 条路由定义
```

---

## 数据库设计 / Database Schema

共 **36 次 Migration**，核心表结构：

| 数据表 | 说明 |
|--------|------|
| `users` | 用户（含角色 `owner/admin/staff/customer`、地址、手机等字段） |
| `products` | 产品（名称、slug、价格、库存、规格、多语言描述） |
| `categories` | 产品品类（层级结构） |
| `brands` | 汽车/产品品牌 |
| `car_models` | 31 款马来西亚车型（Perodua、Proton、Toyota、Honda 等） |
| `product_compatibilities` | 产品与车型兼容性映射表 |
| `orders` | 订单（状态、支付方式、运费、收货信息） |
| `order_items` | 订单行项目（含产品名称快照） |
| `cart_items` | 用户购物车 |
| `services` | 工坊可预约服务（价格、时长、排班） |
| `bookings` | 预约记录（Token、状态、备注） |
| `contacts` | 联系表单提交 |
| `feedback` | 客户评价与案例图片 |
| `gallery_items` | 展示画廊图片 |
| `settings` | 全局键值对系统设置（带缓存） |
| `media` | Spatie MediaLibrary 媒体文件记录 |
| `activity_log` | Spatie ActivityLog 操作审计 |
| `ai_logs` | AI 聊天机器人交互日志 |

---

## 多语言支持 / i18n

| 语言 | 代码 | 存储位置 |
|------|------|----------|
| 英语（默认） | `en` | PHP 代码内联 `__('...')` |
| 马来语 | `ms` | `lang/ms.json` |
| 简体中文 | `zh` | `lang/zh.json` |

**切换机制：**

```
用户点击语言按钮
    → GET /lang/{locale}
    → SetLocale 中间件从 Session 读取语言
    → App::setLocale() 应用全局语言设置
    → 返回原页面
```

翻译覆盖：导航、功能描述、产品术语、表单标签、错误消息等 100+ 键值对。

---

## 管理后台 / Admin Panel

管理后台地址：`http://localhost:8000/admin`

**角色权限：**

| 角色 | 权限范围 |
|------|----------|
| `owner` | 完整系统访问权限 |
| `admin` | 业务数据管理（订单、产品、预约） |
| `staff` | 有限操作权限（查看为主） |
| `customer` | 仅消费者店面，无后台访问权 |

**仪表板 Widget：**
- `StatsOverview` — 营收、待处理预约、未读询盘、订单总量
- `RevenueChart` — 营收趋势折线图
- `TopProductsChart` — 热销产品排行
- `CategoryDistributionChart` — 品类销售分布
- `RecentOrdersWidget` — 最新订单列表
- `RecentActivityWidget` — 系统操作活动流

---

## AI 智能助手 / AI Chatbot

浮动 AI 聊天机器人，支持三种运行模式：

| 模式 | 配置 | 说明 |
|------|------|------|
| `claude` | 需要 `ANTHROPIC_API_KEY` | 使用 Anthropic `claude-haiku-4-5` 模型 |
| `ollama` | 需本地 Ollama 运行 | 使用本地 LLM（配置 `OLLAMA_HOST`） |
| `mock` | 无需任何 API Key | 返回预设模拟回复，用于开发测试 |

```env
AI_DRIVER=mock        # 开发测试
AI_DRIVER=ollama      # 本地 LLM
AI_DRIVER=claude      # 生产环境
```

未设置 `ANTHROPIC_API_KEY` 时，聊天入口自动隐藏，替换为普通联系提示。所有交互记录保存于 `ai_logs` 表。

---

## 安全机制 / Security

| 机制 | 实现方式 |
|------|----------|
| **HTTP 安全头** | `SecurityHeaders` 中间件（`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` 等） |
| **蜜罐防垃圾** | `ContactPage` 含隐藏 `honeypot` 字段，机器人填写则静默丢弃 |
| **频率限制** | 联系表单每 IP 每 5 分钟最多 3 次提交（`RateLimiter`） |
| **输入清洗** | 所有用户文本字段使用 `strip_tags()` 处理 |
| **CSRF 保护** | Laravel 内置 CSRF Token 验证 |
| **角色授权** | Policy + `AdminMiddleware` 双重保护管理后台 |
| **会话安全** | 数据库 Session 驱动，120 分钟超时 |
| **语言跳转防护** | `/lang/{locale}` 路由验证语言代码白名单，防止开放重定向 |

---

## 生产部署 / Production Deployment

**1. 配置生产环境变量**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=winwin
DB_USERNAME=your_user
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
SESSION_DRIVER=file
CACHE_DRIVER=file
```

**2. 执行生产构建**
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan sitemap:generate
php artisan optimize
```

**3. 配置定时任务（Cron）**
```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**4. 配置队列 Worker（Supervisor）**
```ini
[program:winwin-worker]
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
```

---

## Windows / Herd 注意事项

**Session 认证问题（登录后立即退出）：**
```env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

**SQLite 写入权限错误（`rename(): Access is denied`）：**
```cmd
icacls storage /grant Everyone:(OI)(CI)F /T
php artisan view:clear
```

---

## 路由一览 / Routes

| 路径 | 组件 | 描述 |
|------|------|------|
| `GET /` | `HomePage` | 店面首页 |
| `GET /products` | `ProductsPage` | 产品目录 |
| `GET /products/{slug}` | `ProductDetail` | 产品详情 |
| `GET /services` | `ServicesPage` | 工坊服务列表 |
| `GET /booking` | `BookingForm` | 创建预约 |
| `GET /booking/track` | `BookingTracker` | 凭手机号查询预约 |
| `GET /booking/{token}` | `BookingManage` | 管理/取消预约 |
| `GET /gallery` | `GalleryPage` | 展示画廊 |
| `GET /track-order` | `OrderTracker` | 凭单号查询订单 |
| `GET /about` | `AboutPage` | 关于我们 |
| `GET /contact` | `ContactPage` | 联系我们 |
| `GET /faq` | `FaqPage` | 常见问题 |
| `GET /privacy-policy` | `PrivacyPolicyPage` | 隐私政策 |
| `GET /terms-of-service` | `TermsOfServicePage` | 服务条款 |
| `GET /cart` | `CartPage` | 购物车（需登录） |
| `GET /checkout` | `CheckoutPage` | 结账（需登录） |
| `GET /my-orders` | `MyOrdersPage` | 我的订单（需登录） |
| `GET /profile` | `ProfilePage` | 个人中心（需登录） |
| `GET /login` | `UserLogin` | 用户登录 |
| `GET /lang/{locale}` | — | 切换语言（en/ms/zh） |
| `GET /sitemap.xml` | — | SEO Sitemap |
| `* /admin` | Filament | 管理后台（需管理员角色） |

---

## 许可证 / License

本项目为毕业设计（Capstone Project）作品，仅供学术演示使用。所有示例数据及图片资源均用于演示目的。
