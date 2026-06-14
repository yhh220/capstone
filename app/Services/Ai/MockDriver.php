<?php

namespace App\Services\Ai;

use App\Contracts\AiServiceInterface;
use App\Models\AiLog;
use App\Models\Brand;
use App\Models\ChatbotFaq;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class MockDriver implements AiServiceInterface
{
    private string $phone = '016-915 0917';

    private function lang(string $systemPrompt): string
    {
        if (str_starts_with($systemPrompt ?? '', 'lang:')) {
            return substr($systemPrompt, 5);
        }
        return 'en';
    }

    /**
     * Keyword-matched knowledge base. Each rule has a `priority` so that
     * specific topics (a product, warranty) outrank generic ones (greetings,
     * "accessories") when a single message mentions several keywords.
     */
    private function rules(): array
    {
        $p = $this->phone;

        return array_merge($this->dbFaqRules(), [
            // Greetings (low priority — a real topic should win)
            [
                'priority' => 10,
                'keywords' => ['hi', 'hello', 'hey', 'morning', 'afternoon', 'evening', 'good day', 'hai', 'selamat', '你好', '哈喽', '嗨', 'howdy', 'hye'],
                'reply' => [
                    'en' => "Hi there! 😊 How can I help you today?\n\nFeel free to ask about our products, services, bookings, operating hours, or anything else!",
                    'ms' => "Hai! 😊 Ada apa yang boleh saya bantu hari ini?\n\nTanya saja tentang produk, servis, tempahan, waktu operasi atau apa-apa sahaja!",
                    'zh' => "你好！😊 今天有什么可以帮您的？\n\n欢迎询问产品、服务、预约、营业时间等任何问题！",
                ],
            ],

            // Operating hours
            [
                'priority' => 55,
                'keywords' => ['hour', 'open', 'close', 'when do you', 'waktu', 'buka', 'tutup', 'jam operasi', '营业', '开门', '关门', '几点开', '几点关'],
                'reply' => [
                    'en' => "🕐 Our operating hours:\n\nMon – Thu: 10:30 AM – 8:00 PM\nFriday: CLOSED (rest day)\nSaturday: 10:30 AM – 8:00 PM\nSunday: 10:30 AM – 6:00 PM\n\nWe are closed every Friday. Plan your visit accordingly! 😊",
                    'ms' => "🕐 Waktu operasi kami:\n\nIsnin – Khamis: 10:30 PG – 8:00 MLM\nJumaat: TUTUP (hari rehat)\nSabtu: 10:30 PG – 8:00 MLM\nAhad: 10:30 PG – 6:00 PTG\n\nKami tutup setiap hari Jumaat. Rancang kunjungan anda! 😊",
                    'zh' => "🕐 我们的营业时间：\n\n周一至周四：上午 10:30 – 晚上 8:00\n周五：休息（不营业）\n周六：上午 10:30 – 晚上 8:00\n周日：上午 10:30 – 下午 6:00\n\n每周五休息，请提前安排好来访时间！😊",
                ],
            ],

            // Location / address
            [
                'priority' => 55,
                'keywords' => ['location', 'address', 'where are', 'where is', 'direction', 'map', 'how to get', 'alamat', 'mana', 'lokasi', '地址', '在哪', '位置', '怎么去', '路线'],
                'reply' => [
                    'en' => "📍 We are located in Shah Alam, Selangor.\n\nFor the exact address and directions, WhatsApp us at {$p} and we'll send you a Google Maps pin right away! 🗺️",
                    'ms' => "📍 Kami terletak di Shah Alam, Selangor.\n\nUntuk alamat lengkap dan arah, WhatsApp kami di {$p} dan kami akan hantar pin Google Maps dengan segera! 🗺️",
                    'zh' => "📍 我们位于雪兰莪州莎阿南。\n\n如需详细地址和导航，请 WhatsApp 我们：{$p}，我们马上发送 Google Maps 定位给您！🗺️",
                ],
            ],

            // Booking / appointment
            [
                'priority' => 60,
                'keywords' => ['book', 'appointment', 'schedule', 'reservation', 'tempah', 'temujanji', 'booking', '预约', '预订', '订位'],
                'reply' => [
                    'en' => "📅 To book a workshop appointment:\n\n1. Open the Booking page\n2. Select your service and preferred time slot\n3. Fill in your details and confirm\n\nI can take you there now — just tap the button below! 😊",
                    'ms' => "📅 Cara membuat tempahan bengkel:\n\n1. Buka halaman Tempahan\n2. Pilih servis dan slot masa yang sesuai\n3. Isi butiran anda dan sahkan\n\nSaya boleh bawa anda ke sana sekarang — ketik butang di bawah! 😊",
                    'zh' => "📅 如何预约工坊服务：\n\n1. 打开预约页面\n2. 选择服务项目和时间段\n3. 填写资料并确认\n\n我现在就能带您过去——点击下方按钮即可！😊",
                ],
            ],

            // Price / cost
            [
                'priority' => 45,
                'keywords' => ['price', 'cost', 'how much', 'berapa', 'harga', 'murah', 'mahal', '价格', '多少钱', '价钱', '费用', '收费'],
                'reply' => [
                    'en' => "💰 Pricing varies by product brand and market rate — it is not fixed.\n\nFor the most accurate pricing:\n• Browse our Products page for listed prices\n• WhatsApp us at {$p} for installation quotes\n\nWe'll find the best option for your budget! 🙌",
                    'ms' => "💰 Harga bergantung kepada jenama produk dan harga pasaran — ia tidak tetap.\n\nUntuk harga terkini:\n• Layari halaman Produk kami\n• WhatsApp kami di {$p} untuk sebut harga pemasangan\n\nKami akan carikan pilihan terbaik untuk bajet anda! 🙌",
                    'zh' => "💰 价格因品牌和市场行情而异，并非固定价格。\n\n如需了解最新价格：\n• 浏览我们的产品页面\n• WhatsApp 我们：{$p} 获取安装报价\n\n我们会为您找到最合适的选择！🙌",
                ],
            ],

            // Warranty
            [
                'priority' => 70,
                'keywords' => ['warranty', 'guarantee', 'waranti', 'jaminan', '保固', '保修', '质保', '保障'],
                'reply' => [
                    'en' => "✅ Warranty info:\n\nWarranty is NOT fixed — it depends on the product brand and market price. Higher-end branded products generally carry longer warranty periods.\n\n⚠️ Special note on air conditioning (aircond):\n• Original / OEM aircond parts → NO warranty\n• Second-hand / refurbished aircond parts → WITH warranty\n\nFor specific warranty details on any product, WhatsApp us at {$p}! 😊",
                    'ms' => "✅ Maklumat waranti:\n\nWaranti TIDAK tetap — ia bergantung kepada jenama produk dan harga pasaran. Produk jenama premium biasanya mempunyai tempoh waranti yang lebih lama.\n\n⚠️ Nota khas untuk aircond:\n• Alat ganti aircond original / OEM → TIADA waranti\n• Alat ganti aircond second-hand / refurbished → ADA waranti\n\nUntuk maklumat waranti produk tertentu, WhatsApp kami di {$p}! 😊",
                    'zh' => "✅ 保修资讯：\n\n保修期并非固定，视产品品牌和市场价格而定。一般来说，品牌越高端，保修期越长。\n\n⚠️ 冷气（aircond）特别说明：\n• 原装 / OEM 冷气零件 → 没有保修\n• 二手 / 翻新冷气零件 → 有保修\n\n如需了解具体产品保修详情，请 WhatsApp 我们：{$p}！😊",
                ],
            ],

            // Air conditioning / aircond
            [
                'priority' => 75,
                'keywords' => ['aircond', 'air cond', 'aircon', 'air con', 'air conditioning', 'cooling', 'sejuk', 'penyejuk', '冷气', '空调', '冷气机', '空气调节'],
                'reply' => [
                    'en' => "❄️ We carry aircond parts and accessories!\n\n⚠️ Important warranty note:\n• Original / OEM aircond parts → NO warranty\n• Second-hand / refurbished aircond parts → WITH warranty\n\nPricing is based on brand and market rate. WhatsApp us at {$p} for availability and quotes! 🚗",
                    'ms' => "❄️ Kami menyediakan alat ganti dan aksesori aircond!\n\n⚠️ Nota waranti penting:\n• Alat ganti aircond original / OEM → TIADA waranti\n• Alat ganti aircond second-hand / refurbished → ADA waranti\n\nHarga bergantung kepada jenama dan kadar pasaran. WhatsApp kami di {$p} untuk stok dan sebut harga! 🚗",
                    'zh' => "❄️ 我们提供冷气零件与配件！\n\n⚠️ 重要保修说明：\n• 原装 / OEM 冷气零件 → 没有保修\n• 二手 / 翻新冷气零件 → 有保修\n\n价格依品牌及市场行情而定。请 WhatsApp 我们：{$p} 查询库存和报价！🚗",
                ],
            ],

            // Takeaway / pickup
            [
                'priority' => 60,
                'keywords' => ['takeaway', 'take away', 'pickup', 'pick up', 'collect', 'self collect', 'ambil sendiri', '自取', '取货', '自己取', '上门取'],
                'reply' => [
                    'en' => "🛍️ Yes, we offer a pickup (takeaway) service!\n\nYou can order your products and collect them from our Shah Alam showroom at your convenience.\n\nTo arrange a pickup order, WhatsApp us at {$p} and we'll prepare everything for you! 😊",
                    'ms' => "🛍️ Ya, kami menyediakan perkhidmatan ambil sendiri (takeaway)!\n\nAnda boleh pesan produk dan ambil sendiri di kedai kami di Shah Alam mengikut masa yang sesuai.\n\nUntuk membuat pesanan ambil sendiri, WhatsApp kami di {$p}! 😊",
                    'zh' => "🛍️ 是的，我们提供自取（外带）服务！\n\n您可以先下单，然后到我们莎阿南门店自行取货，时间灵活方便。\n\n如需安排自取订单，请 WhatsApp 我们：{$p}，我们提前为您备好！😊",
                ],
            ],

            // Car audio / sound system
            [
                'priority' => 75,
                'keywords' => ['audio', 'sound', 'speaker', 'subwoofer', 'amplifier', 'head unit', 'stereo', 'woofer', '音响', '音箱', '喇叭', '低音', 'sistem bunyi'],
                'reply' => [
                    'en' => "🔊 We specialise in car audio systems:\n\n• Head units & infotainment\n• Speakers & tweeters\n• Subwoofers & amplifiers\n• Sound deadening\n\nWhatsApp us at {$p} for a free recommendation based on your car and budget! 🎵",
                    'ms' => "🔊 Kami pakar dalam sistem audio kereta:\n\n• Head unit & infotainment\n• Pembesar suara & tweeter\n• Subwoofer & amplifier\n• Pengurang bunyi\n\nWhatsApp kami di {$p} untuk cadangan percuma mengikut kereta dan bajet anda! 🎵",
                    'zh' => "🔊 我们专注于汽车音响系统：\n\n• 主机与娱乐系统\n• 喇叭与高音单元\n• 低音炮与功放\n• 隔音处理\n\nWhatsApp 我们：{$p}，根据您的车型和预算提供免费推荐！🎵",
                ],
            ],

            // Tint / window film
            [
                'priority' => 75,
                'keywords' => ['tint', 'window film', 'solar film', 'uv', 'cermin', 'tinted', '贴膜', '隔热膜', '车窗', '防晒'],
                'reply' => [
                    'en' => "🌟 We offer a range of window tinting options:\n\n• Solar / UV-rejection film\n• Privacy tint\n• Various shades and brands\n\nPricing depends on brand and market rate. WhatsApp us at {$p} for options! 😊",
                    'ms' => "🌟 Kami menawarkan pelbagai pilihan tinted tingkap:\n\n• Filem solar / penolak UV\n• Tinted privasi\n• Pelbagai ton dan jenama\n\nHarga bergantung kepada jenama dan kadar pasaran. WhatsApp kami di {$p}! 😊",
                    'zh' => "🌟 我们提供多种车窗隔热膜选择：\n\n• 太阳能 / 隔紫外线膜\n• 私密型深色膜\n• 多种颜色深度与品牌\n\n价格依品牌和市场行情而定。WhatsApp 我们：{$p} 了解详情！😊",
                ],
            ],

            // Dashcam / camera
            [
                'priority' => 75,
                'keywords' => ['dashcam', 'dash cam', 'camera', 'recorder', 'kamera', '行车记录', '记录仪', '摄像', '录像'],
                'reply' => [
                    'en' => "📷 We carry a wide selection of dashcams:\n\n• Front & dual-channel (front + rear)\n• 4K / Full HD resolution\n• Night vision & GPS models\n• Parking mode supported\n\nWhatsApp us at {$p} to find the right dashcam for your car! 🚗",
                    'ms' => "📷 Kami menyediakan pelbagai jenis dashcam:\n\n• Hadapan & dwi-saluran (depan + belakang)\n• Resolusi 4K / Full HD\n• Model penglihatan malam & GPS\n• Sokongan mod letak kereta\n\nWhatsApp kami di {$p} untuk cadangan terbaik! 🚗",
                    'zh' => "📷 我们备有多款行车记录仪：\n\n• 单镜头及双镜头（前+后）\n• 4K / Full HD 分辨率\n• 夜视及 GPS 款式\n• 支持停车监控模式\n\nWhatsApp 我们：{$p} 为您推荐合适型号！🚗",
                ],
            ],

            // Wrap / PPF / ceramic
            [
                'priority' => 75,
                'keywords' => ['wrap', 'ppf', 'paint protection', 'coating', 'ceramic', 'bungkus', 'salut', '贴纸', '车贴', '镀晶', '车漆', '保护膜'],
                'reply' => [
                    'en' => "✨ We offer car protection services:\n\n• Paint Protection Film (PPF)\n• Ceramic coating\n• Full & partial car wrap\n\nKeep your car looking new! WhatsApp us at {$p} for a quote. 🚘",
                    'ms' => "✨ Kami menyediakan servis perlindungan kereta:\n\n• Filem Perlindungan Cat (PPF)\n• Salutan seramik\n• Bungkus kereta penuh & separa\n\nJaga kereta anda supaya sentiasa cantik! WhatsApp kami di {$p} untuk sebut harga. 🚘",
                    'zh' => "✨ 我们提供汽车保护服务：\n\n• 漆面保护膜（PPF）\n• 镀晶处理\n• 整车及局部车身贴膜\n\n让您的爱车保持如新！WhatsApp 我们：{$p} 获取报价。🚘",
                ],
            ],

            // Accessories (general — low priority so specific items win)
            [
                'priority' => 30,
                'keywords' => ['product', 'accessory', 'accessories', 'aksesori', 'produk', 'sell', 'stock', 'item', '产品', '配件', '有没有', '卖', '货'],
                'reply' => [
                    'en' => "🛒 We carry a wide range of car accessories:\n\n• Car audio & infotainment\n• Dashcams & cameras\n• Window tinting\n• Seat covers & floor mats\n• LED lighting\n• Air conditioning parts\n• Car fresheners & organizers\n• And much more!\n\nVisit our Products page or WhatsApp us at {$p}! 😊",
                    'ms' => "🛒 Kami menyediakan pelbagai aksesori kereta:\n\n• Audio & infotainment kereta\n• Dashcam & kamera\n• Tinted tingkap\n• Sarung tempat duduk & alas lantai\n• Lampu LED\n• Alat ganti aircond\n• Pewangi & pengatur kereta\n• Dan banyak lagi!\n\nLayari halaman Produk kami atau WhatsApp di {$p}! 😊",
                    'zh' => "🛒 我们提供种类齐全的汽车配件：\n\n• 音响与娱乐系统\n• 行车记录仪与摄像头\n• 车窗隔热膜\n• 座椅套与脚垫\n• LED 灯饰\n• 冷气零件\n• 车内芳香剂与收纳用品\n• 以及更多！\n\n欢迎浏览产品页面，或 WhatsApp 我们：{$p}！😊",
                ],
            ],

            // Installation
            [
                'priority' => 60,
                'keywords' => ['install', 'installation', 'fitting', 'pasang', 'pemasangan', '安装', '师傅'],
                'reply' => [
                    'en' => "🔧 Yes, we provide professional installation for all products we sell!\n\nOur trained technicians ensure everything is fitted safely and neatly. Book a slot on our Booking page or WhatsApp us at {$p} to arrange an appointment. 🛠️",
                    'ms' => "🔧 Ya, kami menyediakan perkhidmatan pemasangan profesional untuk semua produk yang kami jual!\n\nTeknikal terlatih kami memastikan semua dipasang dengan selamat dan kemas. Tempah slot di halaman Tempahan atau WhatsApp kami di {$p}! 🛠️",
                    'zh' => "🔧 是的，我们为所有销售的产品提供专业安装服务！\n\n我们的技师经过专业培训，确保每一件产品安装安全整洁。请在预约页面选择时间段，或 WhatsApp 我们：{$p} 预约！🛠️",
                ],
            ],

            // Payment
            [
                'priority' => 55,
                'keywords' => ['payment', 'pay', 'cash', 'card', 'transfer', 'bayar', 'tunai', 'ewallet', '付款', '支付', '转账', '刷卡', '现金'],
                'reply' => [
                    'en' => "💳 We accept multiple payment methods:\n\n• Cash\n• Credit / Debit card\n• Online banking / bank transfer\n• E-wallets (Touch 'n Go, GrabPay, etc.)\n\nFor any payment questions, WhatsApp us at {$p}! 😊",
                    'ms' => "💳 Kami menerima pelbagai kaedah pembayaran:\n\n• Tunai\n• Kad kredit / debit\n• Perbankan dalam talian / pindahan bank\n• E-dompet (Touch 'n Go, GrabPay, dll.)\n\nUntuk soalan pembayaran, WhatsApp kami di {$p}! 😊",
                    'zh' => "💳 我们接受多种付款方式：\n\n• 现金\n• 信用卡 / 储蓄卡\n• 网上银行 / 银行转账\n• 电子钱包（Touch 'n Go、GrabPay 等）\n\n如有付款疑问，请 WhatsApp 我们：{$p}！😊",
                ],
            ],

            // Delivery / shipping
            [
                'priority' => 55,
                'keywords' => ['delivery', 'ship', 'shipping', 'courier', 'hantar', 'pos', '快递', '送货', '邮寄'],
                'reply' => [
                    'en' => "🚚 We primarily serve walk-in customers at our Shah Alam showroom.\n\nWe also offer a pickup (takeaway) service — order and collect at your convenience!\n\nFor delivery arrangements, WhatsApp us at {$p} to discuss options. 📦",
                    'ms' => "🚚 Kami terutamanya melayan pelanggan yang hadir ke kedai di Shah Alam.\n\nKami juga menyediakan perkhidmatan ambil sendiri — pesan dan ambil mengikut masa anda!\n\nUntuk penghantaran, WhatsApp kami di {$p}. 📦",
                    'zh' => "🚚 我们主要为莎阿南门店到访客户提供服务。\n\n我们也提供自取（外带）服务，预订后按时来取货即可！\n\n如需快递或送货安排，请 WhatsApp 我们：{$p} 洽谈。📦",
                ],
            ],

            // Contact / WhatsApp
            [
                'priority' => 50,
                'keywords' => ['contact', 'whatsapp', 'call', 'phone', 'number', 'hubungi', 'telefon', '联系', '电话', '联络', '号码'],
                'reply' => [
                    'en' => "📞 Reach us via:\n\nWhatsApp: {$p}\n\nOr use the Contact Us page on this website. We reply within a few hours during business hours! 😊",
                    'ms' => "📞 Hubungi kami melalui:\n\nWhatsApp: {$p}\n\nAtau gunakan halaman Hubungi Kami di laman web ini. Kami membalas dalam beberapa jam semasa waktu perniagaan! 😊",
                    'zh' => "📞 联系我们：\n\nWhatsApp：{$p}\n\n或使用本网站的「联系我们」页面留言，我们在营业时间内几小时内回复！😊",
                ],
            ],

            // Privacy / personal data (Privacy Policy)
            [
                'priority' => 60,
                'keywords' => ['privacy', 'personal data', 'my data', 'data protection', 'pdpa', 'cookie', 'cookies', 'sell my data', 'privasi', 'data peribadi', '隐私', '个人资料', '个人信息', '数据', '保密'],
                'reply' => [
                    'en' => "🔒 Your privacy matters to us:\n\n• We only collect what we need (name, phone, email, vehicle details).\n• We NEVER sell your personal data.\n• Only basic session cookies — no ad tracking.\n• You can view, correct, or delete your data anytime.\n\nWe comply with Malaysia's PDPA 2010. Full details are on our Privacy Policy page. 😊",
                    'ms' => "🔒 Privasi anda penting bagi kami:\n\n• Kami hanya kumpul apa yang perlu (nama, telefon, e-mel, butiran kenderaan).\n• Kami TIDAK PERNAH jual data peribadi anda.\n• Kuki sesi asas sahaja — tiada penjejakan iklan.\n• Anda boleh lihat, betulkan atau padam data anda bila-bila masa.\n\nKami patuhi PDPA 2010 Malaysia. Butiran penuh di halaman Dasar Privasi. 😊",
                    'zh' => "🔒 我们非常重视您的隐私：\n\n• 只收集必要信息（姓名、电话、邮箱、车辆资料）。\n• 我们绝不出售您的个人数据。\n• 仅使用基本会话 cookie——无广告追踪。\n• 您可随时查看、更正或删除您的数据。\n\n我们遵守马来西亚 PDPA 2010 法规。详情见「隐私政策」页面。😊",
                ],
            ],

            // Terms of service / copyright
            [
                'priority' => 58,
                'keywords' => ['terms of service', 'terms', 'tos', 'agreement', 'intellectual property', 'copyright', 'terma', 'syarat perkhidmatan', '条款', '服务条款', '使用条款', '版权', '知识产权', '协议'],
                'reply' => [
                    'en' => "📋 Our Terms of Service in brief:\n\n• Product info & prices are kept accurate — please confirm final fitment & stock with us.\n• Online bookings/orders are confirmed by our team before fulfilment.\n• Please use the site responsibly.\n• All content & logos are our property. © Win Win Car Audio 2026.\n\nFull terms are on our Terms of Service page. 😊",
                    'ms' => "📋 Ringkasan Terma Perkhidmatan kami:\n\n• Maklumat produk & harga dijaga tepat — sahkan kesesuaian & stok dengan kami.\n• Tempahan/pesanan dalam talian disahkan oleh pasukan kami dahulu.\n• Sila guna laman ini dengan bertanggungjawab.\n• Semua kandungan & logo ialah harta kami. © Win Win Car Audio 2026.\n\nTerma penuh di halaman Terma Perkhidmatan. 😊",
                    'zh' => "📋 我们的服务条款简述：\n\n• 产品信息与价格力求准确——请向我们确认最终适配性与库存。\n• 线上预约/订单需经团队确认后才处理。\n• 请合理使用本网站。\n• 网站所有内容与标志均为我们所有。© Win Win Car Audio 2026。\n\n完整条款见「服务条款」页面。😊",
                ],
            ],

            // Vehicle compatibility / fitment (FAQ)
            [
                'priority' => 72,
                'keywords' => ['fit my car', 'compatible', 'compatibility', 'will it fit', 'suitable for', 'fitment', 'does it fit', 'sesuai dengan kereta', 'serasi', 'muat', '适合我的车', '兼容', '适配', '合适吗', '能装吗', '装得上'],
                'reply' => [
                    'en' => "🚗 Great question! To check if a product fits your car:\n\n1. Use the compatibility guidance on the product pages\n2. Tell us your car make, model & year\n3. We'll confirm fitment before you buy or visit\n\nFor a guaranteed match, WhatsApp us at {$p} with your car details. 😊",
                    'ms' => "🚗 Soalan bagus! Untuk semak kesesuaian produk dengan kereta anda:\n\n1. Guna panduan keserasian di halaman produk\n2. Beritahu kami jenama, model & tahun kereta anda\n3. Kami sahkan kesesuaian sebelum anda beli atau datang\n\nUntuk kepastian, WhatsApp kami di {$p}. 😊",
                    'zh' => "🚗 好问题！要确认产品是否适合您的车：\n\n1. 参考产品页面的兼容性说明\n2. 告诉我们您的车品牌、型号与年份\n3. 购买或到店前我们会帮您确认适配\n\n想要确保匹配，请把车辆信息 WhatsApp 给我们：{$p}。😊",
                ],
            ],

            // Installation duration (FAQ)
            [
                'priority' => 68,
                'keywords' => ['how long', 'duration', 'how much time', 'take long', 'how many hours', 'berapa lama', 'berapa jam', '多久', '多长时间', '要多久', '需要多久', '几个小时', '几小时'],
                'reply' => [
                    'en' => "⏱️ Installation time depends on the job:\n\n• Basic audio setup: ~1–2 hours\n• Dashcam / window tint: ~1–3 hours\n• Full custom installation: up to a full day\n\nWe'll give you an estimate when you book. WhatsApp {$p} for specifics! 😊",
                    'ms' => "⏱️ Masa pemasangan bergantung pada kerja:\n\n• Set audio asas: ~1–2 jam\n• Dashcam / tinted: ~1–3 jam\n• Pemasangan custom penuh: sehingga sehari\n\nKami beri anggaran semasa anda tempah. WhatsApp {$p} untuk butiran! 😊",
                    'zh' => "⏱️ 安装时间视项目而定：\n\n• 基础音响：约 1–2 小时\n• 行车记录仪 / 车窗膜：约 1–3 小时\n• 全套定制安装：最多一整天\n\n预约时我们会给您预估时间。详情请 WhatsApp {$p}！😊",
                ],
            ],

            // Custom installation (FAQ)
            [
                'priority' => 66,
                'keywords' => ['custom install', 'customize', 'customise', 'custom build', 'custom setup', 'bespoke', 'pemasangan khas', 'custom audio', '定制', '改装', '个性化', '量身'],
                'reply' => [
                    'en' => "🛠️ Yes — we love custom builds! We offer tailored car audio & accessory installations designed around your vehicle and goals.\n\nTell us what you have in mind and your car details. WhatsApp us at {$p} or visit the showroom to plan it with our team. 🔊",
                    'ms' => "🛠️ Ya — kami suka projek custom! Kami tawarkan pemasangan audio & aksesori yang disesuaikan dengan kereta dan matlamat anda.\n\nBeritahu kami idea anda dan butiran kereta. WhatsApp {$p} atau datang ke kedai untuk rancang bersama pasukan kami. 🔊",
                    'zh' => "🛠️ 当然——我们最喜欢定制项目！我们提供量身打造的汽车音响与配件安装，贴合您的车型与需求。\n\n告诉我们您的想法和车辆信息，WhatsApp {$p} 或到店与团队一起规划。🔊",
                ],
            ],

            // Cancel / reschedule booking (FAQ)
            [
                'priority' => 64,
                'keywords' => ['cancel booking', 'cancel my booking', 'cancel appointment', 'cancel my appointment', 'reschedule', 'change my booking', 'batal tempahan', 'tukar tempahan', '取消预约', '改预约', '改时间', '取消预订'],
                'reply' => [
                    'en' => "📆 To cancel or reschedule:\n\nUse your booking manage link (saved when you booked) to review or cancel before your appointment time. You can also look it up on the 'Track Booking' page using your details.\n\nNeed a hand? WhatsApp us at {$p}. 😊",
                    'ms' => "📆 Untuk batal atau tukar tarikh:\n\nGuna pautan urus tempahan anda (disimpan semasa tempah) untuk semak atau batal sebelum masa temujanji. Anda juga boleh cari di halaman 'Jejak Tempahan'.\n\nPerlu bantuan? WhatsApp {$p}. 😊",
                    'zh' => "📆 取消或改期：\n\n使用您预约时保存的「管理链接」可在预约时间前查看或取消。也可以在「预约查询」页面用您的资料查找。\n\n需要帮忙？WhatsApp 我们：{$p}。😊",
                ],
            ],

            // Online shopping availability (FAQ)
            [
                'priority' => 48,
                'keywords' => ['sell online', 'buy online', 'order online', 'online shopping', 'shop online', 'online store', 'beli online', 'jual online', '网购', '网上买', '在线购买', '能不能网购', '线上下单'],
                'reply' => [
                    'en' => "🛒 Online shopping may be turned on or off depending on current operations.\n\nIf prices or the 'Add to Cart' button are hidden, the easiest way is to WhatsApp us at {$p} — we'll confirm stock, pricing, and arrange pickup or installation for you. 😊",
                    'ms' => "🛒 Pembelian dalam talian mungkin dibuka atau ditutup bergantung pada operasi semasa.\n\nJika harga atau butang 'Tambah ke Troli' disembunyikan, cara paling mudah ialah WhatsApp kami di {$p} — kami sahkan stok, harga dan aturkan ambil sendiri atau pemasangan. 😊",
                    'zh' => "🛒 在线购买功能可能根据当前运营情况开启或关闭。\n\n如果价格或「加入购物车」按钮被隐藏，最简单的方式是 WhatsApp 我们：{$p}——我们会确认库存、价格，并为您安排自取或安装。😊",
                ],
            ],

            // Languages supported (FAQ)
            [
                'priority' => 45,
                'keywords' => ['language', 'languages', 'speak english', 'speak chinese', 'speak malay', 'bahasa', 'cakap', '语言', '说中文', '会中文', '英文', '马来文'],
                'reply' => [
                    'en' => "🌐 Yes! This site and our chat support English, Bahasa Malaysia, and Chinese (中文).\n\nUse the language switcher at the top to change anytime. Our showroom team can assist in these languages too! 😊",
                    'ms' => "🌐 Ya! Laman dan sembang ini menyokong Bahasa Inggeris, Bahasa Malaysia dan Cina (中文).\n\nGuna penukar bahasa di atas untuk tukar bila-bila masa. Pasukan kedai kami juga boleh membantu dalam bahasa-bahasa ini! 😊",
                    'zh' => "🌐 当然！本网站和聊天支持英文、马来文和中文。\n\n使用顶部的语言切换器可随时更改。我们的门店团队也能用这些语言为您服务！😊",
                ],
            ],

            // Promotions / discounts
            [
                'priority' => 55,
                'keywords' => ['discount', 'promo', 'promotion', 'offer', 'deal', 'voucher', 'coupon', 'cheaper', 'diskaun', 'tawaran', '折扣', '优惠', '促销', '便宜', '划算'],
                'reply' => [
                    'en' => "🎉 We often run promotions on selected items and installation packages!\n\nPrices vary by brand and market rate, so for the latest deals and the best price for your budget, WhatsApp us at {$p}. We'll sort you out! 💪",
                    'ms' => "🎉 Kami kerap ada promosi untuk item terpilih dan pakej pemasangan!\n\nHarga berbeza ikut jenama dan pasaran. Untuk tawaran terkini dan harga terbaik, WhatsApp kami di {$p}. 💪",
                    'zh' => "🎉 我们经常有精选商品和安装套餐的优惠活动！\n\n价格依品牌和市场行情而定，想了解最新优惠和最划算的价格，请 WhatsApp 我们：{$p}。💪",
                ],
            ],

            // Returns / refunds / exchange
            [
                'priority' => 65,
                'keywords' => ['refund', 'return', 'exchange', 'replace', 'faulty', 'defective', 'cancel order', 'pulangkan', 'tukar barang', 'rosak', '退款', '退货', '换货', '退换', '坏了', '取消订单'],
                'reply' => [
                    'en' => "🔄 For returns, exchanges, or a faulty item, our team will help you sort it out fairly.\n\nPlease have your order details ready and WhatsApp us at {$p}, or bring the item to our Shah Alam showroom. 😊",
                    'ms' => "🔄 Untuk pemulangan, pertukaran atau barang rosak, pasukan kami akan bantu uruskan dengan adil.\n\nSediakan butiran pesanan anda dan WhatsApp kami di {$p}, atau bawa barang ke kedai kami di Shah Alam. 😊",
                    'zh' => "🔄 关于退货、换货或商品故障，我们的团队会公平地为您处理。\n\n请准备好订单信息并 WhatsApp 我们：{$p}，或将商品带到莎阿南门店。😊",
                ],
            ],

            // Thank you
            [
                'priority' => 15,
                'keywords' => ['thank', 'thanks', 'terima kasih', 'tq', 'thx', '谢谢', '感谢', '多谢', 'tqsm'],
                'reply' => [
                    'en' => "You're welcome! 😊 If you have more questions, feel free to ask. Hope to see you at Win Win Car Studio! 🚗✨",
                    'ms' => "Sama-sama! 😊 Jika ada soalan lain, jangan segan untuk bertanya. Jumpa di Win Win Car Studio! 🚗✨",
                    'zh' => "不客气！😊 如有其他问题随时来问。期待在 Win Win Car Studio 见到您！🚗✨",
                ],
            ],
        ]);
    }

    /**
     * Admin-managed FAQ entries (Filament → Chatbot FAQs), merged ahead of the
     * built-in rules so the shop can add or override answers without code.
     * Cached for an hour; the ChatbotFaq model busts the cache on save/delete.
     */
    private function dbFaqRules(): array
    {
        if (! Schema::hasTable('chatbot_faqs')) {
            return [];
        }

        return Cache::remember('chatbot_faqs', 3600, function () {
            return ChatbotFaq::where('is_active', true)
                ->get()
                ->map(fn (ChatbotFaq $faq) => [
                    'priority' => $faq->priority,
                    'keywords' => array_values(array_filter(array_map(
                        fn ($k) => mb_strtolower(trim($k)),
                        $faq->keywords ?? []
                    ))),
                    'reply' => array_filter([
                        'en' => $faq->reply_en,
                        'ms' => $faq->reply_ms,
                        'zh' => $faq->reply_zh,
                    ]),
                ])
                ->filter(fn ($rule) => $rule['keywords'] !== [] && $rule['reply'] !== [])
                ->values()
                ->all();
        });
    }

    /**
     * Typo-tolerant keyword check. CJK keywords use substring matching; short
     * latin keywords (≤3 chars like "hi") must match as whole words so they
     * don't fire inside unrelated words ("hi" inside "c-hi-nese"); longer latin
     * keywords keep substring matching so stems still catch variants ("tint" →
     * "tinted") and tolerate small typos ("warrenty" → "warranty").
     */
    private function keywordMatches(string $keyword, string $message, array $messageWords): bool
    {
        // CJK has no word boundaries — substring is the correct match there.
        $isLatin = ! preg_match('/[^\x00-\x7f]/', $keyword);

        // Short latin keywords: whole-word match only (fixes "hi" ⊂ "chinese").
        if ($isLatin && mb_strlen($keyword) <= 3) {
            return (bool) preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $message);
        }

        if (str_contains($message, $keyword)) {
            return true;
        }

        $len = mb_strlen($keyword);
        if ($len < 5 || str_contains($keyword, ' ') || ! $isLatin) {
            return false;
        }

        $maxDistance = $len >= 8 ? 2 : 1;
        foreach ($messageWords as $word) {
            if (abs(strlen($word) - $len) <= $maxDistance && levenshtein($word, $keyword) <= $maxDistance) {
                return true;
            }
        }

        return false;
    }

    /**
     * Curated topic list used for "did you mean…" suggestions when nothing
     * matches. Each topic carries the query a tap should send (per language).
     */
    private function topics(): array
    {
        return [
            ['keywords' => ['hour', 'open', 'close', 'waktu', 'buka', '营业', '开门'],
             'label' => ['en' => 'Opening hours', 'ms' => 'Waktu operasi', 'zh' => '营业时间'],
             'query' => ['en' => 'What are your operating hours?', 'ms' => 'Apakah waktu operasi anda?', 'zh' => '你们的营业时间是几点？']],
            ['keywords' => ['location', 'address', 'direction', 'alamat', 'lokasi', '地址', '位置'],
             'label' => ['en' => 'Location', 'ms' => 'Lokasi', 'zh' => '门店地址'],
             'query' => ['en' => 'Where are you located?', 'ms' => 'Di mana lokasi anda?', 'zh' => '你们在哪里？']],
            ['keywords' => ['book', 'appointment', 'tempah', '预约'],
             'label' => ['en' => 'Book appointment', 'ms' => 'Buat tempahan', 'zh' => '预约服务'],
             'query' => ['en' => 'I want to book an appointment', 'ms' => 'Saya mahu buat tempahan', 'zh' => '我想预约']],
            ['keywords' => ['price', 'cost', 'harga', 'berapa', '价格', '多少钱'],
             'label' => ['en' => 'Pricing', 'ms' => 'Harga', 'zh' => '价格'],
             'query' => ['en' => 'How much does it cost?', 'ms' => 'Berapakah harganya?', 'zh' => '价格多少？']],
            ['keywords' => ['warranty', 'guarantee', 'waranti', '保修', '保固'],
             'label' => ['en' => 'Warranty', 'ms' => 'Waranti', 'zh' => '保修'],
             'query' => ['en' => 'What is your warranty policy?', 'ms' => 'Apakah polisi waranti anda?', 'zh' => '保修政策是怎样的？']],
            ['keywords' => ['audio', 'speaker', 'subwoofer', 'sound', '音响', '喇叭'],
             'label' => ['en' => 'Car audio', 'ms' => 'Audio kereta', 'zh' => '汽车音响'],
             'query' => ['en' => 'Tell me about car audio systems', 'ms' => 'Ceritakan tentang sistem audio kereta', 'zh' => '介绍一下汽车音响系统']],
            ['keywords' => ['tint', 'film', 'tinted', '贴膜', '隔热膜'],
             'label' => ['en' => 'Window tinting', 'ms' => 'Tinted tingkap', 'zh' => '车窗隔热膜'],
             'query' => ['en' => 'Tell me about window tinting', 'ms' => 'Ceritakan tentang tinted tingkap', 'zh' => '介绍一下车窗隔热膜']],
            ['keywords' => ['dashcam', 'camera', 'kamera', '记录仪', '行车'],
             'label' => ['en' => 'Dashcams', 'ms' => 'Dashcam', 'zh' => '行车记录仪'],
             'query' => ['en' => 'Tell me about dashcams', 'ms' => 'Ceritakan tentang dashcam', 'zh' => '介绍一下行车记录仪']],
            ['keywords' => ['install', 'fitting', 'pasang', '安装'],
             'label' => ['en' => 'Installation', 'ms' => 'Pemasangan', 'zh' => '安装服务'],
             'query' => ['en' => 'Do you provide installation?', 'ms' => 'Adakah anda menyediakan pemasangan?', 'zh' => '你们提供安装服务吗？']],
            ['keywords' => ['product', 'accessories', 'produk', '产品', '配件'],
             'label' => ['en' => 'Products', 'ms' => 'Produk', 'zh' => '产品'],
             'query' => ['en' => 'What products do you sell?', 'ms' => 'Apakah produk yang anda jual?', 'zh' => '你们卖什么产品？']],
        ];
    }

    /**
     * "Did you mean…" chips for the fallback reply: fuzzy-score every topic
     * against the message and return the closest few (or popular defaults).
     */
    private function fallbackSuggestions(string $lang, string $message): array
    {
        $messageWords = $this->latinWords($message);
        $scored = [];

        foreach ($this->topics() as $i => $topic) {
            $score = 0;
            foreach ($topic['keywords'] as $keyword) {
                if ($this->keywordMatches($keyword, $message, $messageWords)) {
                    $score += 10;
                    continue;
                }
                // Partial similarity nudges near-misses up the list.
                foreach ($messageWords as $word) {
                    similar_text($word, $keyword, $percent);
                    if ($percent >= 70) {
                        $score += 3;
                        break;
                    }
                }
            }
            $scored[$i] = $score;
        }

        arsort($scored);
        $best = array_slice(array_keys($scored), 0, 3, true);

        // If nothing is even close, offer the most popular topics instead.
        if (($scored[array_key_first(array_slice($scored, 0, 1, true))] ?? 0) === 0) {
            $best = [0, 2, 9]; // hours, booking, products
        }

        $topics = $this->topics();

        return array_map(fn ($i) => [
            'label' => $topics[$i]['label'][$lang] ?? $topics[$i]['label']['en'],
            'query' => $topics[$i]['query'][$lang] ?? $topics[$i]['query']['en'],
        ], $best);
    }

    private function latinWords(string $message): array
    {
        preg_match_all('/[a-z0-9]+/', $message, $m);

        return array_values(array_filter($m[0], fn ($w) => strlen($w) >= 3));
    }

    /**
     * Handle everyday questions (date, time, identity, small talk) that need
     * dynamic answers or should always win over keyword topics. Returns null
     * when no everyday intent is matched.
     */
    private function dynamicReply(string $lang, string $msg): ?string
    {
        $p = $this->phone;

        // Current time
        if (preg_match('/\b(time now|current time|what time is it|what\'?s the time|time right now)\b/iu', $msg)
            || preg_match('/(jam berapa sekarang|pukul berapa sekarang)/iu', $msg)
            || preg_match('/(现在几点|几点了|现在时间|现在的时间)/u', $msg)) {
            return $this->timeReply($lang);
        }

        // Today's date / day
        if (preg_match('/\b(what(\'?s)? (the )?date|today\'?s date|date today|what day is it|date now|what is the date)\b/iu', $msg)
            || preg_match('/(tarikh hari ini|hari ini tarikh|hari apa hari ini|hari ini hari apa)/iu', $msg)
            || preg_match('/(今天几号|今天日期|今天星期几|今天礼拜几|几月几号)/u', $msg)) {
            return $this->dateReply($lang);
        }

        // Identity / capabilities
        if (preg_match('/\b(who are you|what are you|who is this|who am i talking to|introduce yourself|tell me about yourself|what(\'?s| is) your name|your name|who r u|are you (a )?(bot|robot|ai|human|real|person))\b/iu', $msg)
            || preg_match('/(siapa awak|awak siapa|anda siapa|awak ni siapa|awak ni apa|kenalkan diri|nama awak|apa yang boleh awak buat|awak (ni )?(bot|robot|ai))/iu', $msg)
            || preg_match('/(你是谁|你是什么|你是啥|你是个什么|你叫什么|你叫什么名字|你的名字|介绍一下你自己|自我介绍|你能做什么|你会什么|你能帮我什么|你是机器人吗|你是真人吗|你是人吗|你是不是机器人)/u', $msg)) {
            return match ($lang) {
                'ms' => "Saya Pembantu Win Win 🚗 — chatbot AI rasmi untuk Win Win Car Studio (kedai audio & aksesori kereta di Shah Alam).\n\nSaya boleh bantu anda dengan:\n• Produk & aksesori\n• Servis & pemasangan\n• Tempahan bengkel\n• Harga, waktu operasi & lokasi\n\nApa yang boleh saya bantu hari ini? 😊",
                'zh' => "我是 Win Win 智能助手 🚗 —— Win Win Car Studio（莎阿南的汽车音响与配件店）的官方 AI 聊天机器人。\n\n我可以帮您解答：\n• 产品与配件\n• 服务与安装\n• 工坊预约\n• 价格、营业时间与地址\n\n今天有什么可以帮您？😊",
                default => "I'm the Win Win Assistant 🚗 — the official AI chatbot for Win Win Car Studio (a car audio & accessories shop in Shah Alam).\n\nI can help you with:\n• Products & accessories\n• Services & installation\n• Workshop bookings\n• Pricing, hours & location\n\nWhat can I help you with today? 😊",
            };
        }

        // How are you
        if (preg_match('/\b(how are you|how r u|how are u|how\'?s it going|apa khabar|how do you do)\b/iu', $msg)
            || preg_match('/(你好吗|最近好吗|过得怎么样)/u', $msg)) {
            return match ($lang) {
                'ms' => "Saya baik, terima kasih! 😊 Sedia membantu anda. Ada apa-apa tentang kereta anda yang boleh saya bantu hari ini?",
                'zh' => "我很好，谢谢您！😊 随时为您服务。今天有什么关于爱车的问题我可以帮忙吗？",
                default => "I'm doing great, thanks for asking! 😊 I'm here to help. Is there anything about your car I can assist with today?",
            };
        }

        // Goodbye
        if (preg_match('/\b(bye|goodbye|see you|see ya|good night)\b/iu', $msg)
            || preg_match('/(selamat tinggal|jumpa lagi|babai)/iu', $msg)
            || preg_match('/(再见|拜拜|晚安)/u', $msg)) {
            return match ($lang) {
                'ms' => "Selamat tinggal! 👋 Terima kasih kerana berkunjung. Jumpa lagi di Win Win Car Studio! 🚗",
                'zh' => "再见！👋 感谢您的到访，期待在 Win Win Car Studio 再次见到您！🚗",
                default => "Goodbye! 👋 Thanks for stopping by. Hope to see you at Win Win Car Studio soon! 🚗",
            };
        }

        // "Who am I?" — the bot doesn't track the visitor's identity.
        if (preg_match('/\b(who am i|do you know me|what(\'?s| is) my name|who am i then)\b/iu', $msg)
            || preg_match('/(我是谁|你知道我是谁|我叫什么)/u', $msg)
            || preg_match('/(siapa saya|nama saya apa|awak kenal saya)/iu', $msg)) {
            return match ($lang) {
                'ms' => "Demi privasi, saya tak simpan maklumat peribadi anda 🔒 — jadi saya tak tahu nama anda. Tapi bagi saya, anda pelanggan istimewa kami! 😊\n\nApa yang boleh saya bantu hari ini?",
                'zh' => "为了保护隐私，我不会记录您的个人信息 🔒，所以我不知道您的名字。不过在我这儿，您就是我们的贵宾！😊\n\n有什么可以帮您？",
                default => "For privacy, I don't store your personal details 🔒 — so I don't know your name. But to me, you're our valued customer! 😊\n\nWhat can I help you with today?",
            };
        }

        // Talk to a real human / agent
        if (preg_match('/\b(human|real person|a person|agent|customer service|customer support|speak to (someone|staff)|talk to (someone|staff|a human)|live chat)\b/iu', $msg)
            || preg_match('/(真人|人工|客服|找人|转人工)/u', $msg)
            || preg_match('/(cakap dengan (orang|staf)|orang sebenar|khidmat pelanggan)/iu', $msg)) {
            return match ($lang) {
                'ms' => "Tiada masalah! 🙋 Pasukan manusia kami sedia membantu.\n\n📞 WhatsApp: {$p}\nAtau lawati kedai kami di Shah Alam. Mereka biasanya membalas dalam beberapa jam pada waktu perniagaan.",
                'zh' => "没问题！🙋 我们的真人团队随时为您服务。\n\n📞 WhatsApp：{$p}\n或到访莎阿南门店。营业时间内通常几小时内回复您。",
                default => "No problem! 🙋 Our human team is happy to help.\n\n📞 WhatsApp: {$p}\nOr visit our Shah Alam showroom. They usually reply within a few hours during business hours.",
            };
        }

        // Are you open now? (live, based on Malaysia time)
        if (preg_match('/\b(open now|are you open|you open|currently open|open right now|still open|open today)\b/iu', $msg)
            || preg_match('/(现在.*营业|现在开.*吗|你们开.*吗|现在开门吗|营业中吗|现在有营业吗)/u', $msg)
            || preg_match('/(buka sekarang|tengah buka|ada buka|buka tak)/iu', $msg)) {
            return $this->openNowReply($lang);
        }

        // Short acknowledgements (whole message only)
        if (preg_match('/^(ok|okay|k|kk|yes|yeah|yep|yup|no|nope|sure|alright|fine|got it|noted|cool)$/iu', trim($msg))
            || preg_match('/^(好|好的|好滴|可以|嗯|嗯嗯|行|没事|没有|不用了|知道了|明白)$/u', trim($msg))
            || preg_match('/^(ok|okay|baik|boleh|takpe|faham|noted)$/iu', trim($msg))) {
            return match ($lang) {
                'ms' => "Baik! 😊 Ada apa-apa lagi yang boleh saya bantu?",
                'zh' => "好的！😊 还有什么可以帮您的吗？",
                default => "Got it! 😊 Is there anything else I can help you with?",
            };
        }

        // Compliments
        if (preg_match('/\b(good (bot|job)|well done|you(\'?re| are) (great|helpful|awesome|amazing|smart|the best|good|nice)|nice work|love you|i like you|so helpful)\b/iu', $msg)
            || preg_match('/(你真棒|你好棒|做得好|你很厉害|你太聪明|我喜欢你|爱你|你真好|你很好)/u', $msg)
            || preg_match('/(awak (bagus|hebat|pandai|terbaik)|bagus la|terima kasih banyak)/iu', $msg)) {
            return match ($lang) {
                'ms' => "Terima kasih! 🥰 Anda sangat baik. Saya sentiasa di sini untuk membantu — tanya saja bila-bila masa! 🚗✨",
                'zh' => "谢谢您！🥰 您太客气了。我随时都在，有需要随时问我哦！🚗✨",
                default => "Aww, thank you! 🥰 You're too kind. I'm always here to help — ask me anything, anytime! 🚗✨",
            };
        }

        // Complaints / frustration → apologise + hand off to humans
        if (preg_match('/\b(useless|stupid|dumb|terrible|awful|hate (you|this)|you(\'?re| are) (bad|useless|stupid|dumb|annoying)|not helpful|doesn\'?t help|worst|rubbish)\b/iu', $msg)
            || preg_match('/(没用|废物|笨|蠢|垃圾|讨厌你|不好用|没帮助|烂|答非所问)/u', $msg)
            || preg_match('/(tak guna|bodoh|teruk|benci|tak membantu|menyusahkan)/iu', $msg)) {
            return match ($lang) {
                'ms' => "Maaf jika saya tak dapat membantu dengan baik. 🙏 Pasukan manusia kami pasti boleh bantu lebih lanjut.\n\n📞 WhatsApp: {$p}\nKami menghargai maklum balas anda!",
                'zh' => "很抱歉没能帮到您。🙏 我们的真人团队一定能为您提供更好的帮助。\n\n📞 WhatsApp：{$p}\n感谢您的反馈！",
                default => "I'm sorry I couldn't help better. 🙏 Our human team can definitely assist you further.\n\n📞 WhatsApp: {$p}\nWe appreciate your feedback!",
            };
        }

        return null;
    }

    private function openNowReply(string $lang): string
    {
        $now = now()->setTimezone('Asia/Kuala_Lumpur');
        $dow = (int) $now->dayOfWeek; // 0 = Sun … 6 = Sat
        $minutes = (int) $now->format('H') * 60 + (int) $now->format('i');
        $t = $now->format('g:i A');

        // Friday (5) is the rest day.
        if ($dow === 5) {
            return match ($lang) {
                'ms' => "❌ Tidak, kami TUTUP hari ini (Jumaat hari rehat kami). Sekarang {$t}.\n\nKami buka semula esok. 😊",
                'zh' => "❌ 抱歉，今天我们休息（周五是固定休息日）。现在 {$t}。\n\n我们明天恢复营业。😊",
                default => "❌ No, we're CLOSED today (Friday is our rest day). It's {$t} now.\n\nWe reopen tomorrow. 😊",
            };
        }

        $openAt = 10 * 60 + 30;                 // 10:30 AM
        $closeAt = $dow === 0 ? 18 * 60 : 20 * 60; // Sun 6 PM, else 8 PM
        $isOpen = $minutes >= $openAt && $minutes < $closeAt;

        if ($isOpen) {
            return match ($lang) {
                'ms' => "✅ Ya, kami BUKA sekarang! 🎉 Masa sekarang {$t}.\n\nSinggahlah ke kedai kami di Shah Alam, atau WhatsApp {$this->phone}.",
                'zh' => "✅ 是的，我们现在正在营业！🎉 现在 {$t}。\n\n欢迎到访莎阿南门店，或 WhatsApp {$this->phone}。",
                default => "✅ Yes, we're OPEN now! 🎉 It's {$t}.\n\nDrop by our Shah Alam showroom, or WhatsApp {$this->phone}.",
            };
        }

        return match ($lang) {
            'ms' => "🔴 Maaf, kami TUTUP sekarang ({$t}).\n\nWaktu operasi: Isnin–Khamis & Sabtu 10:30 PG–8:00 MLM, Ahad 10:30 PG–6:00 PTG, Jumaat tutup.",
            'zh' => "🔴 抱歉，我们现在不在营业时间内（{$t}）。\n\n营业时间：周一至周四及周六 10:30–20:00，周日 10:30–18:00，周五休息。",
            default => "🔴 Sorry, we're CLOSED right now ({$t}).\n\nHours: Mon–Thu & Sat 10:30 AM–8:00 PM, Sun 10:30 AM–6:00 PM, closed Friday.",
        };
    }

    private function timeReply(string $lang): string
    {
        $now = now()->setTimezone('Asia/Kuala_Lumpur');

        return match ($lang) {
            'ms' => "🕐 Masa sekarang ialah " . $now->format('g:i A') . " (waktu Malaysia).\n\nIngat, kami tutup setiap hari Jumaat ya! 😊",
            'zh' => "🕐 现在是 " . $now->format('g:i A') . "（马来西亚时间）。\n\n提醒您，我们每周五休息哦！😊",
            default => "🕐 The time right now is " . $now->format('g:i A') . " (Malaysia time).\n\nJust a reminder — we're closed every Friday! 😊",
        };
    }

    private function dateReply(string $lang): string
    {
        $now = now()->setTimezone('Asia/Kuala_Lumpur');
        $dow = (int) $now->dayOfWeek; // 0 = Sunday … 6 = Saturday
        $monthIndex = (int) $now->month - 1;

        if ($lang === 'ms') {
            $days = ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu'];
            $months = ['Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
            $note = $dow === 5 ? "\n\n⚠️ Hari ini Jumaat — kami tutup hari ini." : '';
            return "📅 Hari ini ialah {$days[$dow]}, " . $now->format('j') . " {$months[$monthIndex]} " . $now->format('Y') . ".{$note}";
        }

        if ($lang === 'zh') {
            $days = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
            $note = $dow === 5 ? "\n\n⚠️ 今天是星期五——我们今天休息。" : '';
            return "📅 今天是 " . $now->format('Y') . " 年 " . $now->format('n') . " 月 " . $now->format('j') . " 日，{$days[$dow]}。{$note}";
        }

        $note = $dow === 5 ? "\n\n⚠️ It's Friday today — we're closed." : '';
        return "📅 Today is " . $now->format('l, F j, Y') . ".{$note}";
    }

    /**
     * Expand common English chat slang / abbreviations so intent matching is
     * robust to "what are u", "where r u", "wat time", "hru", etc.
     * Word-boundary anchored so it never touches Chinese or mid-word letters.
     */
    private function normalizeSlang(string $msg): string
    {
        $replacements = [
            '/\bwru\b/iu'   => 'who are you',
            '/\bhru\b/iu'   => 'how are you',
            '/\bwdyd\b/iu'  => 'what do you do',
            '/\bur\b/iu'    => 'your',
            '/\bu\b/iu'     => 'you',
            '/\br\b/iu'     => 'are',
            '/\bwa[t]+\b/iu' => 'what',   // wat, watt
            '/\bwut\b/iu'   => 'what',
            '/\bwot\b/iu'   => 'what',
            '/\bwanna\b/iu' => 'want to',
            '/\bgonna\b/iu' => 'going to',
            '/\bpls\b/iu'   => 'please',
            '/\bplz\b/iu'   => 'please',
            '/\bdis\b/iu'   => 'this',
            '/\bda\b/iu'    => 'the',
            '/\bn\b/iu'     => 'and',
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $msg) ?? $msg;
    }

    /**
     * Live answers backed by the database, so pricing and product info never
     * drift from what the admin panel says. Returns null when not applicable.
     */
    private function liveDataReply(string $lang, string $msg): ?array
    {
        return $this->servicePriceReply($lang, $msg) ?? $this->productSearchReply($lang, $msg);
    }

    /**
     * Real service prices: "how much is tinting?" answers with the live
     * price and duration of the matched service from the services table.
     */
    private function servicePriceReply(string $lang, string $msg): ?array
    {
        if (! Schema::hasTable('services')) {
            return null;
        }

        $wantsPrice = (bool) preg_match('/price|cost|how much|berapa|harga|多少钱|价格|价钱|收费|rm\b/iu', $msg);
        if (! $wantsPrice) {
            return null;
        }

        // Cache plain arrays (not Eloquent models) so any cache store can
        // serialise them safely.
        $services = collect(Cache::remember('chatbot_services', 600, fn () => Service::where('is_active', true)
            ->get(['name', 'price', 'duration_minutes'])
            ->map(fn (Service $s) => [
                'name' => $s->name,
                'price' => $s->price,
                'duration_label' => $s->duration_label,
            ])
            ->all()));

        // Aliases let BM/ZH (and common shorthand) hit English service names.
        $aliases = [
            'tint'      => ['tint', 'tinted', 'film', '隔热膜', '贴膜', '车窗'],
            'audio'     => ['audio', 'speaker', 'head unit', 'stereo', '音响', '喇叭', 'bunyi'],
            'subwoofer' => ['subwoofer', 'amplifier', 'woofer', 'bass', '低音', '功放'],
            'dashcam'   => ['dashcam', 'dash cam', 'kamera', '记录仪', '行车记录'],
            'alarm'     => ['alarm', 'security', 'immobilizer', 'penggera', '防盗', '警报'],
            'dsp'       => ['dsp', 'tuning', 'calibration', 'penalaan', '调音', '校准'],
        ];

        $matched = $services->filter(function (array $service) use ($msg, $aliases) {
            $name = mb_strtolower($service['name']);
            foreach ($aliases as $stem => $words) {
                if (str_contains($name, $stem)) {
                    foreach ($words as $word) {
                        if (str_contains($msg, $word)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        });

        if ($matched->isEmpty()) {
            return null;
        }

        $lines = $matched->take(3)->map(function (array $service) use ($lang) {
            $name = in_array($lang, ['ms', 'zh'], true) ? __($service['name'], [], $lang) : $service['name'];
            $price = $service['price'] ? 'RM ' . number_format((float) $service['price'], 0) : null;

            return '• ' . $name
                . ($price ? match ($lang) {
                    'ms' => " — dari {$price}",
                    'zh' => " — {$price} 起",
                    default => " — from {$price}",
                } : '')
                . ($service['duration_label'] ? " ({$service['duration_label']})" : '');
        })->implode("\n");

        $message = match ($lang) {
            'ms' => "💰 Ini harga semasa untuk servis berkaitan:\n\n{$lines}\n\nHarga akhir bergantung pada kereta dan produk pilihan anda. Tempah slot atau WhatsApp {$this->phone} untuk sebut harga tepat! 😊",
            'zh' => "💰 相关服务的最新价格：\n\n{$lines}\n\n最终价格视车型和所选产品而定。可以直接预约,或 WhatsApp {$this->phone} 获取准确报价！😊",
            default => "💰 Here are our current prices for the related services:\n\n{$lines}\n\nFinal pricing depends on your car and chosen products. Book a slot or WhatsApp {$this->phone} for an exact quote! 😊",
        };

        return [
            'message' => $message,
            'suggestions' => [[
                'label' => match ($lang) { 'ms' => 'Buat tempahan', 'zh' => '立即预约', default => 'Book appointment' },
                'query' => match ($lang) { 'ms' => 'Saya mahu buat tempahan', 'zh' => '我想预约', default => 'I want to book an appointment' },
            ]],
        ];
    }

    /**
     * Live product lookup: "do you have Sony?" / "got 70mai dashcam?" searches
     * the real catalogue and answers with names and prices.
     */
    private function productSearchReply(string $lang, string $msg): ?array
    {
        if (! Schema::hasTable('products')) {
            return null;
        }

        $hasIntent = (bool) preg_match('/\b(have|got|sell|stock|carry|looking for|find|any)\b|ada\b|jual|cari|有没有|有卖|有.*吗|找|卖不卖/iu', $msg);

        // Brand names from the DB count as intent on their own ("Sony?").
        $brands = Schema::hasTable('brands')
            ? Cache::remember('chatbot_brands', 600, fn () => Brand::pluck('name')->map(fn ($b) => mb_strtolower($b))->all())
            : [];
        $mentionedBrand = collect($brands)->first(fn ($b) => str_contains($msg, $b));

        if (! $hasIntent && ! $mentionedBrand) {
            return null;
        }

        $query = Product::where('is_active', true);

        if ($mentionedBrand) {
            $query->where(fn ($q) => $q->where('brand', 'like', "%{$mentionedBrand}%")
                ->orWhere('name', 'like', "%{$mentionedBrand}%"));
        } else {
            // Search by the message's meaningful words against name/brand.
            $words = array_filter($this->latinWords($msg), fn ($w) => strlen($w) >= 4
                && ! in_array($w, ['have', 'sell', 'stock', 'carry', 'looking', 'find', 'what', 'your', 'does', 'this', 'that', 'with'], true));
            if ($words === []) {
                return null;
            }
            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('name', 'like', "%{$word}%")->orWhere('brand', 'like', "%{$word}%");
                }
            });
        }

        $products = $query->take(3)->get();

        // Brand recognised but nothing in the catalogue right now — still a
        // useful answer (we carry the brand; stock changes often).
        if ($products->isEmpty() && $mentionedBrand) {
            $brandName = mb_convert_case($mentionedBrand, MB_CASE_TITLE);
            $message = match ($lang) {
                'ms' => "👍 Ya, kami ada jenama {$brandName}! Stok berubah dari semasa ke semasa, jadi WhatsApp kami di {$this->phone} untuk semak model dan harga terkini. 😊",
                'zh' => "👍 有的,我们有 {$brandName} 这个品牌！库存型号经常更新,请 WhatsApp 我们：{$this->phone} 查询最新型号和价格。😊",
                default => "👍 Yes, we carry {$brandName}! Stock changes from time to time, so WhatsApp us at {$this->phone} to check the latest models and prices. 😊",
            };

            return ['message' => $message, 'suggestions' => []];
        }

        if ($products->isEmpty()) {
            return null; // fall through to keyword rules / fallback
        }

        $lines = $products->map(function (Product $product) {
            $price = $product->current_price ? ' — RM ' . number_format((float) $product->current_price, 0) : '';
            return "• {$product->name}" . ($product->brand ? " ({$product->brand})" : '') . $price;
        })->implode("\n");

        $message = match ($lang) {
            'ms' => "🛒 Ya, kami ada! Ini padanan terbaik daripada katalog kami:\n\n{$lines}\n\nLayari halaman Produk untuk butiran penuh, atau WhatsApp {$this->phone} untuk semak stok. 😊",
            'zh' => "🛒 有的！以下是我们目录中最匹配的产品：\n\n{$lines}\n\n可以到产品页面查看详情,或 WhatsApp {$this->phone} 确认库存。😊",
            default => "🛒 Yes, we do! Here are the closest matches from our catalogue:\n\n{$lines}\n\nBrowse the Products page for full details, or WhatsApp {$this->phone} to confirm stock. 😊",
        };

        return [
            'message' => $message,
            'suggestions' => [[
                'label' => match ($lang) { 'ms' => 'Lihat semua produk', 'zh' => '查看全部产品', default => 'View all products' },
                'query' => match ($lang) { 'ms' => 'Apakah produk yang anda jual?', 'zh' => '你们卖什么产品？', default => 'What products do you sell?' },
            ]],
        ];
    }

    public function chat(array $messages, ?string $systemPrompt = null): array
    {
        $lang = $this->lang($systemPrompt ?? '');
        $raw = mb_strtolower(trim(collect($messages)->last()['content'] ?? ''));
        // Expand common chat slang ("what are u" → "what are you") so intents match.
        $lastMessage = $this->normalizeSlang($raw);
        $messageWords = $this->latinWords($lastMessage);

        $suggestions = [];
        $isFallback = false;

        // 1) Everyday / dynamic intents (date, time, identity, small talk).
        $reply = $this->dynamicReply($lang, $lastMessage);

        // 2) Live database answers (real service prices, product search).
        if ($reply === null && ($live = $this->liveDataReply($lang, $lastMessage))) {
            $reply = $live['message'];
            $suggestions = $live['suggestions'] ?? [];
        }

        // 3) Keyword topics (admin FAQs + built-ins), scored by priority +
        //    matched keywords, with typo tolerance. A single message can
        //    mention several topics; answer the top few (max 3).
        if ($reply === null) {
            $scored = [];

            foreach ($this->rules() as $rule) {
                $matches = 0;
                foreach ($rule['keywords'] as $keyword) {
                    if ($this->keywordMatches($keyword, $lastMessage, $messageWords)) {
                        $matches++;
                    }
                }

                if ($matches === 0) {
                    continue;
                }

                $scored[] = [
                    'score' => ($rule['priority'] ?? 40) + $matches * 3,
                    'reply' => $rule['reply'][$lang] ?? $rule['reply']['en'],
                ];
            }

            if (! empty($scored)) {
                // Highest score first; stable so equal scores keep rule order.
                usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

                $topicCount = count($scored);
                $answers = array_map(fn ($s) => $s['reply'], array_slice($scored, 0, 3));
                $reply = implode("\n\n— — —\n\n", $answers);

                if ($topicCount > 3) {
                    $reply .= "\n\n" . match ($lang) {
                        'ms' => "👆 Anda bertanya beberapa perkara sekali gus — ini yang utama. Untuk selebihnya, tanya satu persatu ya! 😊",
                        'zh' => "👆 您一次问了好几个问题，以上是主要的几个。其余的可以一个一个问我哦！😊",
                        default => "👆 You asked about a few things at once — here are the main ones. For the rest, just ask me one at a time! 😊",
                    };
                }
            }
        }

        // 4) Fallback — "did you mean…" chips + hand-off to the human team.
        if ($reply === null) {
            $isFallback = true;
            $p = $this->phone;
            $reply = match ($lang) {
                'ms' => "Hmm, saya tidak pasti tentang itu. 🤔 Mungkin anda maksudkan salah satu topik di bawah?\n\nJika tidak, pasukan kami sedia membantu — WhatsApp {$p} untuk respons pantas. 😊",
                'zh' => "嗯,这个问题我不太确定。🤔 您是不是想问下面这些？\n\n如果都不是,我们的团队随时为您服务——WhatsApp {$p} 获得最快回复。😊",
                default => "Hmm, I'm not sure about that one. 🤔 Did you mean one of the topics below?\n\nIf not, our team is happy to help — WhatsApp us at {$p} for the fastest response. 😊",
            };
            $suggestions = $this->fallbackSuggestions($lang, $lastMessage);
        }

        AiLog::record([
            'driver'           => 'mock',
            'feature'          => 'chat',
            'request_payload'  => ['messages' => $messages, 'lang' => $lang],
            'response_payload' => ['message' => $reply],
            // 'fallback' lets the admin filter unanswered questions in AI Logs
            // and turn them into Chatbot FAQ entries.
            'status'           => $isFallback ? 'fallback' : 'success',
            'ip_address'       => request()->ip(),
        ]);

        return ['message' => $reply, 'suggestions' => $suggestions];
    }

    public function recommend(string $query, Collection $products): array
    {
        $recommendations = $products
            ->take(3)
            ->map(fn (Product $product) => [
                'product_id' => $product->id,
                'reason'     => 'Recommended based on your query.',
            ])
            ->values()
            ->all();

        $response = [
            'recommendations' => $recommendations,
            'follow_up'       => 'For a confirmed fitment check, WhatsApp us at ' . $this->phone . '.',
        ];

        AiLog::record([
            'driver'           => 'mock',
            'feature'          => 'recommend',
            'request_payload'  => ['query' => $query],
            'response_payload' => $response,
            'status'           => 'success',
            'ip_address'       => request()->ip(),
        ]);

        return $response;
    }

    public function generateDescription(Product $product): array
    {
        $response = [
            'en' => "{$product->name} is a quality car accessory designed for compatibility, durability, and value.",
            'ms' => "{$product->name} ialah aksesori kereta berkualiti yang direka untuk keserasian, ketahanan, dan nilai.",
            'zh' => "{$product->name} 是一款注重兼容性、耐用性与性价比的优质汽车配件。",
        ];

        AiLog::record([
            'driver'           => 'mock',
            'feature'          => 'generate_description',
            'request_payload'  => ['product_id' => $product->id],
            'response_payload' => $response,
            'status'           => 'success',
            'ip_address'       => request()->ip(),
        ]);

        return $response;
    }
}
