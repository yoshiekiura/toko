<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(KelurahanSeeder::class);
        $this->call(PermissionSeeder::class);        
        $this->call(KasSeeder::class);      
        $this->call(BarangSeeder::class);  
        $this->call(SatuanSeeder::class);
        $this->call(KategoriBarangSeeder::class);
    }
}
