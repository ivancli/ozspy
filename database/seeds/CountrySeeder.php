<?php

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    protected $countryModel;

    public function __construct(\OzSpy\Models\Common\Country $countryModel)
    {
        $this->countryModel = $countryModel;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content = file_get_contents(base_path('vendor/mledoze/countries/dist/countries.json'));
        $countries = json_decode($content);

        if (!is_null($countries) && json_last_error() === JSON_ERROR_NONE) {
            foreach ($countries as $country) {
                $this->countryModel->create([
                    'name' => $country->name->common,
                    'official_name' => $country->name->official,
                    'cca2' => $country->cca2,
                    'cca3' => $country->cca3,
                    'ccn3' => $country->ccn3,
                    'region' => empty($country->region) ? null : $country->region,
                    'subregion' => empty($country->subregion) ? null : $country->subregion,
                ]);
            }
        }
    }
}
