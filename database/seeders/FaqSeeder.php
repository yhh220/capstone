<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Orders & Payment',
                'question_en' => 'Do you sell online?',
                'question_ms' => 'Adakah anda menjual dalam talian?',
                'question_zh' => '你们有在线销售吗？',
                'answer_en' => 'Online shopping may be turned on or off depending on current operations. When it is off, you can still browse everything and visit or WhatsApp us to buy. If prices are hidden, please WhatsApp the store for assistance.',
                'answer_ms' => 'Pembelian dalam talian mungkin dihidupkan atau dimatikan bergantung pada operasi semasa. Apabila ia dimatikan, anda masih boleh melihat semua produk dan datang atau WhatsApp kami untuk membeli. Jika harga disembunyikan, sila WhatsApp kedai untuk bantuan.',
                'answer_zh' => '在线购物可能会根据当前运营情况开启或关闭。关闭时，您仍可浏览所有产品，并亲临门店或通过 WhatsApp 向我们购买。如果价格被隐藏，请通过 WhatsApp 联系门店以获得协助。',
            ],
            [
                'category' => 'Bookings & Showroom',
                'question_en' => 'How do I book a visit?',
                'question_ms' => 'Bagaimana saya menempah lawatan?',
                'question_zh' => '我该如何预约到访？',
                'answer_en' => 'Booking is simply scheduling a time to drop by our Shah Alam showroom. Pick a date and time on the booking page and leave your contact details — choosing a specific service is optional, so you can also just book a general visit to look around.',
                'answer_ms' => 'Tempahan hanyalah menjadualkan masa untuk singgah ke bilik pameran kami di Shah Alam. Pilih tarikh dan masa di halaman tempahan dan tinggalkan butiran hubungan anda — memilih perkhidmatan tertentu adalah pilihan, jadi anda juga boleh menempah lawatan umum untuk melihat-lihat.',
                'answer_zh' => '预约只是安排时间到我们位于莎阿南的展厅。在预约页面选择日期和时间并留下您的联系方式 — 选择特定服务是可选的，因此您也可以只预约一次普通到访来看看。',
            ],
            [
                'category' => 'Products & Support',
                'question_en' => 'How do I know if a product fits my car?',
                'question_ms' => 'Bagaimana saya tahu sesuatu produk sesuai dengan kereta saya?',
                'question_zh' => '我怎么知道产品是否适合我的车？',
                'answer_en' => 'Use the compatibility guidance on the site, then contact the showroom on WhatsApp for a final confirmation before visiting or purchasing.',
                'answer_ms' => 'Gunakan panduan keserasian di laman web, kemudian hubungi bilik pameran melalui WhatsApp untuk pengesahan akhir sebelum melawat atau membeli.',
                'answer_zh' => '请使用网站上的兼容性指南，然后在到访或购买前通过 WhatsApp 联系展厅进行最终确认。',
            ],
            [
                'category' => 'Orders & Payment',
                'question_en' => 'Do you accept real online payments?',
                'question_ms' => 'Adakah anda menerima pembayaran dalam talian sebenar?',
                'question_zh' => '你们接受真实的在线付款吗？',
                'answer_en' => 'Online payments are currently processed through store confirmation. Our staff will guide you through the purchasing process.',
                'answer_ms' => 'Pembayaran dalam talian buat masa ini diproses melalui pengesahan kedai. Kakitangan kami akan membimbing anda melalui proses pembelian.',
                'answer_zh' => '在线付款目前通过门店确认处理。我们的员工将引导您完成购买流程。',
            ],
            [
                'category' => 'Bookings & Showroom',
                'question_en' => 'Can I cancel my booking?',
                'question_ms' => 'Bolehkah saya membatalkan tempahan saya?',
                'question_zh' => '我可以取消预约吗？',
                'answer_en' => 'Yes. Use your booking manage link, or look it up on the Track Booking page with your phone number or booking ID, to review or cancel before your appointment time.',
                'answer_ms' => 'Ya. Gunakan pautan urus tempahan anda, atau cari di halaman Jejak Tempahan dengan nombor telefon atau ID tempahan anda, untuk menyemak atau membatalkan sebelum masa temu janji anda.',
                'answer_zh' => '可以。在预约时间之前，使用您的预约管理链接，或在“追踪预约”页面用您的电话号码或预约编号查询，以查看或取消预约。',
            ],
            [
                'category' => 'Orders & Payment',
                'question_en' => 'Do you offer instalment plans?',
                'question_ms' => 'Adakah anda menawarkan pelan ansuran?',
                'question_zh' => '你们提供分期付款吗？',
                'answer_en' => 'We can help with instalment options on selected items and installation packages, depending on the price and the plan. WhatsApp us with the product you want and we will let you know the options.',
                'answer_ms' => 'Kami boleh membantu dengan pilihan ansuran untuk item terpilih dan pakej pemasangan, bergantung pada harga dan pelan. WhatsApp kami dengan produk yang anda mahukan dan kami akan memberitahu pilihan yang ada.',
                'answer_zh' => '针对部分商品和安装配套，我们可以协助安排分期选项，具体取决于价格和方案。请通过 WhatsApp 告诉我们您想要的产品，我们会告知可行的选项。',
            ],
            [
                'category' => 'Orders & Payment',
                'question_en' => 'Do you take trade-ins?',
                'question_ms' => 'Adakah anda menerima tukar beli (trade-in)?',
                'question_zh' => '你们接受以旧换新吗？',
                'answer_en' => 'Sometimes — it depends on the item and its condition. Send us a photo and details of your current unit on WhatsApp and our team will let you know if we can take it in.',
                'answer_ms' => 'Kadangkala — ia bergantung pada item dan keadaannya. Hantar gambar dan butiran unit semasa anda melalui WhatsApp dan pasukan kami akan memberitahu sama ada kami boleh menerimanya.',
                'answer_zh' => '有时可以 — 这取决于物品及其状况。请通过 WhatsApp 发送您现有设备的照片和详细信息，我们的团队会告知是否可以收取。',
            ],
            [
                'category' => 'Products & Support',
                'question_en' => 'Do you support multiple languages?',
                'question_ms' => 'Adakah anda menyokong pelbagai bahasa?',
                'question_zh' => '你们支持多种语言吗？',
                'answer_en' => 'Yes — the website and our chatbot support English, Bahasa Malaysia, and Chinese (including Traditional Chinese and mixed-language questions).',
                'answer_ms' => 'Ya — laman web dan chatbot kami menyokong Bahasa Inggeris, Bahasa Malaysia dan Bahasa Cina (termasuk Cina Tradisional dan soalan bercampur bahasa).',
                'answer_zh' => '支持 — 我们的网站和聊天机器人支持英文、马来文和中文（包括繁体中文以及混合语言的提问）。',
            ],
            [
                'category' => 'Bookings & Showroom',
                'question_en' => 'Where is your showroom located?',
                'question_ms' => 'Di manakah lokasi bilik pameran anda?',
                'question_zh' => '你们的展厅在哪里？',
                'answer_en' => 'Our showroom is located in Shah Alam, Selangor. Visit us during operating hours for a hands-on experience with our products and services. Contact us on WhatsApp for the exact address and directions.',
                'answer_ms' => 'Bilik pameran kami terletak di Shah Alam, Selangor. Lawati kami pada waktu operasi untuk pengalaman langsung dengan produk dan perkhidmatan kami. Hubungi kami melalui WhatsApp untuk alamat tepat dan arah jalan.',
                'answer_zh' => '我们的展厅位于雪兰莪州莎阿南。请在营业时间内到访，亲身体验我们的产品和服务。如需确切地址和路线，请通过 WhatsApp 联系我们。',
            ],
            [
                'category' => 'Bookings & Showroom',
                'question_en' => 'How long does installation take?',
                'question_ms' => 'Berapa lama masa pemasangan diambil?',
                'question_zh' => '安装需要多长时间？',
                'answer_en' => 'Installation time varies depending on the product and complexity. A basic audio setup may take 1–2 hours, while full custom installations may require a full day. Our team will give you an estimated time when you book.',
                'answer_ms' => 'Masa pemasangan berbeza bergantung pada produk dan kerumitan. Pemasangan audio asas mungkin mengambil masa 1–2 jam, manakala pemasangan tersuai penuh mungkin memerlukan satu hari penuh. Pasukan kami akan memberi anggaran masa semasa anda menempah.',
                'answer_zh' => '安装时间因产品和复杂程度而异。基本音响安装可能需要 1–2 小时，而全套定制安装可能需要一整天。预约时我们的团队会为您提供预计时间。',
            ],
            [
                'category' => 'Products & Support',
                'question_en' => 'Do you provide warranty for products?',
                'question_ms' => 'Adakah anda menyediakan waranti untuk produk?',
                'question_zh' => '你们的产品提供保修吗？',
                'answer_en' => 'Yes. Warranty coverage depends on the brand and product. Our staff will inform you of the warranty terms at the time of purchase. For warranty claims, please bring your receipt and visit the showroom.',
                'answer_ms' => 'Ya. Perlindungan waranti bergantung pada jenama dan produk. Kakitangan kami akan memberitahu anda terma waranti semasa pembelian. Untuk tuntutan waranti, sila bawa resit anda dan lawati bilik pameran.',
                'answer_zh' => '提供。保修范围取决于品牌和产品。购买时我们的员工会告知您保修条款。如需保修索赔，请携带收据并到访展厅。',
            ],
            [
                'category' => 'Products & Support',
                'question_en' => 'Can I request a custom installation?',
                'question_ms' => 'Bolehkah saya meminta pemasangan tersuai?',
                'question_zh' => '我可以要求定制安装吗？',
                'answer_en' => 'Absolutely. We offer custom audio and accessories installation tailored to your vehicle. Contact us via WhatsApp or visit the showroom to discuss your requirements with our team.',
                'answer_ms' => 'Sudah tentu. Kami menawarkan pemasangan audio dan aksesori tersuai yang disesuaikan dengan kenderaan anda. Hubungi kami melalui WhatsApp atau lawati bilik pameran untuk membincangkan keperluan anda dengan pasukan kami.',
                'answer_zh' => '当然可以。我们提供针对您车辆量身定制的音响和配件安装。请通过 WhatsApp 联系我们或到访展厅，与我们的团队讨论您的需求。',
            ],
            // NOTE: the hours/fee % below are today's CANCELLATION_FULL_REFUND_HOURS /
            // CANCELLATION_FEE_PERCENT setting defaults, written as plain text — this
            // FAQ answer does not auto-update if those settings change later. Keep it
            // in sync manually, or point customers to /cancellation-refund-policy,
            // which always shows the live numbers.
            [
                'category' => 'Orders & Payment',
                'question_en' => 'Can I cancel a paid order?',
                'question_ms' => 'Bolehkah saya membatalkan pesanan yang telah dibayar?',
                'question_zh' => '我可以取消已付款的订单吗？',
                'answer_en' => 'Yes, before it ships. Cancel within 24 hours of payment for a full refund. After that but before shipping, a 10% processing fee applies to the refund. Once an order has shipped, it can no longer be self-cancelled — see our Cancellation & Refund Policy page for full details, or use the Cancel Order button in My Account.',
                'answer_ms' => 'Boleh, sebelum ia dihantar. Batalkan dalam 24 jam selepas bayaran untuk bayaran balik penuh. Selepas itu tetapi sebelum penghantaran, yuran proses 10% akan ditolak daripada bayaran balik. Setelah pesanan dihantar, ia tidak lagi boleh dibatalkan sendiri — lihat halaman Polisi Pembatalan & Bayaran Balik kami untuk butiran penuh, atau guna butang Batal Pesanan di Akaun Saya.',
                'answer_zh' => '可以，但须在发货前取消。付款后 24 小时内取消可全额退款。超过此时限但尚未发货取消，将从退款中扣除 10% 的手续费。订单一旦发货，便无法再自行取消 — 完整详情请见我们的取消与退款政策页面，或在「我的账户」中使用取消订单按钮。',
            ],
            [
                'category' => 'Orders & Payment',
                'question_en' => 'What happens to an unpaid order if I don\'t pay?',
                'question_ms' => 'Apa yang berlaku kepada pesanan yang belum dibayar jika saya tidak membayar?',
                'question_zh' => '如果我不付款，未付款的订单会怎样？',
                'answer_en' => 'You can cancel it yourself any time from My Account at no cost, since nothing was ever charged. If you simply leave it, unpaid orders are automatically cancelled and the stock released after 15 minutes.',
                'answer_ms' => 'Anda boleh membatalkannya sendiri pada bila-bila masa melalui Akaun Saya tanpa kos, kerana tidak ada caj yang dikenakan. Jika anda hanya membiarkannya, pesanan yang belum dibayar akan dibatalkan secara automatik dan stok dilepaskan selepas 15 minit.',
                'answer_zh' => '由于从未产生任何费用，您可以随时在「我的账户」中免费自行取消该订单。如果您不作处理，未付款的订单将在 15 分钟后自动取消并释放库存。',
            ],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::updateOrCreate(
                ['question_en' => $faq['question_en']],
                array_merge($faq, ['sort_order' => $i + 1, 'is_active' => true]),
            );
        }
    }
}
