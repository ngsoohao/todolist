<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDoItem extends Model
{
    use HasFactory;
    protected $table='to_do_item';

    protected $fillable = ['title', 'is_complete', 'category_name'];
}
