<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Menu;
use PDO;

class SyncAronium extends Command
{
    protected $signature = 'app:sync-aronium';
    protected $description = 'Sync categories and menus from Aronium SQLite Database';

    public function handle()
    {
        $dbPath = base_path(env('ARONIUM_DB_PATH', 'aronium-database-2026-08-05-08-13.db'));
        
        if (!file_exists($dbPath)) {
            $this->error("Database file not found: {$dbPath}");
            return;
        }

        $this->info("Connecting to Aronium SQLite database...");
        $pdo = new PDO("sqlite:{$dbPath}");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Sync Categories
        $this->info("Syncing Categories...");
        $stmt = $pdo->query("SELECT Id, Name FROM ProductGroup");
        $aroniumGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $categoryMap = []; // Aronium ProductGroupId => CafeApp Category ID

        foreach ($aroniumGroups as $group) {
            $category = Category::updateOrCreate(
                ['name' => $group['Name']],
                ['status' => true]
            );
            $categoryMap[$group['Id']] = $category->id;
        }

        $this->info("Categories synced: " . count($aroniumGroups));

        // 2. Sync Menus
        $this->info("Syncing Menus...");
        // In Aronium, a Product might not have ProductGroupId (null).
        $stmt = $pdo->query("SELECT Id, ProductGroupId, Name, Price, Description, Image FROM Product");
        $aroniumProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $menuCount = 0;
        foreach ($aroniumProducts as $product) {
            $categoryId = null;
            if ($product['ProductGroupId']) {
                $categoryId = $categoryMap[$product['ProductGroupId']] ?? null;
            }

            if (!$categoryId) {
                $uncategorized = Category::firstOrCreate(['name' => 'Uncategorized'], ['status' => true]);
                $categoryId = $uncategorized->id;
            }

            Menu::updateOrCreate(
                ['name' => $product['Name']],
                [
                    'category_id' => $categoryId,
                    'price' => $product['Price'] ?? 0,
                    'description' => $product['Description'] ?? '',
                    'status' => true,
                ]
            );
            $menuCount++;
        }

        $this->info("Menus synced: " . $menuCount);
        $this->info("Sync completed successfully!");
    }
}
