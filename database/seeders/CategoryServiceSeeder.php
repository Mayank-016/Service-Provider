<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Service;

class CategoryServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the 'Home Service' category
        $homeServiceCategory = Category::create([
            'name' => 'Home Service',
        ]);

        // Create 4 services for 'Home Service' category with descriptions
        $homeServices = [
            ['name' => 'Plumbing', 'description' => 'Professional plumbing services for your home, including installation and repairs.'],
            ['name' => 'Electrical Work', 'description' => 'Expert electrical services to ensure safety and functionality for your home.'],
            ['name' => 'Cleaning', 'description' => 'Comprehensive cleaning services to keep your home spotless and hygienic.'],
            ['name' => 'Carpentry', 'description' => 'Skilled carpentry services for custom furniture and home repairs.'],
        ];

        foreach ($homeServices as $service) {
            Service::create([
                'name' => $service['name'],
                'description' => $service['description'],
                'category_id' => $homeServiceCategory->id,
            ]);
        }

        // Create the 'Technical Service' category
        $technicalServiceCategory = Category::create([
            'name' => 'Technical Service',
        ]);

        // Create 4 services for 'Technical Service' category with descriptions
        $technicalServices = [
            ['name' => 'Computer Repair', 'description' => 'Fast and reliable computer repair services for desktops and laptops.'],
            ['name' => 'Mobile Repair', 'description' => 'Get your mobile phone fixed quickly with our expert mobile repair services.'],
            ['name' => 'Networking', 'description' => 'Professional networking services to set up or fix your home or office network.'],
            ['name' => 'Software Installation', 'description' => 'Install the software you need with our efficient software installation services.'],
        ];

        foreach ($technicalServices as $service) {
            Service::create([
                'name' => $service['name'],
                'description' => $service['description'],
                'category_id' => $technicalServiceCategory->id,
            ]);
        }
    }
}
