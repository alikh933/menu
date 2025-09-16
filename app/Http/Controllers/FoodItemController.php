<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FoodItemController extends Controller
{
    public function index()
    {
        // Fetch base items with category
        $items = DB::table('food_items as fi')
            ->join('categories as c', 'c.id', '=', 'fi.category_id')
            ->select('fi.id', 'fi.name', 'fi.price', 'fi.image', 'fi.description', 'c.name as category')
            ->orderBy('fi.id')
            ->get();

        if ($items->isEmpty()) {
            return Inertia::render('RestaurantMenu');
        }

        $itemIds = $items->pluck('id')->all();

        // Calories
        $calories = DB::table('food_item_calories')
            ->whereIn('food_item_id', $itemIds)
            ->select('food_item_id', 'part', 'kcal')
            ->get()
            ->groupBy('food_item_id');

        // Side dishes (pivot)
        $sideDishPivots = DB::table('food_item_side_dish')
            ->whereIn('food_item_id', $itemIds)
            ->select('food_item_id', 'side_dish_id')
            ->get()
            ->groupBy('food_item_id');
        $sideDishIds = $sideDishPivots->flatMap->pluck('side_dish_id')->unique()->values()->all();
        $sideDishes = collect();
        if (!empty($sideDishIds)) {
            $sideDishes = DB::table('side_dishes')
                ->whereIn('id', $sideDishIds)
                ->select('id', 'external_id', 'name', 'icon', 'enabled')
                ->get()
                ->keyBy('id');
        }

        // Sizes (pivot)
        $sizePivots = DB::table('food_item_size')
            ->whereIn('food_item_id', $itemIds)
            ->select('food_item_id', 'size_id')
            ->get()
            ->groupBy('food_item_id');
        $sizeIds = $sizePivots->flatMap->pluck('size_id')->unique()->values()->all();
        $sizes = collect();
        if (!empty($sizeIds)) {
            $sizes = DB::table('sizes')
                ->whereIn('id', $sizeIds)
                ->select('id', 'external_id', 'name', 'extra_price', 'multiplier')
                ->get()
                ->keyBy('id');
        }

        // Suggestions
        $suggestions = DB::table('food_item_suggestions')
            ->whereIn('food_item_id', $itemIds)
            ->select('food_item_id', 'suggested_item_id', 'suggested_name', 'suggested_price', 'suggested_image')
            ->get()
            ->groupBy('food_item_id');

        // Build array with the same shape the frontend expects
        $itemsArray = [];
        foreach ($items as $item) {
            $itemCalories = ($calories[$item->id] ?? collect())->map(function ($c) {
                return [
                    'part' => $c->part,
                    'kcal' => (int) $c->kcal,
                ];
            })->values()->all();

            $itemSideDishes = [];
            foreach (($sideDishPivots[$item->id] ?? collect()) as $p) {
                $sd = $sideDishes[$p->side_dish_id] ?? null;
                if ($sd) {
                    $itemSideDishes[] = [
                        'id' => $sd->external_id ?: ('sd_'.$sd->id),
                        'name' => $sd->name,
                        'icon' => $sd->icon,
                        'enabled' => (bool) $sd->enabled,
                    ];
                }
            }

            $itemSizes = [];
            foreach (($sizePivots[$item->id] ?? collect()) as $p) {
                $sz = $sizes[$p->size_id] ?? null;
                if ($sz) {
                    $itemSizes[] = [
                        'id' => $sz->external_id ?: ('size_'.$sz->id),
                        'name' => $sz->name,
                        'price' => (int) $sz->extra_price,
                        'multiplier' => (float) $sz->multiplier,
                    ];
                }
            }

            $itemSuggestions = ($suggestions[$item->id] ?? collect())->map(function ($s) {
                return [
                    'id' => (int) $s->suggested_item_id,
                    'name' => $s->suggested_name,
                    'price' => (int) $s->suggested_price,
                    'image' => $s->suggested_image,
                ];
            })->values()->all();

            $itemsArray[] = [
                'id' => (int) $item->id,
                'name' => $item->name,
                'price' => (int) $item->price,
                'category' => $item->category,
                'image' => $item->image,
                'desc' => $item->description,
                'calories' => $itemCalories,
                'sideDishes' => $itemSideDishes,
                'sizes' => $itemSizes,
                'suggestions' => $itemSuggestions,
            ];
        }

        // Return the same full list for both sections (no separation)
        $menuItems = [
            'regular' => array_values($itemsArray),
            'simple' => array_values($itemsArray),
        ];

        return Inertia::render('RestaurantMenu', [
            'menuItems' => $menuItems,
        ]);
    }
}
