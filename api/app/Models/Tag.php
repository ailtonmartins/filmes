<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tag
 * 
 * @property int $id
 * @property string $name
 * @property int $movie
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 *
 * @package App\Models
 */
class Tag extends Model
{
	protected $table = 'tags';

	protected $casts = [
		'movie' => 'int'
	];

	protected $fillable = [
		'name',
		'movie'
	];

	public function movie()
	{
		return $this->belongsTo(Movie::class, 'movie');
	}
}
