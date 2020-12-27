<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
	public function scopePublished($query)
	{
		return $query->where('publish', '=', '1');
	}
}