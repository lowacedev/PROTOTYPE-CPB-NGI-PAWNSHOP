<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;

class LocationSeeder extends Seeder
{
    /**
     * Seed Philippine location data from local JSON.
     */
    public function run(): void
    {
        $this->command->info('Loading location data from local JSON file...');
        $jsonPath = database_path('data/philippine_locations.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('Local JSON file not found at: ' . $jsonPath);
            return;
        }

        $jsonString = file_get_contents($jsonPath);
        $data = json_decode($jsonString, true);

        if (!$data) {
            $this->command->error('Failed to parse JSON file.');
            return;
        }

        // Disable foreign key checks for faster truncate/insert (if needed)
        // Note: Using truncate might cause issues if other tables reference these, 
        // so we'll just check if regions exist and skip if they do.
        if (Region::count() > 10) {
            $this->command->info('Location data already seeded. Skipping.');
            return;
        }

        // To prevent duplicate keys, we can just clear existing and re-seed
        // But doing it safely.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('barangays')->truncate();
        City::truncate();
        Province::truncate();
        Region::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();
        $barangayRows = [];

        foreach ($data as $regionCode => $regionInfo) {
            $regionName = $regionInfo['region_name'];
            $region = Region::create([
                'name' => $regionName,
                'code' => $regionCode,
            ]);
            $this->command->info("  Seeded Region: {$regionName}");

            if (isset($regionInfo['province_list'])) {
                foreach ($regionInfo['province_list'] as $provinceName => $provinceInfo) {
                    // Handle special cases in the JSON where province is a string instead of array
                    if (!is_array($provinceInfo)) continue;

                    $province = Province::create([
                        'region_id' => $region->id,
                        'name'      => $provinceName,
                        'code'      => substr(md5($provinceName . $region->id), 0, 20),
                    ]);

                    if (isset($provinceInfo['municipality_list'])) {
                        foreach ($provinceInfo['municipality_list'] as $cityName => $cityInfo) {
                            if (!is_array($cityInfo)) continue;

                            $city = City::create([
                                'province_id' => $province->id,
                                'name'        => $cityName,
                                'code'        => substr(md5($cityName . $province->id), 0, 20),
                            ]);

                            if (isset($cityInfo['barangay_list'])) {
                                foreach ($cityInfo['barangay_list'] as $barangayName) {
                                    $barangayRows[] = [
                                        'city_id'    => $city->id,
                                        'name'       => $barangayName,
                                        'code'       => substr(md5($barangayName . $city->id), 0, 20),
                                        'created_at' => $now,
                                        'updated_at' => $now,
                                    ];

                                    // Chunk inserts to prevent memory issues
                                    if (count($barangayRows) >= 1000) {
                                        DB::table('barangays')->insert($barangayRows);
                                        $barangayRows = [];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Insert remaining barangays
        if (count($barangayRows) > 0) {
            DB::table('barangays')->insert($barangayRows);
        }

        $this->command->info('Location seeding complete!');
        $this->command->info('Regions: ' . Region::count());
        $this->command->info('Provinces: ' . Province::count());
        $this->command->info('Cities: ' . City::count());
        $this->command->info('Barangays: ' . DB::table('barangays')->count());
    }
}
