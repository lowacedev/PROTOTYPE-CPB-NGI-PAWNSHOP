<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;

class LocationSeeder extends Seeder
{
    /**
     * Seed Philippine location data from PSGC Cloud API.
     * API docs: https://psgc.cloud
     */
    public function run(): void
    {
        $this->command->info('Fetching regions from PSGC API...');
        $regions = Http::retry(3, 1000)->get('https://psgc.cloud/api/regions')->json();

        foreach ($regions as $regionData) {
            // Skip if region already exists to allow resuming
            $region = Region::where('code', $regionData['code'])->first();
            
            if ($region) {
                $this->command->info("  Skipping Region: {$region->name} (Already seeded)");
                continue;
            }

            $region = Region::create([
                'name' => $regionData['name'],
                'code' => $regionData['code'],
            ]);
            $this->command->info("  Seeding Region: {$region->name}");

            // Fetch provinces for this region
            $provinces = Http::retry(3, 1000)->get("https://psgc.cloud/api/regions/{$regionData['code']}/provinces")->json();

            foreach ($provinces as $provData) {
                $province = Province::create([
                    'region_id' => $region->id,
                    'name'      => $provData['name'],
                    'code'      => $provData['code'],
                ]);

                // Fetch cities/municipalities for this province
                $cities = Http::retry(3, 1000)->get("https://psgc.cloud/api/provinces/{$provData['code']}/cities-municipalities")->json();

                foreach ($cities as $cityData) {
                    $city = City::create([
                        'province_id' => $province->id,
                        'name'        => $cityData['name'],
                        'code'        => $cityData['code'],
                    ]);

                    // Fetch barangays for this city
                    $barangays = Http::retry(3, 1000)->get("https://psgc.cloud/api/cities-municipalities/{$cityData['code']}/barangays")->json();

                    if (!empty($barangays)) {
                        $rows = [];
                        $now = now();
                        foreach ($barangays as $brgyData) {
                            $rows[] = [
                                'city_id'    => $city->id,
                                'name'       => $brgyData['name'],
                                'code'       => $brgyData['code'],
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                        // Bulk insert barangays for performance
                        DB::table('barangays')->insert($rows);
                    }
                }
            }
        }

        $this->command->info('Location seeding complete!');
        $this->command->info('Regions: ' . Region::count());
        $this->command->info('Provinces: ' . Province::count());
        $this->command->info('Cities: ' . City::count());
        $this->command->info('Barangays: ' . DB::table('barangays')->count());
    }
}
