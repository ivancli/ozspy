<?php

use Illuminate\Database\Seeder;

class RetailersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kogan = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Kogan',
            'abbreviation' => 'kg',
            'domain' => 'https://www.kogan.com',
            'ecommerce_url' => 'https://www.kogan.com/au/',
            'logo' => NULL,
        ])->save();
        $dickSmith = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Dick Smith',
            'abbreviation' => 'ds',
            'domain' => 'https://www.dicksmith.com.au',
            'ecommerce_url' => 'https://www.dicksmith.com.au/da/',
            'logo' => NULL,
        ])->save();
        $jbhifi = \OzSpy\Models\Base\Retailer::create([
            'name' => 'JB Hi-Fi',
            'abbreviation' => 'jbhifi',
            'domain' => 'https://www.jbhifi.com.au',
            'ecommerce_url' => 'https://www.jbhifi.com.au/',
            'logo' => NULL,
        ])->save();
        $officeworks = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Officeworks',
            'abbreviation' => 'ow',
            'domain' => 'https://www.officeworks.com.au',
            'ecommerce_url' => 'https://www.officeworks.com.au/',
            'logo' => NULL,
        ])->save();
    }
}
