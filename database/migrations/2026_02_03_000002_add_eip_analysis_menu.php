<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Menu;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if EIP Analysis menu already exists
        if (Menu::where('slug', 'eip-analysis')->exists()) {
            return;
        }

        // Find the SEC Analysis menu to insert EIP after it
        $secMenu = Menu::where('slug', 'sec-analysis')->first();
        $order = $secMenu ? $secMenu->order + 1 : 7;

        // Bump order of all menus that come after
        Menu::where('order', '>=', $order)
            ->whereNull('parent_id')
            ->increment('order');

        Menu::create([
            'name' => 'EIP Analysis',
            'slug' => 'eip-analysis',
            'icon' => 'bi bi-speedometer2',
            'route' => 'eip-analysis',
            'parent_id' => null,
            'order' => $order,
            'is_active' => true,
        ]);
    }

    public function down(): void
    {
        $eipMenu = Menu::where('slug', 'eip-analysis')->first();

        if ($eipMenu) {
            // Decrement order of menus that came after
            Menu::where('order', '>', $eipMenu->order)
                ->whereNull('parent_id')
                ->decrement('order');

            $eipMenu->delete();
        }
    }
};
