<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use Illuminate\Testing\Fluent\AssertableJson;

class BooksControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_books_get_books_endpoint()
    {
        $books = Book::factory(3)->create();
        $response = $this->getJson('/api/books');
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJson(function (AssertableJson $json) use ($books) {
            $json->whereAllType([
                '0.id' => 'integer',
                '0.title' => 'string',
                '0.isbn' => 'string'
            ]);
            $json->hasAll(['0.id', '0.title', '0.isbn']);
            $book = $books->first();
            $json->whereAll([
                '0.id' => $book->id,
                '0.title' => $book->title,
                '0.isbn' => $book->isbn
            ]);
        });
    }
    public function test_books_get_single_book_endpoint()
    {
        $book = Book::factory(1)->createOne();
        $response = $this->getJson('/api/books/' . $book->id);
        $response->assertJson(function (AssertableJson $json) use ($book) {
            $book = $book->first();
            $json->hasAll(['id', 'title', 'isbn', 'created_at', 'updated_at']);
            $json->whereAllType([
                'id' => 'integer',
                'title' => 'string',
                'isbn' => 'string'
            ]);
            $json->whereAll([
                'id' => $book->id,
                'title' => $book->title,
                'isbn' => $book->isbn
            ]);
        });
    }
    public function test_post_books_endpoint()
    {
        $book = Book::factory(1)->makeOne()->toArray();        
        $response = $this->postJson('/api/books', $book);
        $response->assertStatus(201);
        $response->assertJson(function (AssertableJson $json) use ($book) {
            $json->whereAll([                
                'title' => $book['title'],
                'isbn' => $book['isbn'],
            ])->etc();
        });
    }
}
