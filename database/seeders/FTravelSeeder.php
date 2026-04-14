<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Role;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class FTravelSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->firstOrCreate(['name' => 'admin'], ['description' => 'Quản trị hệ thống']);
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@ftravel.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'status' => 'active',
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $demoUser = User::query()->firstOrCreate(
            ['email' => 'test@gmail.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'loyalty_points' => 500,
            ]
        );
        if ((int) ($demoUser->loyalty_points ?? 0) < 500) {
            $demoUser->update(['loyalty_points' => 500]);
        }

        $domestic = Category::query()->create([
            'name' => 'Tour Trong Nước',
            'slug' => 'domestic',
            'description' => 'Trải nghiệm vẻ đẹp chữ S từ Hà Giang đến Mũi Cà Mau với đội ngũ hướng dẫn viên tận tâm nhất.',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDHaaiIpv6jidRaJ-9mK1fZ7Ka_Kqi7dU-P0-29J78WuTMZUgOtnxAsLIIeBciclAdZi0ubRn_4saeYFupuXQJoi4ETbESjhG5ZD6kZPHD66tbQ0B1zL3AfUPVS3VRl_G3RSBSW3ttVmfC1LojRYpZ81SnvD8nQ6_uZfstpB_OOqFqH6o58kBeeozqINnH8_g1jG-pYW5UsAh0Z2RlCndm4zWf_0Qd-mryz8Bh0XKDpUB3gXdS_pcqzIgV9rs5E9Q7V1mMumrZAPPE',
            'status' => 'active',
        ]);

        $europe = Category::query()->create([
            'name' => 'Châu Âu Lãng Mạn',
            'slug' => 'europe',
            'description' => 'Pháp - Ý - Thụy Sĩ',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx9SQX3POm0RsgFEV8Sih5xrdm4wfqBSwCSXnyKVVYa9xHm-JhvCTBzwoIT8_QEFaItJDdNKg_bJTk9ZfoMpY3BEVDyy8ioaCvr-7I8CmTyn83gVwW8sglWKBRN_9NnGlWgZddq-O9EoaJLJRQSFl-6MRI75cbBZ3rYPYWus2b9Po067n_4qhxnjjn0cyv0GFpKRbJQrcjXOOVD6OHCjsb3e5qq3Qf-BZ7H1vOJywEn6OPDz1-f4qIqima46Fx59XK0_o-U7vkpc4',
            'status' => 'active',
        ]);

        $asia = Category::query()->create([
            'name' => 'Châu Á Huyền Bí',
            'slug' => 'asia',
            'description' => 'Ấn Độ - Bhutan - Nepal',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCdi0XmMQl8uJ5RzAludcSMuo2BoZAmcB3ND7A9PwsUb_8rVhdMWHqFmMIKzLg47Wmw3uAuyAc37JuoGoqAoO2XZHRIEWWxhFTk1ePGdTazsf2CHRBxRHCN7mlsHZr4_4AjSKHlhFdn86gr30SOtBKdvaqElQqRC8HqUxKp98NXQS_5chNZ3D_0IaFURj9QbtF03nOXXA12v0Qe2koErfHq4uf141d847oNNEIYV9Md8U0V3CuiIHX8T7pMuylSozvnnHVCdgLSpn8',
            'status' => 'active',
        ]);

        $international = Category::query()->create([
            'name' => 'Tour Nước Ngoài',
            'slug' => 'international',
            'description' => 'Khám phá 5 châu lục với dịch vụ visa chuyên nghiệp và hành trình bay đẳng cấp.',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDPY8ey-JDL7RRRnal34hfSnBvHOn5obdmp6J8zONxaxynpehHp6KmosdcslCQFH19lsAZGNggIqTYJxaqJYvn8x9jypfnRg0drc3h567jRePyXnE-na_YSxjUCE7FaxXvl1nqsWxs-Piv4Il6CEMRLjsy6iKsmsqNBw9OTXjukdkDzYYRgaER4tcxA6D8Is2la3zesXH-rVieU1xX0UJK6YbyAJl7OBLKHLVgGF078xHCLuTsFvYOsqr6lP6DNQrB1ZZE2v0yU8lY',
            'status' => 'active',
        ]);

        Banner::query()->create([
            'placement' => 'hero',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC-uKo4b1TnGQR08qw2mowJaYxFLtQtmaW4fpCdB6Ie9nCPXOE7WdAyUR_aghbqpRzP-3fH2nAM0kV19z0_carIlvOGk7iVeHMTN21OgCaZ9XauojbNCvCTzK-4WHi3fI_VuP4q6Wamx-o6Za3eoiumTGULMDfAqKtzj8PerX2EP1AydQIoIFELmoHCUnI28grNPV4nW4reDOGqrVOwinhvfX1Im3iyonurDPQCDrh7BckiMqmHX9ovFI_Ck2uElk_XKmGn_srtC4s',
            'title' => 'Hero',
            'link' => '/',
        ]);

        $tours = [
            [
                'category_id' => $domestic->id,
                'name' => 'Hành trình Di sản miền Bắc: Hà Nội - Hạ Long - Ninh Bình',
                'slug' => 'hanh-trinh-di-san-mien-bac',
                'price' => 8990000,
                'discount_price' => 8990000,
                'duration' => 4,
                'thumbnail' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBpjgn4ea5Bh7M-TL9E7WAt_TNohXkE5gMuRoP1Ii4ZbvVgRzuIuTEdhaA8-D-X-r1XOpBumG7CQPDlXwV0zMXw-YXiwFAQHatiY53cmdAIxiGtbOChhW2l5gkFj21GVZrWHstjn5_ZJuHmFe0HzhNsT2xCTZyNZN3Hb7repa9vw5yaNIWNcZb8JqMTG_bFsHbS9bniCXV1oDwzkWKsT3CnIWkNypZ39iAAfJtvMJxVWbfLM3jQpZlnbeZiFZVyzUgsIFwjGyqvNEM',
                'rating' => 4.9,
                'badge_label' => 'Đang Hot',
                'badge_variant' => 'hot',
                'meta_icon1' => 'flight',
                'meta_text1' => 'VNA',
                'meta_icon2' => 'hotel',
                'meta_text2' => 'Khách sạn 5*',
            ],
            [
                'category_id' => $domestic->id,
                'name' => 'Đà Nẵng - Hội An - Bà Nà Hills: Cầu Vàng hùng vĩ',
                'slug' => 'da-nang-hoi-an-ba-na',
                'price' => 5250000,
                'discount_price' => 5250000,
                'duration' => 3,
                'thumbnail' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuACmB8rdjrhbDEfGIZOE-TwrwIPm1mjRfOsTaZfUlI3DawYFd3ckxQ6tIEEUPWoJxyPy3f3TNJ0Etw7Gp3e6jd3k0uqxRRODGeDKhoiDLxcJDjOBsF8pdMgYIIyW7VIFsiqfJ-_ETe1dC4i71r1aUwOHhn60vs_nQM5yeMLBFl09PVBCLr454jyePoeqn3haOLq5uw6BZ6h4oAsw4iK-Mvf-XmlpDP5XGuN-M3Dw-ktGyQmEdLIb36rXnkyxtPrqcAhrbaap4_fAdM',
                'rating' => 4.8,
                'badge_label' => 'Bán chạy',
                'badge_variant' => 'bestseller',
                'meta_icon1' => 'directions_bus',
                'meta_text1' => 'Xe cao cấp',
                'meta_icon2' => 'restaurant',
                'meta_text2' => 'Full Buffet',
            ],
            [
                'category_id' => $domestic->id,
                'name' => 'Phú Quốc: VinWonders - Safari - Cáp treo Hòn Thơm',
                'slug' => 'phu-quoc-vinwonders',
                'price' => 12490000,
                'discount_price' => 12490000,
                'duration' => 5,
                'thumbnail' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDWk6N6kiu1A-YROo55u3OBzX-gEdAJCxTigbIKPI0tIaHL81vBDQ2Dl8TZPLb0L6F6FdMPQiCVKc_3OoNlmJ2Ogh0g3V13yIGkfMTLPfzeGMdjEHltBNaxCvIOTEqctX8J03diCTSTxJ4Wj_SHLK8BAWpGWMpN9A3bzwvT1N63C6ThFQB7ONuRRmRzj7bors8EMyGt6fGNlyFqi7Opw8tt4rkepIV5I_Rc36H3i2cfy-pofCS09MAzGUALl6i9NyY-M-8-FSObJKQ',
                'rating' => 5.0,
                'badge_label' => 'Mới nhất',
                'badge_variant' => 'newest',
                'meta_icon1' => 'flight',
                'meta_text1' => 'Bay Vietjet',
                'meta_icon2' => 'pool',
                'meta_text2' => 'Resort biển',
            ],
            [
                'category_id' => $international->id,
                'name' => 'Nhật Bản: Tokyo - Kyoto - Osaka (giá khuyến mãi)',
                'slug' => 'nhat-ban-tokyo-kyoto-osaka',
                'price' => 34990000,
                'discount_price' => 26240000,
                'duration' => 6,
                'thumbnail' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAm9faVpq0yd5u4KnUIL_v2nuoS1ZRM_4hiR2BIDXhe_ZIQ0r_kjLY0f08Ywn4OOOetrJcIoo8gllWGakniNtiT0gxQPJF_QctlJdh-MNBftpkqLLMtwVDrhY4-wJdE9GaPeu_5drhDF1tdWigApI1X8YiUWRCvGRngbN_uxLzeypSY7RaxaCxnaLoPGX-imXqrL9PokKRnF93o6pKp5rXI-ByoG3fdfRYVFk9yMdy3wWZqZ_79U2g1IYi2o-GV9kX3-odKt6RylFc',
                'rating' => 4.9,
                'meta_icon1' => 'flight',
                'meta_text1' => 'Vietnam Airlines',
                'meta_icon2' => 'restaurant',
                'meta_text2' => 'Ẩm thực Nhật',
            ],
            [
                'category_id' => $international->id,
                'name' => 'Châu Âu: Pháp - Ý - Thụy Sĩ (bay thẳng)',
                'slug' => 'chau-au-phap-y-thuy-si',
                'price' => 45990000,
                'discount_price' => 45990000,
                'duration' => 7,
                'thumbnail' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx9SQX3POm0RsgFEV8Sih5xrdm4wfqBSwCSXnyKVVYa9xHm-JhvCTBzwoIT8_QEFaItJDdNKg_bJTk9ZfoMpY3BEVDyy8ioaCvr-7I8CmTyn83gVwW8sglWKBRN_9NnGlWgZddq-O9EoaJLJRQSFl-6MRI75cbBZ3rYPYWus2b9Po067n_4qhxnjjn0cyv0GFpKRbJQrcjXOOVD6OHCjsb3e5qq3Qf-BZ7H1vOJywEn6OPDz1-f4qIqima46Fx59XK0_o-U7vkpc4',
                'rating' => 4.9,
                'badge_label' => 'Hot',
                'badge_variant' => 'hot',
                'meta_icon1' => 'flight',
                'meta_text1' => 'Turkish Airlines',
                'meta_icon2' => 'hotel',
                'meta_text2' => 'Khách sạn 4*',
            ],
            [
                'category_id' => $international->id,
                'name' => 'Thái Lan: Bangkok - Pattaya',
                'slug' => 'thai-lan-bangkok-pattaya',
                'price' => 8990000,
                'discount_price' => 8990000,
                'duration' => 5,
                'thumbnail' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA_6IrbBwSkMdNuZpOtHD-DqIBaJUf_TmeCHPRMpQ-JHc9x8HvGfAGURS1bX-OgQ9u-ciLu2eVsAHqZKShZXboRgUO8Df0o9oO_YjdpMJTQwgzAzs02hQHuAXQaURnDUXeiZlOvvdTgt2wtstyKYlITwvKbjq_sXkwEGTLxQIvjVOxvl5bm3XgQuBNMuYLt8G-s8jUHD4qjn38L_SUW09j2JAWFPt2J7mPs5_KGg9rnJknOtR_SKpVTuft23x9VfI7fNFaIQ2qQVXY',
                'rating' => 5.0,
                'badge_label' => 'Mới',
                'badge_variant' => 'newest',
                'meta_icon1' => 'flight',
                'meta_text1' => 'Thai Airways',
                'meta_icon2' => 'pool',
                'meta_text2' => 'Resort 5*',
            ],
        ];

        foreach ($tours as $row) {
            Tour::query()->create(array_merge($row, [
                'status' => 'active',
                'description' => 'Tour '.$row['name'].' — chương trình chi tiết sẽ được cập nhật.',
                'start_location' => 'TP.HCM',
                'max_people' => random_int(2, 20),
            ]));
        }

        // Thêm vài tour trong nước để danh sách đủ dài
        $extra = [
            ['Miền Tây Sông Nước: Mỹ Tho - Bến Tre - Cần Thơ', 'mien-tay-song-nuoc', 2190000, 2],
            ['Nha Trang: Vinpearl Land', 'nha-trang-vinpearl', 3590000, 3],
            ['Khám phá Cao Nguyên Đá: Hà Giang', 'ha-giang-dong-van', 6750000, 4],
        ];
        foreach ($extra as [$title, $slug, $price, $days]) {
            Tour::query()->create([
                'category_id' => $domestic->id,
                'name' => $title,
                'slug' => $slug,
                'description' => 'Tour '.$title,
                'price' => $price,
                'discount_price' => $price,
                'duration' => $days,
                'thumbnail' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDJDQBKzgP2UdYtHvgSLWRUFRfJiCyT_5b2te2Y4W24bl0thR-I7sAfzqaKMTON7GeZksKK0rB8bVzY42r0EAW9mV7tL01xcQkGJHG3SeRejZlJTVRyjNuXfQ41HONtrX6RLsVFdMiqJlIRvrRLXk6MIO3xo2ocl4f6WuldVU-fln9PNYiXiSr5yZBxqjxVzSrzPW3JSHdllGQWJKmWrrcNiMvFzjF3ZhuNQi7TM2rEeDqS_qoXQXmjlezggIE68LoQpwBsmloXAQE',
                'status' => 'active',
                'rating' => 4.7,
                'start_location' => 'Hà Nội',
                'max_people' => 16,
                'meta_icon1' => 'directions_boat',
                'meta_text1' => 'Thuyền',
                'meta_icon2' => 'eco',
                'meta_text2' => 'Thiên nhiên',
            ]);
        }

        if (Schema::hasColumn('tours', 'departure_date')) {
            foreach (Tour::query()->orderBy('id')->get() as $i => $tour) {
                $tour->departure_date = now()->addDays(7 + ($i % 55));
                $tour->save();
            }
        }

        Coupon::query()->updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'title' => 'Chào mừng thành viên mới',
                'scope' => 'domestic',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_value' => 1000000,
                'max_discount' => 500000,
                'quantity' => 200,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(60),
                'status' => 'active',
            ]
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'WORLD20'],
            [
                'title' => 'Tour quốc tế cuối năm',
                'scope' => 'international',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'min_order_value' => 15000000,
                'max_discount' => 3000000,
                'quantity' => 80,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(90),
                'status' => 'active',
            ]
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'SACOM1TR'],
            [
                'title' => 'Đồng hành Sacombank',
                'scope' => 'bank',
                'discount_type' => 'fixed',
                'discount_value' => 1000000,
                'min_order_value' => 12000000,
                'max_discount' => null,
                'quantity' => 50,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(45),
                'status' => 'active',
            ]
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'VIN200'],
            [
                'title' => 'Combo nghỉ dưỡng',
                'scope' => 'hotel',
                'discount_type' => 'fixed',
                'discount_value' => 200000,
                'min_order_value' => 2000000,
                'max_discount' => null,
                'quantity' => 120,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(120),
                'status' => 'active',
            ]
        );

        Coupon::query()->updateOrCreate(
            ['code' => 'FLASH5'],
            [
                'title' => 'Flash sale hàng tuần',
                'scope' => 'all',
                'discount_type' => 'percent',
                'discount_value' => 5,
                'min_order_value' => 3000000,
                'max_discount' => 400000,
                'quantity' => null,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(365),
                'status' => 'active',
            ]
        );

        $firstTour = Tour::query()->first();
        if ($firstTour) {
            Review::query()->create([
                'user_id' => $admin->id,
                'tour_id' => $firstTour->id,
                'rating' => 5,
                'comment' => 'Dịch vụ tuyệt vời, lịch trình hợp lý.',
                'status' => 'approved',
                'created_at' => now(),
            ]);
        }

        $tourIds = Tour::query()->pluck('id')->all();
        if (count($tourIds) > 0) {
            $statusCycle = ['confirmed', 'pending', 'paid', 'cancelled'];
            $names = ['Nguyễn Anh Tuấn', 'Lê Thị Mai', 'Phạm Minh Đức', 'Trần Văn Hoàng', 'Hoàng Thu Hà'];
            for ($i = 0; $i < 28; $i++) {
                $tourId = $tourIds[array_rand($tourIds)];
                $daysAgo = random_int(0, 120);
                $st = $statusCycle[$i % count($statusCycle)];
                $isPaid = in_array($st, ['confirmed', 'paid'], true);
                $booking = Booking::query()->create([
                    'user_id' => $admin->id,
                    'tour_id' => $tourId,
                    'booking_code' => 'BK'.str_pad((string) (10000 + $i), 5, '0', STR_PAD_LEFT),
                    'total_price' => random_int(5, 120) * 100_000,
                    'status' => $st,
                    'payment_status' => $isPaid ? 'paid' : 'pending',
                    'booking_date' => now()->subDays($daysAgo),
                    'travel_date' => now()->addDays(random_int(5, 90)),
                    'number_of_people' => random_int(1, 5),
                    'created_at' => now()->subDays($daysAgo),
                ]);
                BookingDetail::query()->create([
                    'booking_id' => $booking->id,
                    'customer_name' => $names[$i % count($names)],
                    'customer_phone' => '09'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'customer_email' => 'khach'.$i.'@demo.ftravel.test',
                ]);
            }
        }
    }
}
