<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $navigations = [
            ['name' => 'Home', 'route' => 'home', 'order' => 1],
            ['name' => 'Profile', 'route' => 'profile.edit', 'order' => 2],
            ['name' => 'Saved Times', 'route' => 'saved-times.index', 'order' => 3],
            ['name' => 'Generate PDF', 'route' => 'generate.pdf', 'order' => 4],
            ['name' => 'Email PDF', 'route' => 'email_form', 'order' => 5],
            // Add more items as needed
        ];
    
        foreach ($navigations as $nav) {
            \App\Models\Navigation::create($nav);
        }
    }
    
}
