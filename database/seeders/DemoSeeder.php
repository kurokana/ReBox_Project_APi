<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Box;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============= CREATE USERS WITH ROLES =============
        
        // Admin User
        $adminUser = User::create([
            'name' => 'Admin ReBox',
            'email' => 'admin@rebox.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'bio' => 'Administrator ReBox System',
        ]);

        // Pengepul Users
        $pengepul1 = User::create([
            'name' => 'Pengepul Jaya',
            'email' => 'pengepul@rebox.com',
            'password' => Hash::make('password123'),
            'role' => 'pengepul',
            'phone' => '082345678901',
            'address' => 'Jl. Daur Ulang No. 123, Jakarta',
            'bio' => 'Pengepul sampah profesional sejak 2015',
            'balance' => 0,
        ]);

        $pengepul2 = User::create([
            'name' => 'Budi Collector',
            'email' => 'budi@pengepul.com',
            'password' => Hash::make('password123'),
            'role' => 'pengepul',
            'phone' => '083456789012',
            'address' => 'Jl. Hijau Daun No. 45, Bandung',
            'bio' => 'Spesialisasi elektronik dan plastik',
            'balance' => 150000,
        ]);

        // Pengguna (Regular Users)
        $demoUser = User::create([
            'name' => 'Demo User',
            'email' => 'demo@rebox.com',
            'password' => Hash::make('password123'),
            'role' => 'pengguna',
            'phone' => '084567890123',
            'address' => 'Jl. Contoh No. 1, Jakarta Selatan',
        ]);

        $johnUser = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'pengguna',
            'phone' => '085678901234',
            'address' => 'Jl. Mawar No. 10, Surabaya',
        ]);

        $janeUser = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'role' => 'pengguna',
            'phone' => '086789012345',
            'address' => 'Jl. Melati No. 7, Yogyakarta',
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Peralatan elektronik dan gadget'],
            ['name' => 'Pakaian', 'description' => 'Pakaian dan aksesori'],
            ['name' => 'Buku', 'description' => 'Buku dan majalah'],
            ['name' => 'Dapur', 'description' => 'Peralatan dapur dan masak'],
            ['name' => 'Mainan', 'description' => 'Mainan anak dan koleksi'],
            ['name' => 'Alat Tulis', 'description' => 'Alat tulis kantor dan sekolah'],
            ['name' => 'Olahraga', 'description' => 'Peralatan olahraga'],
            ['name' => 'Dekorasi', 'description' => 'Dekorasi rumah'],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[] = Category::create($category);
        }

        // Create Boxes for Demo User
        $box1 = Box::create([
            'user_id' => $demoUser->id,
            'name' => 'Box Kamar Tidur',
            'description' => 'Box untuk barang-barang kamar tidur',
        ]);

        $box2 = Box::create([
            'user_id' => $demoUser->id,
            'name' => 'Box Dapur',
            'description' => 'Peralatan dapur yang jarang dipakai',
        ]);

        $box3 = Box::create([
            'user_id' => $demoUser->id,
            'name' => 'Box Ruang Kerja',
            'description' => 'Peralatan kantor dan alat tulis',
        ]);

        $box4 = Box::create([
            'user_id' => $demoUser->id,
            'name' => 'Box Garasi',
            'description' => 'Peralatan olahraga dan outdoor',
        ]);

        $box5 = Box::create([
            'user_id' => $demoUser->id,
            'name' => 'Box Anak',
            'description' => 'Mainan dan barang anak-anak',
        ]);

        // Create Boxes for John User
        $box6 = Box::create([
            'user_id' => $johnUser->id,
            'name' => 'Box Koleksi',
            'description' => 'Koleksi buku dan elektronik',
        ]);

        $box7 = Box::create([
            'user_id' => $johnUser->id,
            'name' => 'Box Gudang',
            'description' => 'Penyimpanan jangka panjang',
        ]);

        // Create Items for Box 1 (Kamar Tidur)
        Item::create([
            'box_id' => $box1->id,
            'category_id' => $createdCategories[1]->id, // Pakaian
            'name' => 'Jaket Musim Dingin',
            'description' => 'Jaket tebal warna hitam',
            'quantity' => 2,
        ]);

        Item::create([
            'box_id' => $box1->id,
            'category_id' => $createdCategories[1]->id, // Pakaian
            'name' => 'Selimut Tambahan',
            'description' => 'Selimut fleece biru',
            'quantity' => 3,
        ]);

        Item::create([
            'box_id' => $box1->id,
            'category_id' => $createdCategories[7]->id, // Dekorasi
            'name' => 'Bantal Sofa',
            'description' => 'Bantal dekoratif warna-warni',
            'quantity' => 5,
        ]);

        // Create Items for Box 2 (Dapur)
        Item::create([
            'box_id' => $box2->id,
            'category_id' => $createdCategories[3]->id, // Dapur
            'name' => 'Mixer Listrik',
            'description' => 'Mixer Philips 3 kecepatan',
            'quantity' => 1,
        ]);

        Item::create([
            'box_id' => $box2->id,
            'category_id' => $createdCategories[3]->id, // Dapur
            'name' => 'Set Cetakan Kue',
            'description' => 'Cetakan kue berbagai bentuk',
            'quantity' => 12,
        ]);

        Item::create([
            'box_id' => $box2->id,
            'category_id' => $createdCategories[3]->id, // Dapur
            'name' => 'Piring Makan Set',
            'description' => 'Piring keramik putih 6 pcs',
            'quantity' => 6,
        ]);

        // Create Items for Box 3 (Ruang Kerja)
        Item::create([
            'box_id' => $box3->id,
            'category_id' => $createdCategories[5]->id, // Alat Tulis
            'name' => 'Printer HP LaserJet',
            'description' => 'Printer HP hitam putih',
            'quantity' => 1,
        ]);

        Item::create([
            'box_id' => $box3->id,
            'category_id' => $createdCategories[5]->id, // Alat Tulis
            'name' => 'Ream Kertas A4',
            'description' => 'Kertas HVS 80gsm',
            'quantity' => 3,
        ]);

        Item::create([
            'box_id' => $box3->id,
            'category_id' => $createdCategories[0]->id, // Elektronik
            'name' => 'Mouse Wireless',
            'description' => 'Logitech M185',
            'quantity' => 2,
        ]);

        Item::create([
            'box_id' => $box3->id,
            'category_id' => $createdCategories[5]->id, // Alat Tulis
            'name' => 'Stapler dan Isi',
            'description' => 'Stapler besar + 2 box isi',
            'quantity' => 1,
        ]);

        // Create Items for Box 4 (Garasi)
        Item::create([
            'box_id' => $box4->id,
            'category_id' => $createdCategories[6]->id, // Olahraga
            'name' => 'Raket Badminton',
            'description' => 'Raket Yonex + cover',
            'quantity' => 2,
        ]);

        Item::create([
            'box_id' => $box4->id,
            'category_id' => $createdCategories[6]->id, // Olahraga
            'name' => 'Bola Basket',
            'description' => 'Bola basket Spalding',
            'quantity' => 1,
        ]);

        Item::create([
            'box_id' => $box4->id,
            'category_id' => $createdCategories[6]->id, // Olahraga
            'name' => 'Matras Yoga',
            'description' => 'Matras yoga ungu dengan tas',
            'quantity' => 2,
        ]);

        // Create Items for Box 5 (Anak)
        Item::create([
            'box_id' => $box5->id,
            'category_id' => $createdCategories[4]->id, // Mainan
            'name' => 'Lego Classic',
            'description' => 'Set Lego Classic 500 pcs',
            'quantity' => 1,
        ]);

        Item::create([
            'box_id' => $box5->id,
            'category_id' => $createdCategories[4]->id, // Mainan
            'name' => 'Boneka Teddy Bear',
            'description' => 'Boneka beruang coklat besar',
            'quantity' => 3,
        ]);

        Item::create([
            'box_id' => $box5->id,
            'category_id' => $createdCategories[2]->id, // Buku
            'name' => 'Buku Cerita Anak',
            'description' => 'Koleksi buku dongeng',
            'quantity' => 15,
        ]);

        // Create Items for Box 6 (Koleksi John)
        Item::create([
            'box_id' => $box6->id,
            'category_id' => $createdCategories[0]->id, // Elektronik
            'name' => 'Headphone Sony',
            'description' => 'Headphone noise cancelling',
            'quantity' => 1,
        ]);

        Item::create([
            'box_id' => $box6->id,
            'category_id' => $createdCategories[2]->id, // Buku
            'name' => 'Novel Klasik',
            'description' => 'Koleksi novel klasik dunia',
            'quantity' => 20,
        ]);

        // Create Items for Box 7 (Gudang John)
        Item::create([
            'box_id' => $box7->id,
            'category_id' => $createdCategories[0]->id, // Elektronik
            'name' => 'Kabel Charger',
            'description' => 'Berbagai macam kabel charger',
            'quantity' => 10,
        ]);

        Item::create([
            'box_id' => $box7->id,
            'category_id' => $createdCategories[7]->id, // Dekorasi
            'name' => 'Lampu Hias LED',
            'description' => 'Lampu LED strip warna warni',
            'quantity' => 5,
        ]);

        // ============= CREATE TRANSACTIONS =============
        
        // Transaction 1: Pending (waiting for demoUser to accept)
        $transaction1 = Transaction::create([
            'box_id' => $box1->id,
            'pengguna_id' => $demoUser->id,
            'pengepul_id' => $pengepul1->id,
            'status' => 'pending',
            'total_price' => 75000,
            'admin_fee' => 7500,
            'pengepul_earnings' => 67500,
            'notes' => 'Penawaran untuk box kamar tidur',
        ]);

        // Transaction 2: Accepted (pengepul can complete)
        $transaction2 = Transaction::create([
            'box_id' => $box2->id,
            'pengguna_id' => $demoUser->id,
            'pengepul_id' => $pengepul1->id,
            'status' => 'accepted',
            'total_price' => 120000,
            'admin_fee' => 12000,
            'pengepul_earnings' => 108000,
            'notes' => 'Peralatan dapur layak pakai',
            'accepted_at' => now()->subDays(1),
        ]);

        // Transaction 3: Completed
        $transaction3 = Transaction::create([
            'box_id' => $box6->id,
            'pengguna_id' => $johnUser->id,
            'pengepul_id' => $pengepul2->id,
            'status' => 'completed',
            'total_price' => 250000,
            'admin_fee' => 25000,
            'pengepul_earnings' => 225000,
            'notes' => 'Elektronik dalam kondisi baik',
            'accepted_at' => now()->subDays(3),
            'completed_at' => now()->subDays(2),
        ]);

        // Transaction 4: Pending from pengepul2
        $transaction4 = Transaction::create([
            'box_id' => $box3->id,
            'pengguna_id' => $demoUser->id,
            'pengepul_id' => $pengepul2->id,
            'status' => 'pending',
            'total_price' => 95000,
            'admin_fee' => 9500,
            'pengepul_earnings' => 85500,
            'notes' => 'Tertarik dengan printer dan mouse',
        ]);

        // Update pengepul2 balance (from completed transaction)
        $pengepul2->increment('balance', 225000);

        // ============= CREATE NOTIFICATIONS =============
        
        // Notification for demoUser (pending transaction)
        Notification::create([
            'user_id' => $demoUser->id,
            'title' => 'Penawaran Baru',
            'message' => 'Pengepul Jaya menawarkan Rp 75.000 untuk Box Kamar Tidur',
            'type' => 'info',
            'is_read' => false,
            'data' => ['transaction_id' => $transaction1->id, 'box_id' => $box1->id],
        ]);

        Notification::create([
            'user_id' => $demoUser->id,
            'title' => 'Penawaran Baru',
            'message' => 'Budi Collector menawarkan Rp 95.000 untuk Box Ruang Kerja',
            'type' => 'info',
            'is_read' => false,
            'data' => ['transaction_id' => $transaction4->id, 'box_id' => $box3->id],
        ]);

        // Notification for pengepul1 (accepted transaction)
        Notification::create([
            'user_id' => $pengepul1->id,
            'title' => 'Transaksi Diterima',
            'message' => 'Demo User menerima penawaran Anda untuk Box Dapur',
            'type' => 'success',
            'is_read' => false,
            'data' => ['transaction_id' => $transaction2->id],
        ]);

        // Notification for johnUser (completed)
        Notification::create([
            'user_id' => $johnUser->id,
            'title' => 'Transaksi Selesai',
            'message' => 'Transaksi dengan Budi Collector telah selesai',
            'type' => 'success',
            'is_read' => true,
            'data' => ['transaction_id' => $transaction3->id],
        ]);

        // Admin notification (broadcast example)
        Notification::create([
            'user_id' => $demoUser->id,
            'title' => 'Selamat Datang di ReBox',
            'message' => 'Terima kasih telah bergabung dengan ReBox. Mulai kelola sampah Anda sekarang!',
            'type' => 'info',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $johnUser->id,
            'title' => 'Selamat Datang di ReBox',
            'message' => 'Terima kasih telah bergabung dengan ReBox. Mulai kelola sampah Anda sekarang!',
            'type' => 'info',
            'is_read' => true,
        ]);

        Notification::create([
            'user_id' => $janeUser->id,
            'title' => 'Selamat Datang di ReBox',
            'message' => 'Terima kasih telah bergabung dengan ReBox. Mulai kelola sampah Anda sekarang!',
            'type' => 'info',
            'is_read' => false,
        ]);

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('');
        $this->command->info('👥 USERS CREATED:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👤 ADMIN:');
        $this->command->info('   - admin@rebox.com / password123');
        $this->command->info('');
        $this->command->info('🚚 PENGEPUL:');
        $this->command->info('   - pengepul@rebox.com / password123');
        $this->command->info('   - budi@pengepul.com / password123');
        $this->command->info('');
        $this->command->info('👨‍💼 PENGGUNA:');
        $this->command->info('   - demo@rebox.com / password123');
        $this->command->info('   - john@example.com / password123');
        $this->command->info('   - jane@example.com / password123');
        $this->command->info('');
        $this->command->info('📊 DATA SUMMARY:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📦 Boxes: 7');
        $this->command->info('📁 Categories: 8');
        $this->command->info('📋 Items: 23');
        $this->command->info('💰 Transactions: 4 (1 pending, 1 accepted, 1 completed, 1 pending)');
        $this->command->info('🔔 Notifications: 7');
    }
}
