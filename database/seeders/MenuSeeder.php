<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Read source JS file
        $menuPath = base_path('resources/js/Components/MenuData.js');
        if (!File::exists($menuPath)) {
            $this->command?->warn('MenuData.js not found; skipping MenuSeeder.');
            return;
        }

        $raw = File::get($menuPath);

        // Sanitize JS → JSON
        $json = $this->convertJsExportToJson($raw);
        $parsed = json_decode($json, true);

        if (!is_array($parsed)) {
            $this->command?->warn('Failed to parse MenuData.js, falling back to minimal seed data.');
        }

        // Truncate tables
        DB::table('food_item_suggestions')->truncate();
        DB::table('food_item_size')->truncate();
        DB::table('sizes')->truncate();
        DB::table('food_item_side_dish')->truncate();
        DB::table('side_dishes')->truncate();
        DB::table('food_item_calories')->truncate();
        DB::table('food_items')->truncate();
        DB::table('categories')->truncate();

        // Helper caches
        $categoryIdByName = [];
        $sideDishIdByKey = [];
        $sizeIdByKey = [];
        // Use parsed data if available; otherwise, fallback to empty
        $groups = [];
        if (is_array($parsed) && isset($parsed['regular']) && isset($parsed['simple'])) {
            $groups = ['regular' => $parsed['regular'], 'simple' => $parsed['simple']];
        }

        $insertedCount = 0;

        foreach (['regular', 'simple'] as $groupKey) {
            $items = $groups[$groupKey] ?? [];
            foreach ($items as $item) {
                $categoryName = $item['category'];
                if (!isset($categoryIdByName[$categoryName])) {
                    $categoryIdByName[$categoryName] = DB::table('categories')->insertGetId([
                        'name' => $categoryName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $foodItemId = DB::table('food_items')->insertGetId([
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'category_id' => $categoryIdByName[$categoryName],
                    'image' => $item['image'] ?? null,
                    'description' => $item['desc'] ?? ($item['description'] ?? null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertedCount++;

                foreach ($item['calories'] ?? [] as $cal) {
                    DB::table('food_item_calories')->insert([
                        'food_item_id' => $foodItemId,
                        'part' => $cal['part'],
                        'kcal' => $cal['kcal'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach ($item['sideDishes'] ?? [] as $sd) {
                    $key = $sd['id'];
                    if (!isset($sideDishIdByKey[$key])) {
                        $sideDishIdByKey[$key] = DB::table('side_dishes')->insertGetId([
                            'external_id' => $sd['id'] ?? null,
                            'name' => $sd['name'],
                            'icon' => $sd['icon'] ?? null,
                            'enabled' => (bool)($sd['enabled'] ?? true),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    DB::table('food_item_side_dish')->insert([
                        'food_item_id' => $foodItemId,
                        'side_dish_id' => $sideDishIdByKey[$key],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach ($item['sizes'] ?? [] as $size) {
                    $key = $size['id'];
                    if (!isset($sizeIdByKey[$key])) {
                        $sizeIdByKey[$key] = DB::table('sizes')->insertGetId([
                            'external_id' => $size['id'] ?? null,
                            'name' => $size['name'],
                            'extra_price' => (int)($size['price'] ?? 0),
                            'multiplier' => (float)($size['multiplier'] ?? 1.0),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    DB::table('food_item_size')->insert([
                        'food_item_id' => $foodItemId,
                        'size_id' => $sizeIdByKey[$key],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach ($item['suggestions'] ?? [] as $sg) {
                    DB::table('food_item_suggestions')->insert([
                        'food_item_id' => $foodItemId,
                        'suggested_item_id' => (int)($sg['id'] ?? 0),
                        'suggested_name' => $sg['name'] ?? '',
                        'suggested_price' => (int)($sg['price'] ?? 0),
                        'suggested_image' => $sg['image'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if ($this->command) {
            $this->command->info("Inserted {$insertedCount} menu items from MenuData.js");
        }
    }

    private function convertJsExportToJson(string $raw): string
    {
        // Remove export prefix/suffix
        $s = preg_replace('/^\s*export\s+const\s+menuItems\s*=\s*/m', '', $raw);
        $s = trim($s);
        if (str_ends_with($s, ';')) {
            $s = substr($s, 0, -1);
        }

        // Remove JS comments
        $s = preg_replace('#/\*.*?\*/#s', '', $s); // block comments
        $s = preg_replace('~(^|\s)//.*$~m', '$1', $s); // line comments (use ~ delimiter to avoid // conflict)

        // Quote object keys: { id: 1 } -> { "id": 1 }
        $s = preg_replace('/([\{,]\s*)([A-Za-z_][A-Za-z0-9_]*)\s*:/u', '$1"$2":', $s);

        // JSON expects true/false,null as lowercase already – OK
        // Ensure trailing commas removed (common in JS)
        $s = preg_replace('/,\s*([}\]])/', '$1', $s);

        return $s;
    }
}


