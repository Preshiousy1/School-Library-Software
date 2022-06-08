<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Student extends User
{
    protected static $singleTableType = 'student';

    public function book_requests()
    {
        return $this->hasMany(BookRequest::class, 'user_id');
    }

    public function pending_requests()
    {
        // return $this->book_requests();
        // ->where('is_returned', '!=', true)->where('is_accepted', '!=', true)->where('is_pending', '=', true)->get();

        return $this->book_requests()->where('book_requests.is_pending', 1)->where('book_requests.is_returned', null)->where('book_requests.is_accepted', null);
    }

    public function accepted_requests() //borrowed_book
    {
        return $this->book_requests()->where('book_requests.is_returned', null)->where('book_requests.is_accepted', true);
    }
}
