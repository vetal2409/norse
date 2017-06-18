<?php

namespace e1\models;

use Carbon\Carbon;
use e1\Providers\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

/**
 * Class user
 * @package e1\Models
 *
 * @property string $_id
 * @property string $name
 * @property string $type
 * @property string $url
 *
 * @property Collection|user[] $feeds
 *
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class channel extends Model
{

    const TYPE_RSS = 'rss';
    const TYPE_TWITTER = 'twitter';

    protected $restriction = [
        user::ROLE_USER
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'type',
        'url',
    ];

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in([self::TYPE_RSS, self::TYPE_TWITTER])],
            'url' => 'required|active_url',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            /** @var $model self */

            # todo: DO FOR TWITTER
            $model->app('service.channel')->addFeed($model);

            return true;
        });

        static::deleting(function ($model) {
            /** @var $model self */

            return true;
        });
    }

    # appends

    public function getFeedsAttribute()
    {
        return $this->app()->model('feeds')->where('channel_id', '=', $this->getKey())->get();
    }
}
