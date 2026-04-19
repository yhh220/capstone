# Win Win Car Audio — Capstone Execution Plan v3
**最后更新:** 2026-04-17
**Repo:** https://github.com/yhh220/capstone
**Supervisor:** Norayu Abdul Talib
**Employer:** Win Win Car Audio Auto Accessories (002645107-U), Shah Alam

---

## ⚡ 分工一览（打开第一件事就看这个）

| 人 | 负责什么 | 不负责什么 |
|----|---------|-----------|
| **YHH** | 全部网页开发（除 3D）+ AI 模块 + Service Booking + Survey + Report Ch.1 + Project Lead + Employer Liaison | 3D、在线购物后端、Admin CRUD |
| **Chris** | Online Shopping（Cart/Checkout/Order）+ Admin Filament Panel + Database Migrations + Deployment + Report Ch.3 | 3D、网页 UI、AI |
| **Soon Gor** | **只做 3D**：Homepage 3D 展示区 + Product Detail 3D Viewer。技术选型、产品选择、实现全部自己决定 + Report Ch.2（系统设计部分） | 其他网页开发、后端、Admin |

**协作规则：**
- 每周日晚各自发 weekly update 给 supervisor Norayu（模板在最下面）
- 有 blocker 当天说，不要憋到 sync
- Git commit 每天 push，message 写清楚做了什么
- YHH 负责给 Soon Gor 留好 3D 的挂载点（`div` slot），不要让他等

---

## 📅 时间线总览

| 周 | 日期 | 重点 | 关键截止 |
|----|------|------|---------|
| **1** | Apr 17–25 | YHH 访谈舅舅；全组 setup | Obj 1 草稿 |
| **2** | Apr 26–May 2 | Survey 发出；静态页面完成 | Obj 2 开始收数据 |
| **3** | May 3–9 | 竞品分析；Survey 收完分析 | Obj 3 |
| **4** | May 10–16 | UML + ERD + Figma + DB migrations 定稿 | Obj 4 |
| **5** | May 17–23 | 设计文档全完；Ollama 跑通；3D 技术验证 | Obj 5 |
| **6** | May 24–30 | Dev Sprint 1 | Booking / Catalog / 3D 开始 |
| **7** | May 31–Jun 6 | Dev Sprint 2 | AI / Cart+Checkout / 3D 进行中 |
| **8** | **Jun 7–13** | **Prototype 完成** | **Obj 6 — 上线** |
| **9** | Jun 14–20 | Testing | Obj 7 — test cases + bug fix |
| **10** | Jun 21–27 | User Evaluation | Obj 8 — 15+ 用户 |
| **11** | Jun 28–Jul 4 | Future Work + Report 草稿 80% | Obj 9 |
| **12** | Jul 5–11 | Report 定稿 + Rehearsal 1 | |
| **13** | Jul 12–18 | Rehearsal 2 + Exhibition poster 印好 | |
| **14** | Jul 19–25 | **Presentation + Exhibition** | |

---

## 🔴 YHH 的任务

### 本周立刻要做（截止 Apr 25）

#### 访谈舅舅（Objective 1）
这个 weekend 去店里，**带纸笔/录音**，问这些：

- 一天的运营流程：几点开门，客户怎么来，怎么付款，怎么关店
- 客户怎么找到你们的店？（路过 / Facebook / 朋友介绍）
- 客户最常问什么问题？
- 现在 aircond 服务怎么预约？（电话 / WhatsApp / 直接来）
- 最耗时 / 最麻烦的日常工作是什么？
- 有没有常回头的熟客？他们买什么？
- 你希望这个网站帮你解决什么问题？
- 店里一个月大概多少客人？旺季淡季？

**顺便：**
- [ ] 拍 3–5 张店内照片（门面、产品展示、工作区）
- [ ] 拿舅舅全名 + 称谓（Mr./Mdm.）用于 Employer Form 和 Report
- [ ] 确认舅舅愿意在 proposal form 上签名盖章

#### Survey 设计 + 发出（Objective 2，截止 May 2）

Google Forms，EN 为主，15–20 题，发到：
- WhatsApp：舅舅客户群 + 你们三个的朋友圈
- 舅舅 Facebook page 发 post
- 店里打印 QR code 贴墙上

**问题建议（改成自己的语气）：**
1. 你有车吗？（Yes/No）
2. 车牌 / 车型 / 年份？（open-ended）
3. 上次买车用配件是在哪里？（实体店 / Shopee / Lazada / Facebook）
4. 买配件最麻烦的是什么？（multi-select）
5. 你想提前在网上看产品再去店里吗？（1–5）
6. 你会用网上预约来预约 aircond 服务吗？（1–5）
7. 你信任 AI 给你推荐适合你车的配件吗？（1–5）
8. 你偏好用哪种语言浏览网站？（EN / MS / 中文）
9. 买配件最看重什么？（价格 / 品质 / 品牌 / 兼容性）
10. 你用过哪些汽配网站或 app？
11. 如果可以直接在网上下单，你会用吗？（1–5）
12. 下单后你想收到确认 email 吗？（Yes/No）
13. 你用 WhatsApp 联系店家方便吗？（1–5）
14. 你知道 Win Win Car Audio 这家店吗？（Yes/No）
15. 开放式：你希望这个网站有什么功能？

**目标 ≥30 份，截止 May 2。**

#### Ollama 本地环境
```bash
# 1. 安装 Ollama
# Windows: https://ollama.com/download/windows
# Mac: brew install ollama

# 2. 下载 model（约 3GB，找 WiFi 下）
ollama pull aisingapore/Gemma-SEA-LION-v4-4B-VL
ollama pull gemma3:4b  # 备用

# 3. 测试
ollama run aisingapore/Gemma-SEA-LION-v4-4B-VL
>>> Recommend car audio for Proton X70 2022
>>> 帮我找适合 Myvi 的 dash cam
>>> Cadangan aksesori untuk Honda City
>>> /bye

# 4. 确认 API 在跑
# 浏览器打开 http://localhost:11434 → 看到 "Ollama is running" = 成功
```

---

### Dev 阶段（Week 5–8）

#### 网页开发——你全部负责

**YHH 负责的所有页面和组件：**

| 页面 / 组件 | 内容 |
|------------|------|
| Homepage | Hero + Categories grid + Featured products + Services overview + **3D 展示区（留 div slot 给 Soon Gor）** + Testimonials + WhatsApp CTA |
| About | 公司故事（用访谈资料）+ 品牌展示 + 地址 + Google Maps |
| Services | 每个服务 card（name, duration, price, Book Now button） |
| Product Catalog | Search + Filter（category/price/brand）+ Sort + Grid + Pagination |
| Product Detail | 图片 gallery + **3D viewer slot（留给 Soon Gor）** + 规格 + 价格（toggle 控制）+ Add to Cart（toggle 控制）+ AI 推荐按钮 |
| Booking Wizard | 4步流程（见下方详细） |
| My Booking | 凭 email + token 查询预约状态 |
| Contact | Form + Google Maps embed + WhatsApp + 营业时间 |
| FAQ | Accordion，10+ 题，内容关于产品/服务/预约 |
| Privacy Policy | PDPA 2010 compliant |
| Terms of Service | 购买条款 + 预约条款 |
| 404 / 500 | Custom error pages |
| Language switcher | Header 右上角 EN / MS / 中文，存 session |
| AI Recommendation UI | 浮动 "Ask AI" 按钮 → input → product card 结果 |

**3D 挂载点（你写，Soon Gor 来填）：**
```blade
{{-- Homepage 3D 展示区 --}}
<section id="3d-showcase" class="py-16">
    <div id="3d-mount-homepage" data-product-slug="skynavi-android-player">
        {{-- Soon Gor 的组件 mount 在这里 --}}
        {{-- Fallback: 产品图片 --}}
        <img src="{{ asset('images/skynavi-placeholder.jpg') }}" alt="Skynavi Android Player">
    </div>
</section>

{{-- Product detail 3D viewer --}}
@if($product->model_url)
    <div id="3d-mount-product"
         data-model-url="{{ $product->model_url }}"
         data-product-name="{{ $product->name }}">
        {{-- Soon Gor 的 viewer mount 在这里 --}}
    </div>
@else
    {{-- 普通图片 gallery fallback --}}
    <div class="product-gallery">...</div>
@endif
```

#### Service Booking Module（Week 5–6）

**流程：** 选服务 → 选日期 → 选时段 → 填资料 → 确认 + Email

**Livewire `BookingWizard`：**

```
Step 1 — ServiceSelector
  - 从 DB 取 active services
  - 每个显示: 名称、时长、价格、简介
  - 选中 = highlight + next step

Step 2 — DatePicker
  - Calendar UI
  - 禁用：过去日期、fully booked 日期
  - 选中日期 → Step 3

Step 3 — TimeSlotGrid
  - 按 service.duration_minutes 自动生成时段
  - 营业时间：9:00 AM – 6:00 PM（可在 Settings 配置）
  - 已占用（DB 有 confirmed booking 该时段）→ grey out
  - 选中时段 → Step 4

Step 4 — CustomerForm
  - Name, Email, Phone（必填）
  - Vehicle model, Vehicle plate（必填）
  - Notes（可选）
  - 提交 → 生成 confirm_token → 发 email → 跳 Confirmation 页

Confirmation 页
  - 显示预约摘要
  - "Add to Calendar" 按钮（Google Calendar deep link）
  - "Manage My Booking" 按钮（带 token 的 URL）
```

**Slot 冲突检测（重要）：**
```php
// BookingService.php
public function isSlotAvailable(int $serviceId, Carbon $startAt): bool
{
    $service = Service::find($serviceId);
    $endAt = $startAt->copy()->addMinutes(
        $service->duration_minutes + $service->buffer_after
    );

    return !Booking::where('service_id', $serviceId)
        ->where('status', '!=', 'cancelled')
        ->where(function($q) use ($startAt, $endAt) {
            $q->whereBetween('start_at', [$startAt, $endAt])
              ->orWhereBetween('end_at', [$startAt, $endAt])
              ->orWhere(function($q2) use ($startAt, $endAt) {
                  $q2->where('start_at', '<=', $startAt)
                     ->where('end_at', '>=', $endAt);
              });
        })->exists();
}
```

**Booking Email（Laravel Mail + Mailtrap）：**
```
Subject: Booking Confirmed – [Service] on [Date] | Win Win Car Audio

Dear [Name],

Your appointment has been confirmed!

Service:  [Service Name]
Date:     [Date, e.g. Saturday, 10 May 2026]
Time:     [10:00 AM – 11:00 AM]
Vehicle:  [Model + Plate]

📍 No. 22, Jalan Dinar CU3/C, Taman Subang Perdana,
   Seksyen U3, 40150 Shah Alam, Selangor

📞 016-915 0917

Manage your booking: [link with confirm_token]

See you soon,
Win Win Car Audio Auto Accessories
```

---

#### AI 模块（Week 6–7）

**架构（先建骨架，Week 4）：**

```
app/Contracts/AiServiceInterface.php
  - recommend(string $query, Collection $products): array
  - generateDescription(Product $product): array

app/Services/Ai/MockDriver.php
  - recommend() → ["Sorry, AI is currently unavailable. WhatsApp us: 016-915 0917"]
  - generateDescription() → ["en" => "...", "ms" => "...", "zh" => "..."] (placeholder)

app/Services/Ai/OllamaDriver.php
  - HTTP::post(config('ai.ollama.host') . '/api/chat', [...])
  - Parse JSON response
  - Log to ai_logs table

app/Providers/AiServiceProvider.php
  - bind AiServiceInterface → driver from config('ai.driver')
```

**config/ai.php：**
```php
return [
    'driver' => env('AI_DRIVER', 'mock'),
    'ollama' => [
        'host'  => env('OLLAMA_HOST', 'http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL', 'aisingapore/Gemma-SEA-LION-v4-4B-VL'),
    ],
];
```

**Product Recommendation（Customer UI）：**
```
页面右下角浮动 "Ask AI 🔍" 按钮
  → 点击展开 input panel
  → "Tell us your car model and what you're looking for"
  → 输入 + 提交
  → Loading spinner
  → 显示推荐的 product cards（3–5 个）
     每张 card：图片 + 名称 + 价格 + "View Product" 按钮
  → 若 MockDriver：显示 WhatsApp 联系按钮替代

Rate limit: 10 req/hr per IP
```

**System Prompt（Recommendation）：**
```
You are a product assistant for Win Win Car Audio Auto Accessories
in Shah Alam, Malaysia. The shop sells car audio, dash cameras,
tinted film, body kits, LED lights, wipers, and number plates.
Brands: Mohawk, 70mai, Alpine, Skynavi, Sparko.

RULES:
1. Only recommend products from the catalog provided below.
2. Respond in the SAME LANGUAGE as the user (EN / Malay / Chinese).
3. Keep response concise: max 5 recommendations.
4. If no match found, suggest customer WhatsApp: 016-915 0917.
5. Output ONLY valid JSON:
   {"recommendations":[{"product_id":X,"reason":"..."}],"follow_up":"..."}

Product catalog:
{PRODUCTS_JSON}
```

**AI Description Generator（Admin Filament Action）：**
```php
// On ProductResource edit page, add action button:
// "Generate Description (AI)"
// → calls OllamaDriver::generateDescription($product)
// → fills description_en, description_ms, description_zh fields
// → admin reviews and saves

System Prompt:
"You are a marketing copywriter for an auto accessories shop in Malaysia.
Generate product descriptions in 3 languages.
Return ONLY JSON: {"en":"...","ms":"...","zh":"..."}
Each ~80-100 words. Professional but friendly tone.
Focus on: compatibility, quality, and value."
```

---

### Report 责任

**Chapter 1 全部（边做边写）：**
- 1.1 Company Background（用访谈资料 + 公司名片）
- 1.2 Problem Statement（来自 survey findings）
- 1.3 Primary Research（survey 图表 + interview summary）
- 1.4 Literature Review（≥10 Harvard 引用，Google Scholar 找）
- 1.5 Competitor Analysis（帮 review Soon Gor 的竞品资料）
- 1.6 Requirements Analysis（functional + non-functional）

**每完成一个模块就截图，写进 report 不用重做。**

---

## 🔵 Chris 的任务

### 本周立刻要做（截止 Apr 25）

#### 1. 建好所有 DB Migrations

```bash
php artisan make:migration create_services_table
php artisan make:migration create_bookings_table
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
php artisan make:migration create_cart_items_table
php artisan make:migration create_settings_table
php artisan make:migration create_ai_logs_table
php artisan make:migration add_ecommerce_fields_to_products_table
```

**Schema（直接 copy 进去）：**

```php
// services
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('duration_minutes');
    $table->integer('buffer_after')->default(15);
    $table->decimal('price', 8, 2);
    $table->boolean('active')->default(true);
    $table->timestamps();
});

// bookings
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_id')->constrained();
    $table->string('guest_name');
    $table->string('guest_email');
    $table->string('guest_phone');
    $table->string('vehicle_model');
    $table->string('vehicle_plate')->nullable();
    $table->dateTime('start_at');
    $table->dateTime('end_at');
    $table->enum('status', ['pending','confirmed','completed','cancelled'])
          ->default('pending');
    $table->text('notes')->nullable();
    $table->string('confirm_token')->unique();
    $table->timestamps();
    $table->index(['start_at', 'status']);
});

// orders
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();   // ORD-2026-00001
    $table->string('tracking_number')->unique(); // WNWN12345678
    $table->string('customer_name');
    $table->string('customer_email');
    $table->string('customer_phone');
    $table->json('shipping_address');
    $table->decimal('subtotal', 8, 2);
    $table->decimal('total', 8, 2);
    $table->enum('status', ['pending','processing','shipped','delivered','cancelled'])
          ->default('pending');
    $table->enum('payment_status', ['pending','paid'])->default('paid'); // mock = always paid
    $table->timestamps();
});

// order_items
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained();
    $table->string('product_name');  // snapshot at purchase time
    $table->decimal('price', 8, 2); // snapshot
    $table->integer('quantity');
    $table->timestamps();
});

// cart_items
Schema::create('cart_items', function (Blueprint $table) {
    $table->id();
    $table->string('session_id')->index();
    $table->foreignId('product_id')->constrained();
    $table->integer('quantity')->default(1);
    $table->timestamps();
});

// settings
Schema::create('settings', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->text('value');
    $table->timestamps();
});

// ai_logs
Schema::create('ai_logs', function (Blueprint $table) {
    $table->id();
    $table->string('session_id')->index();
    $table->enum('type', ['recommendation', 'description']);
    $table->text('input');
    $table->text('output')->nullable();
    $table->integer('latency_ms')->nullable();
    $table->string('driver');  // 'ollama' or 'mock'
    $table->timestamps();
});

// products 新增字段
Schema::table('products', function (Blueprint $table) {
    $table->integer('stock')->default(0)->after('price');
    $table->string('brand')->nullable()->after('stock');
    $table->text('description_ms')->nullable()->after('description');
    $table->text('description_zh')->nullable()->after('description_ms');
    $table->json('specs')->nullable();
    $table->json('compatible_vehicles')->nullable();
    $table->string('model_url')->nullable(); // path to .glb file (for 3D)
    $table->boolean('has_3d')->default(false);
});
```

**Seed Settings（DatabaseSeeder.php 里加）：**
```php
DB::table('settings')->insert([
    ['key' => 'ONLINE_SHOPPING_ENABLED', 'value' => 'false', 'updated_at' => now(), 'created_at' => now()],
    ['key' => 'BUSINESS_HOURS_START',    'value' => '09:00', 'updated_at' => now(), 'created_at' => now()],
    ['key' => 'BUSINESS_HOURS_END',      'value' => '18:00', 'updated_at' => now(), 'created_at' => now()],
]);
```

**跑 migrate：**
```bash
php artisan migrate
php artisan db:seed
```

#### 2. 新增 Filament Resources

```bash
php artisan make:filament-resource Service --generate
php artisan make:filament-resource Booking --generate
php artisan make:filament-resource Order --generate
php artisan make:filament-resource Setting --generate
```

#### 3. AI 骨架（给 YHH 用）

```bash
mkdir -p app/Contracts app/Services/Ai
```

建这两个空文件，YHH 来填内容：
- `app/Contracts/AiServiceInterface.php`
- `app/Services/Ai/MockDriver.php`
- `app/Services/Ai/OllamaDriver.php`

#### 4. Helper function

```php
// app/helpers.php（在 composer.json autoload files 里加这个路径）
if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed {
        return cache()->remember("setting_{$key}", 3600, fn() =>
            \App\Models\Setting::find($key)?->value ?? $default
        );
    }
}
```

#### 5. 更新 .env.example

```env
AI_DRIVER=mock
OLLAMA_HOST=http://127.0.0.1:11434
OLLAMA_MODEL=aisingapore/Gemma-SEA-LION-v4-4B-VL

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@winwincaraudio.my
MAIL_FROM_NAME="Win Win Car Audio"
```

---

### Dev 阶段（Week 5–8）

#### Online Shopping Toggle 逻辑

```php
// 清 cache 在 SettingResource 保存时调用
cache()->forget('setting_ONLINE_SHOPPING_ENABLED');
```

**Toggle OFF 的完整行为：**

| 位置 | OFF 行为 | ON 行为 |
|------|---------|--------|
| 导航 | "Shop" 隐藏 | "Shop" 显示 |
| Product card | 无价格、无购买按钮 | 价格 + "Add to Cart" |
| Product detail | 价格隐藏、按钮显示 "Coming Soon" | 全部显示 |
| `/cart` 直接访问 | redirect → home + "Coming soon" flash | 正常 |
| `/checkout` 直接访问 | redirect → home + flash | 正常 |
| Admin 设置页 | 开关显示当前状态 | 同左 |

**Blade 用法（YHH 用，Chris 实现 helper）：**
```blade
@if(setting('ONLINE_SHOPPING_ENABLED') === 'true')
    {{-- 价格和购买按钮 --}}
@else
    <span class="badge badge-secondary">Coming Soon</span>
@endif
```

**Middleware（保护 cart/checkout）：**
```php
// app/Http/Middleware/ShoppingEnabled.php
public function handle(Request $request, Closure $next): Response
{
    if (setting('ONLINE_SHOPPING_ENABLED') !== 'true') {
        return redirect('/')->with('flash', 'Online shopping coming soon!');
    }
    return $next($request);
}
// 注册到 routes/web.php 的 /cart 和 /checkout 路由
```

#### Cart（Session-based）

**Livewire `CartDrawer`：**
- 右上角 cart icon + item count badge
- 点击 → side drawer 从右滑入
- 每个 item：图片 + 名称 + 价格 + 数量 +/- + 删除
- Subtotal 实时更新（`wire:model`）
- "Proceed to Checkout" button
- 空 cart：显示 "Your cart is empty" + "Continue Shopping" button

**Session key:** `cart_{session_id}`，存 `[product_id => quantity]`

#### Checkout（3 步 Livewire wizard）

**Step 1 — Customer Info：**
- Name, Email, Phone（必填）
- Street, City, Postcode, State（收货地址）
- Order notes（选填）

**Step 2 — Mock Payment：**
```
┌─────────────────────────────────┐
│  Payment Method                  │
│  ○ Online Banking                │
│  ○ Cash on Delivery              │
│                                  │
│  ┌──────────────────────────┐   │
│  │  ⚠️  DEMO MODE            │   │
│  │  No actual payment       │   │
│  │  will be processed.      │   │
│  └──────────────────────────┘   │
│                                  │
│  [    Place Order (Demo) →    ]  │
└─────────────────────────────────┘
```

**Step 3 — Confirmation：**
```php
// 下单逻辑
$order = Order::create([
    'order_number'    => 'ORD-' . date('Y') . '-' . str_pad(Order::count() + 1, 5, '0', STR_PAD_LEFT),
    'tracking_number' => 'WNWN' . strtoupper(Str::random(8)),
    'payment_status'  => 'paid', // mock = always paid
    // ...
]);
// 清空 cart session
// 发 confirmation email
// 跳 /orders/{order_number}/confirmed
```

#### Order Tracking（`/track-order`）

- Input: Order Number + Email
- 查到 → 显示 status timeline
```
✅ Order Placed  →  ⏳ Processing  →  📦 Shipped  →  ✅ Delivered
```
- Admin 在 Filament 手动推进状态
- 推进时自动发 status update email 给客户

#### Admin Filament 优先级

1. **SettingResource** — 最重要，Online Shopping toggle 在这里
2. **ServiceResource** — YHH booking 依赖
3. **BookingResource** — calendar view + list + status change
4. **OrderResource** — list + detail + status progression + email trigger
5. **ProductResource** — 已有，加 AI description generator action slot（YHH 来填）

#### Email Templates（`resources/views/mail/`）

建这几个 Mailable：
- `OrderConfirmationMail` — 下单成功
- `OrderStatusUpdateMail` — 状态变更（Processing / Shipped / Delivered）
- `BookingConfirmationMail` — 预约确认（YHH 的，但你建好 Mail class）
- `BookingStatusUpdateMail` — 预约状态变更

用 Mailtrap free sandbox 测试，不花钱。

#### Order Confirmation Email 内容

```
Subject: Order Confirmed – [ORDER_NUMBER] | Win Win Car Audio

Dear [CUSTOMER_NAME],

Your order has been confirmed! 🎉

Order Number:    [ORDER_NUMBER]
Tracking Number: [TRACKING_NUMBER]
Status:          Processing

─────────────────────────────
Items:
  [Product Name] × [qty]     RM [price]
  [Product Name] × [qty]     RM [price]

Subtotal:                    RM [subtotal]
─────────────────────────────
Total:                       RM [total]

Track your order: [link to /track-order]

Questions? WhatsApp us: 016-915 0917

Win Win Car Audio Auto Accessories
No. 22, Ground Floor, Jalan Dinar CU3/C,
Taman Subang Perdana, Seksyen U3,
40150 Shah Alam, Selangor
```

---

### Report 责任

**Chapter 3 全部（Development）：**
- 3.1 Development Methodology（Agile，解释为什么）
- 3.2 Module Implementation（Online shopping, Admin, DB — 截图 + 代码片段）
- 3.3 Key Code Snippets（toggle logic, order generation 各一段）
- 3.4 Deployment（hosting 步骤、production env vars）
- 3.5 Integration Challenges（你解决过的具体问题）

---

## 🟢 Soon Gor 的任务

### 你的范围

**你只做 3D，全部自己决定：**
- 用什么技术（Three.js / model-viewer / Spline / 360° 图片序列 / 其他）
- 用哪些产品展示（建议 Skynavi Android Player，其他自己选）
- 模型从哪里来（自己建模 / 找免费素材 / 拍 360° 照片）
- 交互方式（旋转 / 缩放 / 颜色切换 / 自动播放）

**你只需要和 YHH 对齐两件事：**
1. Homepage 里的挂载点 div ID：`id="3d-mount-homepage"`
2. Product detail 里的挂载点：`id="3d-mount-product"`, `data-model-url`, `data-product-name`

其他全部你自己说了算。

---

### 本周要做（截止 Apr 25）

**技术验证（最重要）：**

先花 2–3 天研究哪种 3D 方案对你最可行，做一个最简单的 Hello World：
- 如果用 Three.js → 能不能加载一个 `.glb` 模型并旋转
- 如果用 model-viewer → 能不能嵌入 Laravel Blade 页面
- 如果用 360° 图片序列 → 能不能拖动切换图片

**做出来了 = 技术验证通过，Week 5 开始正式实现。**
**做不出来 = Week 2 告诉 YHH，及时换方案，不要等到 Week 6。**

---

### Dev 阶段（Week 5–8）

#### Homepage 3D 展示区

**挂载在：**
```html
<div id="3d-mount-homepage"
     data-product-slug="skynavi-android-player"
     class="w-full h-[500px]">
</div>
```

**你要实现：**
- 用 Vite 引入你的 3D library（在 `resources/js/app.js` import）
- 或者用 CDN script tag 在 Blade layout 里加
- 找到 `#3d-mount-homepage` 这个 div，mount 你的 3D viewer
- 加 fallback：如果 WebGL 不支持 / 模型加载失败 → 显示普通图片

**建议做法（你也可以完全不按这个）：**
```
Homepage:
  标题: "Experience Our Signature Product in 3D"
  副标题: "Skynavi Android Player – Rotate, Zoom, Explore"
  3D 展示: 可旋转的 Skynavi 模型 / 360° 图片
  CTA: "View Product Details →"
```

#### Product Detail 3D Viewer

**挂载在（YHH 的 product detail 页会有这个 div）：**
```html
<div id="3d-mount-product"
     data-model-url="/storage/models/skynavi.glb"
     data-product-name="Skynavi Android Player">
</div>
```

**你要实现：**
- 读取 `data-model-url` 加载模型
- 基本交互：拖拽旋转、滚轮缩放、双击重置
- 控制栏按钮：旋转 | 缩放 | 重置 | 全屏
- Mobile：双指缩放、单指旋转
- Loading：进度条 / spinner（加载 3D 模型要几秒）
- 如果 `data-model-url` 空 → 不显示这个 div（YHH 控制的）

#### 关于模型来源（你自己决定，给几个参考）

**免费 3D 模型资源：**
- Sketchfab.com（搜 "car stereo", "android car player", "dashcam"，CC 授权的可用）
- TurboSquid free section
- Free3D.com
- 自己用 Blender 建简单模型（盒子 + 贴图就够展示）

**如果做 360° 图片序列（最简单方案）：**
- 找舅舅把 Skynavi 产品拿出来，手机拍 24–36 张（每转 10–15 度一张）
- 上传到 `public/images/360/skynavi/`（命名 001.jpg, 002.jpg...）
- 用 JS 监听 mouse drag → 切换图片

**格式建议：**
- Three.js 用 `.glb`（binary GLTF，最小）
- model-viewer 也用 `.glb`
- 用 Draco compression 压缩（目标 < 5MB）

---

### Report 责任

**Chapter 2（System Design）：**
- 2.1 System Architecture diagram（你画，draw.io / Excalidraw）
- 2.2 UML Diagrams（Use Case / Activity / Class / Sequence — 和 YHH + Chris 分工，你整理进 report）
- 2.3 Database Design（ERD — Chris 给你，你放进 report）
- 2.4 UI Design（Figma wireframes，你做的 3D 相关部分）
- 2.5 Development Tools Table（team 共同填）
- **2.6 3D Implementation（你的部分：技术选型理由、实现方式、模型来源）**

**另外：**
- Exhibition poster（Week 13 前设计 + 印好）

---

## 🏗️ 系统架构（给 Soon Gor Chapter 2 用）

```
Customer Browser
      │
      ▼
[Laravel App Server (Vercel / Railway)]
      │
 ┌────┴─────────────────────────────────┐
 │  Blade + Livewire (Customer Pages)   │
 │  ─────────────────────────────────   │
 │  Homepage · Products · Booking       │
 │  Cart · Checkout · Tracking         │
 │  AI Recommendation UI                │
 │                                      │
 │  ┌─────────────────────────────┐    │
 │  │  3D Viewer (Soon Gor)       │    │
 │  │  - Homepage showcase        │    │
 │  │  - Product detail viewer    │    │
 │  └─────────────────────────────┘    │
 └──────────┬───────────────────────────┘
            │
 ┌──────────┴──────────┐
 │  Laravel Backend    │
 │  ─────────────────  │
 │  Booking Logic      │
 │  Order Management   │
 │  AI Service Layer   │
 │  Settings (Toggle)  │
 └──────────┬──────────┘
            │
 ┌──────────┴──────────┐
 │  MySQL Database     │
 │  ─────────────────  │
 │  products           │
 │  categories         │
 │  services           │
 │  bookings           │
 │  orders + items     │
 │  cart_items         │
 │  settings           │
 │  feedbacks          │
 │  contacts           │
 │  ai_logs            │
 └─────────────────────┘

[Filament Admin /admin]           [Ollama (YHH laptop, dev only)]
  Products · Bookings · Orders      aisingapore/Gemma-SEA-LION-v4-4B-VL
  Settings (Toggle) · Feedbacks     ← AI_DRIVER=ollama (dev)
                                     AI_DRIVER=mock (production)

[External Services]
  Mailtrap / SMTP → booking + order emails
  WhatsApp deep link (wa.me/, no API)
  Google Maps iframe (no API key)
```

---

## 🗄️ Database Tables 完整清单

| Table | 谁建 | 谁用 |
|-------|------|------|
| `categories` | 已有 | YHH (catalog) |
| `products` + 新字段 | Chris | YHH (catalog, detail), Soon Gor (model_url) |
| `services` | Chris | YHH (booking) |
| `bookings` | Chris | YHH (booking wizard + admin) |
| `orders` | Chris | Chris (checkout + admin) |
| `order_items` | Chris | Chris |
| `cart_items` | Chris | Chris |
| `feedbacks` | 已有 | Chris (admin) |
| `contacts` | 已有 | Chris (admin) |
| `settings` | Chris | YHH (toggle blade check), Chris (admin toggle) |
| `ai_logs` | Chris | YHH (AI module logging) |

---

## ✅ Feature 完整清单

### Must-Have
- [ ] Homepage（Hero, categories, featured, 3D showcase, testimonials, WhatsApp CTA）
- [ ] Product catalog（search, filter, sort, pagination）
- [ ] Product detail（gallery, 3D viewer, specs, toggle-controlled price + cart）
- [ ] Service booking wizard（4步 + email）
- [ ] My Booking（email + token 查询）
- [ ] AI product recommendation（浮动按钮 + product cards）
- [ ] AI description generator（Filament admin action）
- [ ] Online shopping cart（session-based）
- [ ] Checkout（3步 + mock payment）
- [ ] Order confirmation page + email
- [ ] Order tracking page
- [ ] Admin toggle（Online Shopping ON/OFF）
- [ ] WhatsApp integration（floating button + product inquiry）
- [ ] Testimonials
- [ ] Contact page（form + map）
- [ ] FAQ
- [ ] Multi-language（EN / MS / 中文）
- [ ] Responsive（mobile + tablet + desktop）
- [ ] Privacy Policy + Terms of Service
- [ ] **3D Homepage showcase（Soon Gor）**
- [ ] **3D Product detail viewer（Soon Gor）**

### Should-Have
- [ ] 360° product turntable（由 Soon Gor 决定是否做）
- [ ] My Bookings history page
- [ ] Related products
- [ ] 404 / 500 custom error pages
- [ ] Cookie consent banner（PDPA）
- [ ] SEO meta tags per page
- [ ] Sitemap.xml

### Could-Have（时间够才做）
- [ ] Dark mode
- [ ] Product reviews
- [ ] Newsletter signup
- [ ] PWA manifest

---

## 📝 Report 章节分工

| 章节 | 负责人 | 截止 |
|------|--------|------|
| Cover, TOC, Acknowledgements, About Authors | 各人自己写自己部分 | Week 12 |
| Introduction | YHH | Week 11 |
| Chapter 1 — Analysis | **YHH** | Week 11 |
| Chapter 2 — Design | **Soon Gor** | Week 11 |
| Chapter 3 — Development | **Chris** | Week 11 |
| Chapter 4 — Testing | 全组 | Week 12 |
| Chapter 5 — Evaluation | YHH（数据）+ Soon Gor（图表） | Week 12 |
| Chapter 6 — Future Enhancement | 全组 | Week 11 |
| Conclusion | YHH | Week 12 |
| References（Harvard） | 各自管自己引用的 | Week 12 |

### Appendix 分工
| Appendix | 内容 | 谁 |
|----------|------|-----|
| A | Proposal | 已有 |
| B | Gantt Chart | YHH |
| C | Survey Form | YHH |
| D | Survey Results + User Evaluation | YHH |
| E | Interview Transcript | YHH |
| F | Test Cases（≥30） | Chris |
| G | User Manual | Chris |
| H | README / Setup Guide | Chris |

---

## 🎤 Presentation 分工（每人 ≥10 min，Individual）

| 人 | 内容 |
|----|------|
| **YHH** | Opening + Problem statement + Survey findings + Booking module demo + AI module demo |
| **Chris** | Tech stack rationale + Online shopping demo（toggle OFF → ON）+ Order flow + Admin panel demo + Deployment |
| **Soon Gor** | 3D design process + Homepage 3D demo + Product viewer demo + User evaluation results + Future work |

**必准备的 Q&A（各人写 20 题）：**
- "Why Laravel?" → Team familiarity, Filament saves admin dev time, PHP hosting cheap in MY
- "How does the toggle work?" → Settings table + cache + Middleware + Blade directive
- "Why local AI instead of ChatGPT API?" → Zero cost for small business, data privacy, adapter = easy upgrade later
- "What if Ollama fails during demo?" → MockDriver fallback shows WhatsApp CTA
- "Two customers booking same slot?" → DB overlap check + conflict detection in BookingService
- "How is 3D different from regular photos?" → Soon Gor 自己准备

---

## 📮 Weekly Update Template（每周日发给 Norayu）

```
Subject: Capstone Weekly Update – Week [N] – [Your Name]

Dear Dr. Norayu,

Week [N] Update:

Completed this week:
- [task 1]
- [task 2]

In progress:
- [task 3]

Plan for next week:
- [task 4]

Blockers / Questions:
- [if any, or write "None"]

Estimated hours this week: ~X hours
GitHub commits: [link]

Regards,
[Name]
```

---

## 🚨 本周行动清单（Apr 17–25）

### YHH
- [ ] 这个 weekend 访谈舅舅（带上面 8 个问题）
- [ ] 拍 3–5 张店内照片
- [ ] 写 company background 草稿（Obj 1）
- [ ] 设计 Google Forms survey（基于上面 15 题）
- [ ] 安装 Ollama + pull SEA-LION model，测试能跑
- [ ] Email supervisor Norayu 确认 meeting 时间
- [ ] 给 Homepage 留好 `div#3d-mount-homepage`（通知 Soon Gor）

### Chris
- [ ] 建 7 个 migration 文件（用上面的 schema）
- [ ] `php artisan migrate` 跑通，no error
- [ ] 建 AiServiceInterface、MockDriver、OllamaDriver 空骨架
- [ ] 建 `app/helpers.php` + `setting()` helper
- [ ] 更新 `.env.example`
- [ ] 新增 4 个 Filament Resources（Service, Booking, Order, Setting）

### Soon Gor
- [ ] 研究 3D 技术方案（Three.js / model-viewer / 360°图片序列）
- [ ] 做最简单的 Hello World 验证（能显示一个 3D 物体或转盘）
- [ ] Week 2 告诉 YHH：确定用哪种技术
- [ ] 找一个可用的 Skynavi 或汽车音响的免费 3D 模型（Sketchfab）
- [ ] 开始找 3 篇竞品网站（Obj 3 的一部分，截止 May 9）

---

> **成绩结构提醒：**
> System 40% | Report 30% | Presentation 20% (individual) | Management 10% (individual)
>
> Report + Presentation = 50%，和 system 一样重。边写代码边记录截图，别等最后。
