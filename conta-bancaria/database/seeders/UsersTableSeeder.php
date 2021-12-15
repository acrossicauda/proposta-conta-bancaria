<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //(`name`, `Ativa`, `LimiteDisponivel`) VALUES ('Tiago', '1', '100');
        User::create([
            'name'      => 'Tiago',
            'Ativa'     => 1,
            'LimiteDisponivel'  => 100,
            'api_token' => Hash::make('teste'),
            'password'  => Hash::make('teste'),
        ]);
    }
}
