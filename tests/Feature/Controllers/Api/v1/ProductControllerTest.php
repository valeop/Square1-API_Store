<?php

namespace Tests\Feature\Controllers\Api\v1;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function test_index_endpoint_paginated_products()
    {
        Product::factory()->hasVariant(3)->count(5)->create(); //hasVariant() refers to relation
        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'variant' => [
                        '*' => [
                            'id',
                            'product_id',
                            'color',
                            'size',
                            'stock_quantity'
                        ]
                    ]
                ]
            ]
        ]);
    }


}

//test('example', function () {
//    $response = $this->get('/');
//
//    $response->assertStatus(200);
//});
