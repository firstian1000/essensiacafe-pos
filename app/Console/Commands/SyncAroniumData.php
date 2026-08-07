<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Menu;
use PDO;
use Exception;

class SyncAroniumData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aronium:sync {--file= : Path to the Aronium DB file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync categories and menus from Aronium SQLite database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file') ?: base_path(env('ARONIUM_DB_PATH', 'aronium-database-2026-08-05-08-13.db'));
        
        if (!file_exists($file)) {
            $this->error("Database file not found: {$file}");
            return;
        }

        $this->info("Connecting to Aronium SQLite database at {$file}...");

        try {
            $pdo = new PDO('sqlite:' . $file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 1. Sync Categories (ProductGroup)
            $this->info("Syncing Categories (ProductGroup)...");
            $stmt = $pdo->query("SELECT Id, Name FROM ProductGroup");
            $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Map to store Aronium Group Id => Laravel Category Id
            $categoryIdMap = [];

            foreach ($groups as $group) {
                $category = Category::updateOrCreate(
                    ['name' => $group['Name']],
                    ['status' => true] // Ensure it's active
                );
                
                $categoryIdMap[$group['Id']] = $category->id;
                $this->line("Synced category: {$category->name}");
            }
            
            $this->info(count($groups) . " categories synced.");

            // 2. Sync Menus (Product)
            $this->info("Syncing Menus (Product)...");
            $stmt = $pdo->query("SELECT Id, ProductGroupId, Name, Price, Description FROM Product");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $menuCount = 0;
            foreach ($products as $product) {
                if (empty($product['ProductGroupId'])) {
                    // Skip if product has no group (category)
                    continue;
                }
                
                $laravelCategoryId = $categoryIdMap[$product['ProductGroupId']] ?? null;
                
                if (!$laravelCategoryId) {
                    $this->warn("Skipping product {$product['Name']} because its group ID {$product['ProductGroupId']} was not found.");
                    continue;
                }

                $menu = Menu::updateOrCreate(
                    ['name' => $product['Name']],
                    [
                        'category_id' => $laravelCategoryId,
                        'price' => (int) $product['Price'],
                        'description' => $product['Description'],
                        'status' => true
                    ]
                );
                
                $this->line("Synced menu: {$menu->name} - Rp {$menu->price}");
                $menuCount++;
            }
            
            $this->info("{$menuCount} menus synced.");
            $this->info("Synchronization completed successfully!");

        } catch (Exception $e) {
            $this->error("Error syncing data: " . $e->getMessage());
        }
    }
}
