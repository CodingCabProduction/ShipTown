<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;

class RemoveProductsWithoutRequiredTagJob extends UniqueJob
{
    public function handle(): void
    {
        $requiredTag = 'Available Online';

        do {
            $recordsAffected = DB::affectingStatement("
                DELETE FROM modules_magento2msi_inventory_source_items WHERE ID IN (SELECT ID FROM (SELECT modules_magento2msi_inventory_source_items.ID
                FROM modules_magento2msi_inventory_source_items
                LEFT JOIN products
                  ON products.id = modules_magento2msi_inventory_source_items.product_id
                LEFT JOIN taggables
                  ON taggables.tag_name = ?
                  AND taggables.taggable_id = modules_magento2msi_inventory_source_items.product_id
                  AND taggables.taggable_type = ?
                WHERE taggables.tag_name IS NULL
                LIMIT 5000) as tbl)
            ", [$requiredTag, Product::class]);
        } while ($recordsAffected > 0);
    }
}
