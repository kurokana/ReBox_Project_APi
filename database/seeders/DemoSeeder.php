<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Box;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Demo Users
        $demoUser = User::create([
            'name' => 'Demo User',
            'email' => 'demo@rebox.com',
            'password' => Hash::make('password123'),
        ]);

        $johnUser = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
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

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('👤 Demo Users:');
        $this->command->info('   - demo@rebox.com / password123');
        $this->command->info('   - john@example.com / password123');
        $this->command->info('📦 Created: 7 boxes, 8 categories, 23 items');
    }
}
