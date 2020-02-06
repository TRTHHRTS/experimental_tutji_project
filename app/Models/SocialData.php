<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SocialData
 *
 * @property int $id
 * @property string $id_user
 * @property string|null $id_fb
 * @property string|null $id_google
 * @property string|null $id_vk
 * @property string|null $last_login_via
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereIdFb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereIdGoogle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereIdVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereLastLoginVia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SocialData query()
 */
class SocialData extends Model
{
    protected $table = 'social_data';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'social_id');
    }

}