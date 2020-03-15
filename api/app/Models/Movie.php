<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Movie
 * 
 * @property int $id
 * @property string $name
 * @property string $file
 * @property int $file_size
 * @property int $user
 * @property string $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property Collection|Tag[] $tags
 *
 * @package App\Models
 */
class Movie extends Model
{
	use SoftDeletes;
	protected $table = 'movies';

	protected $casts = [
		'file_size' => 'int',
		'user' => 'int'
	];

	protected $fillable = [
		'name',
		'file',
		'file_size',
		'user'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user');
	}

	public function tags()
	{
		return $this->hasMany(Tag::class, 'movie');
    }
    
    public function getFileAttribute($value)
	{
		if ($value) {
			return  asset('/movie/' . $value);
		} else {
			return '';
		}
    }
    
    public function getFileSizeAttribute($bytes)
	{
		if ($bytes) {
			if ($bytes >= 1073741824)
            {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            }
            elseif ($bytes >= 1048576)
            {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            }
            elseif ($bytes >= 1024)
            {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            }
            elseif ($bytes > 1)
            {
                $bytes = $bytes . ' bytes';
            }
            elseif ($bytes == 1)
            {
                $bytes = $bytes . ' byte';
            }
            else
            {
                $bytes = '0 bytes';
            }
            return $bytes;
		} else {
			return '';
		}
	}
}
