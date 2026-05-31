<?php

namespace App\Services\Ai;

use App\Contracts\AiServiceInterface;
use App\Models\AiLog;
use App\Models\Product;
use Illuminate\Support\Collection;

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

        return [
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
        ];
    }

    /**
     * Handle everyday questions (date, time, identity, small talk) that need
     * dynamic answers or should always win over keyword topics. Returns null
     * when no everyday intent is matched.
     */
    private function dynamicReply(string $lang, string $msg): ?string
    {
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

        return null;
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

    public function chat(array $messages, ?string $systemPrompt = null): string
    {
        $lang = $this->lang($systemPrompt ?? '');
        $raw = mb_strtolower(trim(collect($messages)->last()['content'] ?? ''));
        // Expand common chat slang ("what are u" → "what are you") so intents match.
        $lastMessage = $this->normalizeSlang($raw);

        // 1) Everyday / dynamic intents (date, time, identity, small talk).
        $reply = $this->dynamicReply($lang, $lastMessage);

        // 2) Keyword topics, scored by priority + number of matched keywords.
        //    A single message can mention several topics; answer the top few
        //    (max 3) instead of silently dropping all but one, and tell the
        //    user to ask the rest one at a time.
        if ($reply === null) {
            $scored = [];

            foreach ($this->rules() as $rule) {
                $matches = 0;
                foreach ($rule['keywords'] as $keyword) {
                    if (str_contains($lastMessage, $keyword)) {
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

        // 3) Fallback — direct to the human team.
        $p = $this->phone;
        $reply ??= match ($lang) {
            'ms' => "Terima kasih atas pertanyaan anda! 😊\n\nSaya tidak pasti tentang itu, tetapi pasukan kami boleh membantu.\n\n📞 WhatsApp kami di {$p} untuk respons pantas, atau singgah ke kedai kami di Shah Alam semasa waktu operasi.",
            'zh' => "感谢您的提问！😊\n\n这个问题我不太确定，但我们的团队可以为您解答。\n\n📞 请 WhatsApp 我们：{$p}，或在营业时间内到访我们的莎阿南门店。",
            default => "Thanks for your message! 😊\n\nI'm not sure about that, but our team can help you directly.\n\n📞 WhatsApp us at {$p} for the fastest response, or visit our Shah Alam showroom during business hours.",
        };

        AiLog::record([
            'driver'           => 'mock',
            'feature'          => 'chat',
            'request_payload'  => ['messages' => $messages, 'lang' => $lang],
            'response_payload' => ['message' => $reply],
            'status'           => 'success',
            'ip_address'       => request()->ip(),
        ]);

        return $reply;
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
