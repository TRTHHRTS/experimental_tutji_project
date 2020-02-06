<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserDetails
 *
 * @property int $id
 * @property int $user_id
 * @property int $gender
 * @property string|null $age
 * @property string|null $birthday
 * @property string|null $photo_url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails wherePhotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserDetails query()
 */
class UserDetails extends Model
{
    protected $table = 'user_details';

    public $timestamps = false;

    protected $fillable = [
        'gender', 'photo_url', 'birthday'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
