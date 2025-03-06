<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;

class ItemTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    // public function it_can_create_an_item()
    // {
    //     Item::create([
    //         'name' => 'Test Item',
    //         'status' => 'Allowed'
    //     ]);

    //     $this->assertDatabaseHas('items', [
    //         'name' => 'Test Item',
    //         'status' => 'Allowed'
    //     ]);
    // }

    /** @test */
    // public function it_filters_allowed_items_with_scope()
    // {
    //     // Create multiple items
    //     Item::create(['name' => 'Allowed Item 1', 'status' => 'Allowed']);
    //     Item::create(['name' => 'Prohibited Item', 'status' => 'Prohibited']);
    //     Item::create(['name' => 'Allowed Item 2', 'status' => 'Allowed']);

    //     // Get only Allowed items
    //     $allowedItems = Item::allowed()->get();

    //     // Assert the count and contents
    //     $this->assertCount(2, $allowedItems);
    //     $this->assertTrue($allowedItems->every(fn($item) => $item->status === 'Allowed'));
    // }
/** @test */
    public function it_can_create_item()
    {
        $data = [
            'name' => 'Laptop',
            'status' => 'Allowed',
        ];

        $response = $this->postJson('/api/items', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }
}
