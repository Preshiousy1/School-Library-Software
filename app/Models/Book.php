<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'author',
        'category',
        'year_published',
        'is_available',
        'is_borrowed'
    ];

    public function book_requests()
    {
        return $this->hasMany(BookRequest::class, 'book_id');
    }

    public function pending_requests()
    {
        return $this->book_requests()->where('book_requests.is_pending', 1)->where('book_requests.is_returned', null)->where('book_requests.is_accepted', null);
    }

    public function accepted_requests()
    {
        return $this->book_requests()->where('book_requests.is_returned', null)->where('book_requests.is_accepted', true);
    }
}
