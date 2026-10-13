<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAlphabeticalOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_nav@bunardi.com',
            'role' => 'admin',
        ]);
    }

    public function test_navigation_groups_and_submenus_are_sorted_alphabetically(): void
    {
        $this->actingAs($this->admin);

        $panel = filament()->getPanel('admin');
        filament()->setCurrentPanel($panel);

        $navigation = $panel->getNavigation();

        $groupLabels = [];
        $submenusByGroup = [];

        foreach ($navigation as $group) {
            $label = $group->getLabel();
            if ($label) {
                $groupLabels[] = $label;
                $submenusByGroup[$label] = collect($group->getItems())->map(fn ($item) => $item->getLabel())->values()->all();
            }
        }

        // 1. Verify Groups are sorted alphabetically
        $sortedGroupLabels = $groupLabels;
        sort($sortedGroupLabels, SORT_NATURAL | SORT_FLAG_CASE);
        $this->assertEquals($sortedGroupLabels, $groupLabels, 'Navigation groups are not sorted alphabetically.');

        // 2. Verify Submenus within each group are sorted alphabetically
        foreach ($submenusByGroup as $groupName => $items) {
            $sortedItems = $items;
            sort($sortedItems, SORT_NATURAL | SORT_FLAG_CASE);
            $this->assertEquals(
                $sortedItems,
                $items,
                "Submenus under group '{$groupName}' are not sorted alphabetically."
            );
        }
    }
}
