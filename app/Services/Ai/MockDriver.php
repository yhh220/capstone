<?php

namespace App\Services\Ai;

use App\Contracts\AiServiceInterface;
use App\Models\AiLog;
use App\Models\Product;
use Illuminate\Support\Collection;

class MockDriver implements AiServiceInterface
{
    private string $phone = '016-915 0917';

    private function rules(): array
    {
        $p = $this->phone;

        return [
            // Greetings
            ['keywords' => ['hi', 'hello', 'hey', 'morning', 'afternoon', 'evening', 'good day', 'hai', 'apa khabar', 'selamat', '你好', '哈喽', '嗨'],
             'reply' => "Hi there! 👋 Welcome to Win Win Car Studio.\n\nI can help you with:\n• Products & accessories\n• Workshop bookings\n• Operating hours & location\n• Installation & pricing info\n\nWhat can I help you with today?"],

            // Operating hours
            ['keywords' => ['hour', 'open', 'close', 'time', 'when', 'waktu', 'buka', 'tutup', '营业', '时间', '开门', '关门'],
             'reply' => "🕐 Our operating hours:\n\nMon – Thu: 10:30 AM – 8:00 PM\nFriday: CLOSED (rest day)\nSaturday: 10:30 AM – 8:00 PM\nSunday: 10:30 AM – 6:00 PM\n\nWe're closed every Friday. Plan your visit accordingly! 😊"],

            // Location / address
            ['keywords' => ['location', 'address', 'where', 'direction', 'map', 'find', 'alamat', 'mana', 'tempat', '地址', '在哪', '位置', '怎么去'],
             'reply' => "📍 We are located in Shah Alam, Selangor.\n\nFor the exact address and directions, please WhatsApp us at {$p} — we'll send you a Google Maps pin right away! 🗺️"],

            // Booking / appointment
            ['keywords' => ['book', 'appointment', 'schedule', 'slot', 'reservation', 'tempah', 'temujanji', '预约', '预订', '约'],
             'reply' => "📅 To book a workshop appointment:\n\n1. Go to our Booking page from the menu\n2. Select your service and preferred time slot\n3. Fill in your details and confirm\n\nNeed help booking? WhatsApp us at {$p} and we'll sort it out for you! 😊"],

            // Price / cost
            ['keywords' => ['price', 'cost', 'how much', 'berapa', 'harga', 'murah', 'mahal', '价格', '多少钱', '价钱', '费用'],
             'reply' => "💰 Pricing depends on the product and service selected.\n\nFor the most accurate and up-to-date pricing, please:\n• Browse our Products page for listed prices\n• WhatsApp us at {$p} for installation quotes\n\nWe'll give you the best deal! 🙌"],

            // Car audio / sound system
            ['keywords' => ['audio', 'sound', 'speaker', 'subwoofer', 'amplifier', 'amp', 'head unit', 'stereo', 'music', 'woofer', '音响', '音箱', '喇叭', '低音'],
             'reply' => "🔊 We specialise in car audio systems including:\n\n• Head units & infotainment\n• Speakers & tweeters\n• Subwoofers & amplifiers\n• Sound deadening\n\nOur team can recommend the best setup for your car and budget. WhatsApp us at {$p} for a free consultation! 🎵"],

            // Tint / window film
            ['keywords' => ['tint', 'film', 'window', 'solar', 'uv', 'cermin', 'tingkap', '贴膜', '隔热膜', '车窗'],
             'reply' => "🌟 We offer a range of window tinting options:\n\n• Solar / UV-rejection film\n• Privacy tint\n• Various shades and brands\n\nFor pricing and brand options, WhatsApp us at {$p} or drop by the shop! 😊"],

            // Dashcam / camera
            ['keywords' => ['dashcam', 'dash cam', 'camera', 'cctv', 'recorder', 'recording', 'kamera', '行车记录', '记录仪', '摄像'],
             'reply' => "📷 We carry a wide selection of dashcams:\n\n• Front & dual-channel (front + rear)\n• 4K / Full HD resolution\n• Night vision & GPS models\n• Parking mode supported\n\nWhatsApp us at {$p} to find the right dashcam for your car! 🚗"],

            // Car wrap / paint protection
            ['keywords' => ['wrap', 'ppf', 'paint', 'protection', 'coating', 'ceramic', 'bungkus', 'salut', '贴纸', '车贴', '镀晶', '车漆'],
             'reply' => "✨ We offer car protection services:\n\n• Paint Protection Film (PPF)\n• Ceramic coating\n• Full & partial car wrap\n\nProtect your car's finish and keep it looking new! Contact us at {$p} for a quote. 🚘"],

            // Accessories (general)
            ['keywords' => ['product', 'accessory', 'accessories', 'aksesori', 'produk', 'sell', 'have', 'stock', '产品', '配件', '有没有', '卖'],
             'reply' => "🛒 We carry a wide range of car accessories:\n\n• Car audio & infotainment\n• Dashcams & cameras\n• Window tinting\n• Seat covers & mats\n• LED lighting\n• Car fresheners & organizers\n• And much more!\n\nVisit our Products page to browse, or WhatsApp us at {$p} for availability. 😊"],

            // Installation
            ['keywords' => ['install', 'installation', 'fit', 'fitting', 'pasang', 'pemasangan', '安装', '装', '上门'],
             'reply' => "🔧 Yes, we provide professional installation for all products we sell!\n\nOur trained technicians ensure everything is fitted safely and neatly. Book a workshop slot on our Booking page, or WhatsApp us at {$p} to arrange an appointment. 🛠️"],

            // Warranty
            ['keywords' => ['warranty', 'guarantee', 'waranti', 'jaminan', '保固', '保修', '质保'],
             'reply' => "✅ Warranty coverage depends on the brand and product.\n\nMost products come with manufacturer warranty. Our installation work is also covered for quality assurance.\n\nFor specific warranty details, please WhatsApp us at {$p} — we're happy to clarify! 😊"],

            // Payment
            ['keywords' => ['payment', 'pay', 'cash', 'card', 'online', 'transfer', 'bayar', 'tunai', '付款', '支付', '转账', '刷卡'],
             'reply' => "💳 We accept multiple payment methods:\n\n• Cash\n• Credit / Debit card\n• Online banking / bank transfer\n• E-wallets (Touch 'n Go, Grab Pay, etc.)\n\nFor any payment enquiries, feel free to WhatsApp us at {$p}! 😊"],

            // Delivery / shipping
            ['keywords' => ['delivery', 'ship', 'shipping', 'courier', 'send', 'hantar', 'pos', '快递', '送货', '邮寄'],
             'reply' => "🚚 We primarily serve walk-in customers at our Shah Alam showroom.\n\nFor delivery arrangements, please WhatsApp us at {$p} to discuss options and availability. 📦"],

            // Contact / WhatsApp
            ['keywords' => ['contact', 'whatsapp', 'call', 'phone', 'number', 'hubungi', 'telefon', '联系', '电话', '联络'],
             'reply' => "📞 You can reach us via:\n\nWhatsApp: {$p}\n\nOr use the Contact Us page on this website to send us a message. We typically reply within a few hours during business hours! 😊"],

            // Thank you
            ['keywords' => ['thank', 'thanks', 'terima kasih', 'tq', 'ty', 'thx', '谢谢', '感谢', '多谢'],
             'reply' => "You're welcome! 😊 If you have any other questions, feel free to ask. We're always happy to help!\n\nSee you at Win Win Car Studio! 🚗✨"],
        ];
    }

    public function chat(array $messages, ?string $systemPrompt = null): string
    {
        $lastMessage = mb_strtolower(collect($messages)->last()['content'] ?? '');

        $reply = null;

        foreach ($this->rules() as $rule) {
            foreach ($rule['keywords'] as $keyword) {
                if (str_contains($lastMessage, $keyword)) {
                    $reply = $rule['reply'];
                    break 2;
                }
            }
        }

        $reply ??= "Thanks for your message! 😊\n\nI'm not sure about that, but our team can help you directly.\n\n📞 WhatsApp us at {$this->phone} for the fastest response, or visit us at our Shah Alam showroom during business hours.\n\nIs there anything else I can help with?";

        AiLog::record([
            'driver' => 'mock',
            'feature' => 'chat',
            'request_payload' => ['messages' => $messages, 'system_prompt' => $systemPrompt],
            'response_payload' => ['message' => $reply],
            'status' => 'success',
            'ip_address' => request()->ip(),
        ]);

        return $reply;
    }

    public function recommend(string $query, Collection $products): array
    {
        $recommendations = $products
            ->take(3)
            ->map(fn (Product $product) => [
                'product_id' => $product->id,
                'reason' => 'Demo recommendation based on your query.',
            ])
            ->values()
            ->all();

        $response = [
            'recommendations' => $recommendations,
            'follow_up' => 'If you want a confirmed fitment check, WhatsApp us at 016-915 0917.',
        ];

        AiLog::record([
            'driver' => 'mock',
            'feature' => 'recommend',
            'request_payload' => ['query' => $query],
            'response_payload' => $response,
            'status' => 'success',
            'ip_address' => request()->ip(),
        ]);

        return $response;
    }

    public function generateDescription(Product $product): array
    {
        $response = [
            'en' => "{$product->name} is a reliable showroom-highlight accessory designed to improve compatibility, quality, and value for everyday drivers.",
            'ms' => "{$product->name} ialah aksesori pilihan yang menekankan keserasian, kualiti, dan nilai untuk pemandu harian.",
            'zh' => "{$product->name} 是一款强调兼容性、品质与性价比的精选汽车配件，适合日常驾驶使用。",
        ];

        AiLog::record([
            'driver' => 'mock',
            'feature' => 'generate_description',
            'request_payload' => ['product_id' => $product->id],
            'response_payload' => $response,
            'status' => 'success',
            'ip_address' => request()->ip(),
        ]);

        return $response;
    }
}
