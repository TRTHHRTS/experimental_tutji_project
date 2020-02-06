<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $sender_id
 * @property int $rcpt_id
 * @property string $message
 * @property int $readed
 * @property int $hidden
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $rcpt
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereRcptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereReaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message query()
 */
class Message extends Model
{
    protected $fillable = [
    ];

    protected $casts = [
    ];

    public function sender() {
        return $this->belongsTo('App\Models\User', 'sender_id');
    }

    public function rcpt() {
        return $this->belongsTo('App\Models\User', 'rcpt_id');
    }
}
