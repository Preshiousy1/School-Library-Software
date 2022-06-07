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
        return BookRequest::where('user_id', $this->id)->where('is_returned', '!=', true)->where('is_accepted', '!=', true)->where('is_pending', '=', true)->get();
    }

    public function accepted_requests()
    {
        return $this->book_requests()->where('is_returned', '!=', true)->where('is_accepted', true)->get();
    }
}
