<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    CafeTable::where('status', 'occupied')->update(['status' => 'available']);
})->dailyAt('06:00');

Artisan::command('menus:import-essensia-sql {path}', function (string $path) {
    if (! file_exists($path)) {
        $this->error("File SQL tidak ditemukan: {$path}");
        return 1;
    }

    $sql = file_get_contents($path);

    $splitTuples = function (string $values): array {
        $tuples = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $length = strlen($values);

        for ($i = 0; $i < $length; $i++) {
            $char = $values[$i];
            $next = $i + 1 < $length ? $values[$i + 1] : null;

            if ($char === "'" && $inString && $next === "'") {
                $current .= "''";
                $i++;
                continue;
            }

            if ($char === "'") {
                $inString = ! $inString;
                $current .= $char;
                continue;
            }

            if (! $inString && $char === '(') {
                if ($depth > 0) {
                    $current .= $char;
                }
                $depth++;
                continue;
            }

            if (! $inString && $char === ')') {
                $depth--;
                if ($depth === 0) {
                    $tuples[] = $current;
                    $current = '';
                    continue;
                }
            }

            if ($depth > 0) {
                $current .= $char;
            }
        }

        return $tuples;
    };

    $parseRows = function (string $table) use ($sql, $splitTuples): array {
        if (! preg_match_all("/INSERT INTO {$table} \\([^)]*\\) VALUES\\s*(.*?);/is", $sql, $matches)) {
            return [];
        }

        $rows = [];

        foreach ($matches[1] as $values) {
            foreach ($splitTuples($values) as $tuple) {
                $rows[] = str_getcsv(str_replace("''", "'", $tuple), ',', "'", '\\');
            }
        }

        return $rows;
    };

    $brands = [];
    foreach ($parseRows('brands') as $row) {
        $brands[(int) $row[0]] = $row[1];
    }

    $categories = [];
    foreach ($parseRows('menu_categories') as $row) {
        $categories[(int) $row[0]] = [
            'brand_id' => (int) $row[1],
            'name' => $row[2],
            'sort_order' => (int) $row[4],
        ];
    }

    $menus = [];
    foreach ($parseRows('menus') as $row) {
        $menus[(int) $row[0]] = [
            'category_id' => (int) $row[1],
            'name' => $row[2],
            'description' => $row[4] ?: null,
            'price' => (int) $row[5],
        ];
    }

    $variantMenus = [];
    foreach ($parseRows('menu_variants') as $row) {
        $menuId = (int) $row[0];
        $variantName = $row[1];
        $variantPrice = (int) $row[2];
        $isDefault = (int) $row[3] === 1;

        if ($isDefault || ! isset($menus[$menuId])) {
            continue;
        }

        $variantMenus[] = [
            'category_id' => $menus[$menuId]['category_id'],
            'name' => $menus[$menuId]['name'].' - '.$variantName,
            'description' => trim(($menus[$menuId]['description'] ?? '').' Varian: '.$variantName),
            'price' => $variantPrice,
            'source_menu_id' => $menuId,
        ];
    }

    foreach ($menus as $menu) {
        if ($menu['category_id'] === 7) {
            $variantMenus[] = [
                'category_id' => 7,
                'name' => $menu['name'].' - Double Mie',
                'description' => trim(($menu['description'] ?? '').' Varian: Double Mie.'),
                'price' => 26000,
                'source_menu_id' => null,
            ];
        }
    }

    if ($categories === [] || $menus === []) {
        $this->error('File SQL tidak berisi data kategori/menu yang bisa diimport.');
        return 1;
    }

    DB::transaction(function () use ($brands, $categories, $menus, $variantMenus) {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::table('order_items')->delete();
        DB::table('orders')->delete();
        Menu::query()->delete();
        Category::query()->delete();

        DB::table('sqlite_sequence')->whereIn('name', ['order_items', 'orders', 'menus', 'categories'])->delete();

        foreach ($categories as $id => $category) {
            Category::create([
                'id' => $id,
                'name' => $category['name'],
                'description' => 'Brand: '.($brands[$category['brand_id']] ?? 'Esensia'),
                'status' => true,
            ]);
        }

        foreach ($menus as $id => $menu) {
            Menu::create([
                'id' => $id,
                'category_id' => $menu['category_id'],
                'name' => $menu['name'],
                'description' => $menu['description'],
                'price' => $menu['price'],
                'image' => null,
                'status' => true,
                'is_recommended' => in_array($id, [3, 10, 11, 12, 14, 34, 35, 37], true),
            ]);
        }

        foreach ($variantMenus as $menu) {
            Menu::create([
                'category_id' => $menu['category_id'],
                'name' => $menu['name'],
                'description' => $menu['description'],
                'price' => $menu['price'],
                'image' => null,
                'status' => true,
                'is_recommended' => false,
            ]);
        }

        DB::statement('PRAGMA foreign_keys = ON');
    });

    $this->info('Import selesai: '.count($categories).' kategori dan '.(count($menus) + count($variantMenus)).' menu dimasukkan.');
    $this->line('- Menu dasar: '.count($menus));
    $this->line('- Menu hasil ekspansi varian: '.count($variantMenus));
    $this->warn('Data order/order item lama dibersihkan karena memakai menu lama.');

    return 0;
})->purpose('Import katalog menu Esensia dari dump SQL MySQL ke schema aplikasi saat ini');
