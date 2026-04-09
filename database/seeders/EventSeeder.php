<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Ticket::query()->delete();
        Event::query()->delete();

        $categories = EventCategory::all()->keyBy('name');
        $organizers = User::where('role', 'organizer')->get()->keyBy('email');

        if ($categories->isEmpty() || $organizers->isEmpty()) {
            return;
        }

        // Public-event-inspired concert data for a richer demo catalog.
        $events = [
            [
                'organizer_email' => 'organizer@example.com',
                'category' => 'Music Concert',
                'title' => 'RIIZE - 2026 RIIZE Concert Tour',
                'banner_path' => 'https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Konser RIIZE di Indonesia sebagai bagian dari tur 2026 RIIZING LOUD dengan panggung skala arena dan penampilan lagu-lagu andalan mereka.',
                'start_time' => Carbon::create(2026, 1, 10, 19, 0),
                'end_time' => Carbon::create(2026, 1, 10, 22, 0),
                'location_name' => 'ICE BSD Hall 5-6',
                'address' => 'ICE BSD City, Tangerang, Banten',
                'latitude' => -6.30131600,
                'longitude' => 106.65284700,
                'tickets' => [
                    ['name' => 'CAT 2', 'price' => 950000, 'quota' => 400],
                    ['name' => 'VIP', 'price' => 1750000, 'quota' => 120],
                ],
            ],
            [
                'organizer_email' => 'sound.rhythm@example.com',
                'category' => 'Music Concert',
                'title' => 'ATEEZ - ATEEZ 2026 World Tour (In Your Fantasy) In Asia',
                'banner_path' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1600&q=80',
                'description' => 'ATEEZ membawa tur dunia mereka ke Indonesia dengan panggung besar, visual intens, dan setlist penuh anthem favorit penggemar.',
                'start_time' => Carbon::create(2026, 1, 31, 19, 0),
                'end_time' => Carbon::create(2026, 1, 31, 22, 0),
                'location_name' => 'ICE BSD Hall 5-6',
                'address' => 'ICE BSD City, Tangerang, Banten',
                'latitude' => -6.30131600,
                'longitude' => 106.65284700,
                'tickets' => [
                    ['name' => 'CAT 3', 'price' => 900000, 'quota' => 450],
                    ['name' => 'VIP', 'price' => 1800000, 'quota' => 100],
                ],
            ],
            [
                'organizer_email' => 'organizer@example.com',
                'category' => 'Music Concert',
                'title' => 'Padi Reborn - Dua Delapan Padi Reborn (360 Stage)',
                'banner_path' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Padi Reborn hadir dengan konser spesial panggung 360 derajat yang membawa nuansa nostalgia dan produksi visual megah.',
                'start_time' => Carbon::create(2026, 1, 31, 20, 0),
                'end_time' => Carbon::create(2026, 1, 31, 22, 30),
                'location_name' => 'Tennis Indoor Senayan',
                'address' => 'Jl. Pintu Satu Senayan, Jakarta',
                'latitude' => -6.22270300,
                'longitude' => 106.80286600,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 350000, 'quota' => 500],
                    ['name' => 'Premium', 'price' => 650000, 'quota' => 180],
                ],
            ],
            [
                'organizer_email' => 'colorasia.live@example.com',
                'category' => 'Music Concert',
                'title' => "The 90's Intimate 2nd Edition",
                'banner_path' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Konser nostalgia internasional menghadirkan Michael Learns To Rock, Jim Brickman, Peabo Bryson, dan special featuring Vina Panduwinata serta Rita Effendy.',
                'start_time' => Carbon::create(2026, 2, 7, 19, 0),
                'end_time' => Carbon::create(2026, 2, 7, 23, 0),
                'location_name' => 'Istora Senayan',
                'address' => 'Kompleks Gelora Bung Karno, Jakarta',
                'latitude' => -6.22204400,
                'longitude' => 106.80315100,
                'tickets' => [
                    ['name' => 'CAT 2', 'price' => 650000, 'quota' => 350],
                    ['name' => 'VIP', 'price' => 1500000, 'quota' => 120],
                ],
            ],
            [
                'organizer_email' => 'organizer@example.com',
                'category' => 'Music Concert',
                'title' => 'Dream Theater - 40th Anniversary Tour 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1498038432885-c6f3f1b912ee?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Dream Theater merayakan 40 tahun perjalanan musik lewat konser anniversary tour di Jakarta.',
                'start_time' => Carbon::create(2026, 2, 7, 20, 0),
                'end_time' => Carbon::create(2026, 2, 7, 23, 0),
                'location_name' => 'Stadion Madya GBK',
                'address' => 'Kompleks Gelora Bung Karno, Jakarta',
                'latitude' => -6.21895700,
                'longitude' => 106.80286500,
                'tickets' => [
                    ['name' => 'Festival', 'price' => 900000, 'quota' => 700],
                    ['name' => 'VIP', 'price' => 1800000, 'quota' => 150],
                ],
            ],
            [
                'organizer_email' => 'colorasia.live@example.com',
                'category' => 'Music Concert',
                'title' => 'Westlife - A Gala Evening',
                'banner_path' => 'https://images.unsplash.com/photo-1501612780327-45045538702b?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Westlife kembali ke Indonesia dengan konsep konser orkestra berskala besar bersama Magenta Orchestra.',
                'start_time' => Carbon::create(2026, 2, 10, 19, 0),
                'end_time' => Carbon::create(2026, 2, 10, 22, 30),
                'location_name' => 'NICE PIK 2',
                'address' => 'Nusantara International Convention Exhibition, PIK 2, Jakarta',
                'latitude' => -6.10451200,
                'longitude' => 106.74024900,
                'tickets' => [
                    ['name' => 'Silver', 'price' => 1100000, 'quota' => 500],
                    ['name' => 'Diamond', 'price' => 2500000, 'quota' => 120],
                ],
            ],
            [
                'organizer_email' => 'nusantara.live@example.com',
                'category' => 'Music Concert',
                'title' => 'Josh Groban - GEMS World Tour 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Josh Groban membawa GEMS World Tour ke Jakarta dengan Raisa sebagai special guest untuk penampilan istimewa.',
                'start_time' => Carbon::create(2026, 2, 15, 19, 0),
                'end_time' => Carbon::create(2026, 2, 15, 22, 0),
                'location_name' => 'The Ritz-Carlton Jakarta, Pacific Place Ballroom',
                'address' => 'Pacific Place, SCBD, Jakarta',
                'latitude' => -6.22408900,
                'longitude' => 106.80932200,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 1250000, 'quota' => 250],
                    ['name' => 'Premium', 'price' => 2750000, 'quota' => 80],
                ],
            ],
            [
                'organizer_email' => 'harmony.stage@example.com',
                'category' => 'Music Concert',
                'title' => 'Anime Symphony 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Jakarta Concert Orchestra kembali dengan konser anime symphony yang membawakan lagu-lagu populer Jepang dalam aransemen orkestra.',
                'start_time' => Carbon::create(2026, 1, 25, 15, 30),
                'end_time' => Carbon::create(2026, 1, 25, 21, 0),
                'location_name' => 'Graha Bhakti Budaya, TIM',
                'address' => 'Taman Ismail Marzuki, Jl. Cikini Raya No.73, Jakarta',
                'latitude' => -6.19040200,
                'longitude' => 106.83973800,
                'tickets' => [
                    ['name' => 'Topaz', 'price' => 535000, 'quota' => 180],
                    ['name' => 'Berlian', 'price' => 1535000, 'quota' => 50],
                ],
            ],
            [
                'organizer_email' => 'sound.rhythm@example.com',
                'category' => 'Music Concert',
                'title' => 'aespa - SYNK: aeXIS LINE in Jakarta',
                'banner_path' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1600&q=80',
                'description' => 'aespa menyapa MY Indonesia lewat konser tur aeXIS LINE dengan produksi visual futuristik dan panggung energi tinggi.',
                'start_time' => Carbon::create(2026, 4, 4, 19, 0),
                'end_time' => Carbon::create(2026, 4, 4, 22, 0),
                'location_name' => 'ICE BSD Hall 5-6',
                'address' => 'ICE BSD City, Tangerang, Banten',
                'latitude' => -6.30131600,
                'longitude' => 106.65284700,
                'tickets' => [
                    ['name' => 'CAT 3', 'price' => 1000000, 'quota' => 420],
                    ['name' => 'VIP', 'price' => 2100000, 'quota' => 100],
                ],
            ],
            [
                'organizer_email' => 'sound.rhythm@example.com',
                'category' => 'Music Concert',
                'title' => 'NCT WISH - INTO THE WISH: Our WISH',
                'banner_path' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1600&q=80',
                'description' => 'NCT WISH hadir di Indonesia untuk konser solo pertama mereka dengan konsep youthful dan panggung penuh koreografi.',
                'start_time' => Carbon::create(2026, 4, 11, 19, 0),
                'end_time' => Carbon::create(2026, 4, 11, 22, 0),
                'location_name' => 'ICE BSD Hall 5',
                'address' => 'ICE BSD City, Tangerang, Banten',
                'latitude' => -6.30131600,
                'longitude' => 106.65284700,
                'tickets' => [
                    ['name' => 'CAT 2', 'price' => 850000, 'quota' => 450],
                    ['name' => 'VIP', 'price' => 1650000, 'quota' => 90],
                ],
            ],
            [
                'organizer_email' => 'organizer@example.com',
                'category' => 'Music Concert',
                'title' => 'Deep Purple & Slank - All Greatest Hits Live in Jakarta',
                'banner_path' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Kolaborasi lintas generasi yang menyatukan legenda rock dunia Deep Purple dengan ikon musik Indonesia, Slank.',
                'start_time' => Carbon::create(2026, 4, 18, 20, 0),
                'end_time' => Carbon::create(2026, 4, 18, 23, 0),
                'location_name' => 'Indonesia Arena, GBK Senayan',
                'address' => 'Kompleks Gelora Bung Karno, Jakarta',
                'latitude' => -6.21876400,
                'longitude' => 106.80244100,
                'tickets' => [
                    ['name' => 'Festival', 'price' => 750000, 'quota' => 600],
                    ['name' => 'VIP', 'price' => 1750000, 'quota' => 140],
                ],
            ],
            [
                'organizer_email' => 'sound.rhythm@example.com',
                'category' => 'Music Concert',
                'title' => 'TREASURE - 2025-26 TREASURE TOUR: PULSE ON',
                'banner_path' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1600&q=80',
                'description' => 'TREASURE menggelar konser dua hari di Indonesia Arena dalam rangkaian tur PULSE ON yang dinanti penggemarnya.',
                'start_time' => Carbon::create(2026, 4, 25, 18, 30),
                'end_time' => Carbon::create(2026, 4, 26, 22, 0),
                'location_name' => 'Indonesia Arena, GBK Senayan',
                'address' => 'Kompleks Gelora Bung Karno, Jakarta',
                'latitude' => -6.21876400,
                'longitude' => 106.80244100,
                'tickets' => [
                    ['name' => 'CAT 2', 'price' => 1200000, 'quota' => 600],
                    ['name' => 'VIP', 'price' => 2500000, 'quota' => 140],
                ],
            ],
            [
                'organizer_email' => 'arena.sports@example.com',
                'category' => 'Music Concert',
                'title' => 'Hammersonic 2026: Decade of Dominion',
                'banner_path' => 'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Festival metal dan rock besar dengan panggung multi-lineup dalam perayaan satu dekade Hammersonic.',
                'start_time' => Carbon::create(2026, 5, 2, 14, 0),
                'end_time' => Carbon::create(2026, 5, 3, 23, 0),
                'location_name' => 'NICE PIK 2',
                'address' => 'NICE PIK 2, Tangerang',
                'latitude' => -6.10451200,
                'longitude' => 106.74024900,
                'tickets' => [
                    ['name' => '2 Day Pass', 'price' => 850000, 'quota' => 800],
                    ['name' => 'VIP 2 Day Pass', 'price' => 1900000, 'quota' => 200],
                ],
            ],
            [
                'organizer_email' => 'nusantara.live@example.com',
                'category' => 'Music Concert',
                'title' => 'BESOK KONSER 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Festival musik Indonesia dengan lineup awal HIVI! dan Perunggu, serta kejutan nama lain yang diumumkan bertahap.',
                'start_time' => Carbon::create(2026, 5, 2, 15, 0),
                'end_time' => Carbon::create(2026, 5, 3, 23, 0),
                'location_name' => 'Lapangan Tembak Yonif 201',
                'address' => 'Jakarta Timur, DKI Jakarta',
                'latitude' => -6.23277800,
                'longitude' => 106.90247200,
                'tickets' => [
                    ['name' => 'Day Pass', 'price' => 180000, 'quota' => 500],
                    ['name' => '2 Day Pass', 'price' => 320000, 'quota' => 300],
                ],
            ],
            [
                'organizer_email' => 'harmony.stage@example.com',
                'category' => 'Music Concert',
                'title' => 'Eric Chou - Odyssey Stars',
                'banner_path' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Eric Chou menyapa penggemar Indonesia lewat konser solo berskala arena dengan lagu-lagu pop romantis andalannya.',
                'start_time' => Carbon::create(2026, 5, 9, 19, 30),
                'end_time' => Carbon::create(2026, 5, 9, 22, 0),
                'location_name' => 'Indonesia Arena',
                'address' => 'Kompleks Gelora Bung Karno, Jakarta',
                'latitude' => -6.21876400,
                'longitude' => 106.80244100,
                'tickets' => [
                    ['name' => 'CAT 2', 'price' => 950000, 'quota' => 420],
                    ['name' => 'VIP', 'price' => 2100000, 'quota' => 90],
                ],
            ],
            [
                'organizer_email' => 'organizer@example.com',
                'category' => 'Music Concert',
                'title' => 'One Ok Rock - DETOX Asia Tour 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1508252592163-5d3c3c559e9c?auto=format&fit=crop&w=1600&q=80',
                'description' => 'One Ok Rock membawa DETOX Asia Tour 2026 ke Jakarta dengan panggung rock stadium penuh energi.',
                'start_time' => Carbon::create(2026, 5, 16, 20, 0),
                'end_time' => Carbon::create(2026, 5, 16, 23, 0),
                'location_name' => 'Beach City International Stadium',
                'address' => 'Ancol Beach City, Jakarta Utara',
                'latitude' => -6.11836100,
                'longitude' => 106.85377800,
                'tickets' => [
                    ['name' => 'Festival', 'price' => 850000, 'quota' => 600],
                    ['name' => 'VIP Standing', 'price' => 1750000, 'quota' => 140],
                ],
            ],
            [
                'organizer_email' => 'harmony.stage@example.com',
                'category' => 'Music Concert',
                'title' => 'Java Jazz Festival 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Festival jazz tahunan berskala internasional dengan line-up lintas genre dan berbagai panggung sepanjang akhir pekan.',
                'start_time' => Carbon::create(2026, 5, 29, 15, 0),
                'end_time' => Carbon::create(2026, 5, 31, 23, 0),
                'location_name' => 'NICE PIK 2',
                'address' => 'NICE PIK 2, Tangerang',
                'latitude' => -6.10451200,
                'longitude' => 106.74024900,
                'tickets' => [
                    ['name' => 'Daily Pass', 'price' => 375000, 'quota' => 900],
                    ['name' => '3 Day Pass', 'price' => 950000, 'quota' => 500],
                ],
            ],
            [
                'organizer_email' => 'colorasia.live@example.com',
                'category' => 'Music Concert',
                'title' => "Disney's The Lion King Live in Concert",
                'banner_path' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Pertunjukan musikal live yang menghadirkan pengalaman konser dan teater keluarga dengan aransemen megah.',
                'start_time' => Carbon::create(2026, 6, 13, 15, 0),
                'end_time' => Carbon::create(2026, 6, 14, 21, 0),
                'location_name' => 'Ciputra Artpreneur Theater',
                'address' => 'Lotte Shopping Avenue, Jakarta',
                'latitude' => -6.22419500,
                'longitude' => 106.82283900,
                'tickets' => [
                    ['name' => 'Regular', 'price' => 450000, 'quota' => 300],
                    ['name' => 'VIP', 'price' => 1200000, 'quota' => 80],
                ],
            ],
            [
                'organizer_email' => 'harmony.stage@example.com',
                'category' => 'Music Concert',
                'title' => 'Prambanan Jazz 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Prambanan Jazz kembali digelar di pelataran Candi Prambanan dengan Michael Learns To Rock sebagai salah satu headliner.',
                'start_time' => Carbon::create(2026, 7, 3, 16, 0),
                'end_time' => Carbon::create(2026, 7, 5, 23, 0),
                'location_name' => 'Candi Prambanan',
                'address' => 'Jl. Raya Solo - Yogyakarta, Sleman',
                'latitude' => -7.75202100,
                'longitude' => 110.49146700,
                'tickets' => [
                    ['name' => 'Festival Pass', 'price' => 450000, 'quota' => 700],
                    ['name' => 'VIP Pass', 'price' => 1250000, 'quota' => 120],
                ],
            ],
            [
                'organizer_email' => 'organizer@example.com',
                'category' => 'Music Concert',
                'title' => 'My Chemical Romance Live in Jakarta',
                'banner_path' => 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1600&q=80',
                'description' => 'My Chemical Romance hadir di Jakarta lewat konser tunggal berskala stadion dengan katalog lagu emosional dan teatrikal mereka.',
                'start_time' => Carbon::create(2026, 11, 22, 19, 0),
                'end_time' => Carbon::create(2026, 11, 22, 23, 0),
                'location_name' => 'Jakarta International Stadium',
                'address' => 'Papanggo, Tanjung Priok, Jakarta Utara',
                'latitude' => -6.12555600,
                'longitude' => 106.88250000,
                'tickets' => [
                    ['name' => 'Festival', 'price' => 1250000, 'quota' => 900],
                    ['name' => 'VIP', 'price' => 2600000, 'quota' => 180],
                ],
            ],
            [
                'organizer_email' => 'edu.summit@example.com',
                'category' => 'Tech Conference',
                'title' => 'Google I/O Extended Jakarta 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Konferensi teknologi komunitas yang membahas AI, Android, cloud, dan web modern lewat keynote, panel, dan networking session.',
                'start_time' => Carbon::create(2026, 6, 20, 9, 0),
                'end_time' => Carbon::create(2026, 6, 20, 18, 0),
                'location_name' => 'Balai Kartini',
                'address' => 'Jl. Gatot Subroto Kav. 37, Jakarta Selatan',
                'latitude' => -6.23864200,
                'longitude' => 106.83018000,
                'tickets' => [
                    ['name' => 'Conference Pass', 'price' => 250000, 'quota' => 450],
                    ['name' => 'VIP Networking', 'price' => 650000, 'quota' => 80],
                ],
            ],
            [
                'organizer_email' => 'creative.hub@example.com',
                'category' => 'Workshop',
                'title' => 'UI/UX Design Bootcamp by Creative Hub',
                'banner_path' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Workshop intensif untuk mempelajari design thinking, wireframing, prototyping, dan evaluasi usability bersama mentor industri.',
                'start_time' => Carbon::create(2026, 7, 11, 10, 0),
                'end_time' => Carbon::create(2026, 7, 11, 16, 30),
                'location_name' => 'Pos Bloc Jakarta',
                'address' => 'Jl. Pos No.2, Pasar Baru, Jakarta Pusat',
                'latitude' => -6.16108600,
                'longitude' => 106.83697600,
                'tickets' => [
                    ['name' => 'Workshop Seat', 'price' => 150000, 'quota' => 120],
                    ['name' => 'Workshop + Mentoring', 'price' => 300000, 'quota' => 40],
                ],
            ],
            [
                'organizer_email' => 'arena.sports@example.com',
                'category' => 'Sports',
                'title' => 'Jakarta Night Run 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1486218119243-13883505764c?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Lari malam 10K di pusat kota Jakarta dengan entertainment zone, hydration points, dan finisher medal eksklusif.',
                'start_time' => Carbon::create(2026, 8, 8, 19, 0),
                'end_time' => Carbon::create(2026, 8, 8, 23, 0),
                'location_name' => 'Plaza Barat GBK',
                'address' => 'Gelora Bung Karno, Senayan, Jakarta',
                'latitude' => -6.21851300,
                'longitude' => 106.80175400,
                'tickets' => [
                    ['name' => '5K Pass', 'price' => 175000, 'quota' => 700],
                    ['name' => '10K Pass', 'price' => 250000, 'quota' => 500],
                ],
            ],
            [
                'organizer_email' => 'harmony.stage@example.com',
                'category' => 'Art Exhibition',
                'title' => 'Jakarta Contemporary Art Week 2026',
                'banner_path' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&w=1600&q=80',
                'description' => 'Pameran seni kontemporer yang menghadirkan instalasi imersif, karya visual seniman muda, dan sesi artist talk sepanjang pekan.',
                'start_time' => Carbon::create(2026, 9, 3, 10, 0),
                'end_time' => Carbon::create(2026, 9, 7, 21, 0),
                'location_name' => 'Galeri Nasional Indonesia',
                'address' => 'Jl. Medan Merdeka Timur No.14, Jakarta Pusat',
                'latitude' => -6.17832900,
                'longitude' => 106.83263900,
                'tickets' => [
                    ['name' => 'Daily Pass', 'price' => 50000, 'quota' => 600],
                    ['name' => 'Week Pass', 'price' => 180000, 'quota' => 180],
                ],
            ],
        ];

        foreach ($events as $eventData) {
            $organizer = $organizers->get($eventData['organizer_email']);
            $category = $categories->get($eventData['category']);

            if (! $organizer || ! $category) {
                continue;
            }

            $event = Event::create([
                'organizer_id' => $organizer->id,
                'category_id' => $category->id,
                'title' => $eventData['title'],
                'slug' => Str::slug($eventData['title']) . '-' . Str::lower(Str::random(5)),
                'banner_path' => $eventData['banner_path'],
                'description' => $eventData['description'],
                'start_time' => $eventData['start_time'],
                'end_time' => $eventData['end_time'],
                'location_name' => $eventData['location_name'],
                'address' => $eventData['address'],
                'latitude' => $eventData['latitude'],
                'longitude' => $eventData['longitude'],
                'status' => 'published',
            ]);

            foreach ($eventData['tickets'] as $ticketData) {
                Ticket::create([
                    'event_id' => $event->id,
                    'name' => $ticketData['name'],
                    'price' => $ticketData['price'],
                    'quota' => $ticketData['quota'],
                    'available_qty' => $ticketData['quota'],
                ]);
            }
        }
    }
}
