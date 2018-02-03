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
            'priority' => 1,
        ])->save();
        $dickSmith = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Dick Smith',
            'abbreviation' => 'ds',
            'domain' => 'https://www.dicksmith.com.au',
            'ecommerce_url' => 'https://www.dicksmith.com.au/da/',
            'logo' => NULL,
            'priority' => 1,
        ])->save();
        $jbhifi = \OzSpy\Models\Base\Retailer::create([
            'name' => 'JB Hi-Fi',
            'abbreviation' => 'jbhifi',
            'domain' => 'https://www.jbhifi.com.au',
            'ecommerce_url' => 'https://www.jbhifi.com.au/',
            'logo' => NULL,
            'priority' => 1,
        ])->save();
        $officeworks = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Officeworks',
            'abbreviation' => 'ow',
            'domain' => 'https://www.officeworks.com.au',
            'ecommerce_url' => 'https://www.officeworks.com.au/',
            'logo' => NULL,
            'priority' => 2,
        ])->save();
        $harveyNoman = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Harvey Norman',
            'abbreviation' => 'hn',
            'domain' => 'https://www.harveynorman.com.au',
            'ecommerce_url' => 'https://www.harveynorman.com.au/',
            'logo' => NULL,
            'priority' => 6,
        ])->save();
        $theGoodGuys = \OzSpy\Models\Base\Retailer::create([
            'name' => 'The Good Guys',
            'abbreviation' => 'tgg',
            'domain' => 'https://www.thegoodguys.com.au',
            'ecommerce_url' => 'https://www.thegoodguys.com.au/',
            'logo' => NULL,
            'priority' => 6,
        ])->save();
        $appliancesOnline = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Appliances Online',
            'abbreviation' => 'aol',
            'domain' => 'https://www.appliancesonline.com.au',
            'ecommerce_url' => 'https://www.appliancesonline.com.au/',
            'logo' => NULL,
            'priority' => 6,
        ])->save();
        $winningAppliances = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Winning Appliances',
            'abbreviation' => 'wa',
            'domain' => 'https://www.winningappliances.com.au',
            'ecommerce_url' => 'https://www.winningappliances.com.au/',
            'logo' => NULL,
            'priority' => 6,
        ])->save();
        $joyceMayne = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Joyce Mayne',
            'abbreviation' => 'jm',
            'domain' => 'https://www.joycemayne.com.au',
            'ecommerce_url' => 'https://www.joycemayne.com.au/',
            'logo' => NULL,
            'priority' => 6,
        ])->save();
        $godfreys = \OzSpy\Models\Base\Retailer::create([
            'name' => 'Godfreys',
            'abbreviation' => 'gf',
            'domain' => 'https://www.godfreys.com.au',
            'ecommerce_url' => 'https://www.godfreys.com.au/',
            'logo' => NULL,
            'priority' => 6,
        ])->save();
    }
}
