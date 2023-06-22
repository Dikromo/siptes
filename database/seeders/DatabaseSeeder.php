<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Customer;
use App\Models\Fileexcel;
use App\Models\Roleuser;
use App\Models\Statuscall;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(30)->create();
        Roleuser::create([
            'nama'          => 'Superadmin',
            'created_id'       => '1',
        ]);
        Roleuser::create([
            'nama'          => 'Supervisor',
            'created_id'       => '1',
        ]);
        Roleuser::create([
            'nama'          => 'Telemarketing',
            'created_id'       => '1',
        ]);
        Roleuser::create([
            'nama'          => 'Admin',
            'created_id'       => '1',
        ]);
        Statuscall::create([
            'nama'          => 'Closing',
            'created_id'       => '1',
        ]);
        Statuscall::create([
            'nama'          => 'Interest',
            'created_id'       => '2',
        ]);
        Statuscall::create([
            'nama'          => 'Call Back',
            'created_id'       => '3',
        ]);
        Fileexcel::create([
            'kode'          => 'SIP-001',
            'nama_file'     => 'bulk.xlxs',
            'user_id'       => '1',
        ]);
        Fileexcel::create([
            'kode'          => 'SIP-002',
            'nama_file'     => 'bulk2.xlxs',
            'user_id'       => '1',
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Customer::factory(20)->create();
    }
}
