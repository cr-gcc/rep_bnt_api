<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    $users = [
      [
        'name' => 'User',
        'last_name' => 'One',
        'email' => 'userone@example.com',
        'user' => 'XXXX000000X0',
        'pass' => Hash::make('admin123456'),
      ],
      [
        'name' => 'User',
        'last_name' => 'Two',
        'email' => 'usertwo@example.com',
        'user' => 'XXXX000000X1',
        'pass' => Hash::make('user123456'),
      ]
    ];
    foreach ($users as $user) {
      User::firstOrCreate([
        'name' => $user['name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'user' => $user['user'],
        'password' => $user['pass'],
      ]);
    }
  }
}
