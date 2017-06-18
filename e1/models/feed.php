<?php

namespace e1\models;

use Carbon\Carbon;
use e1\Providers\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

/**
 * Class feed
 * @package e1\Models
 *
 * @property string $_id
 * @property string $title
 * @property string $content
 * @property string $link
 * @property string $url
 * @property string $type
 * @property string $channel_ids
 *
 * @property Collection|channel[] $channels
 *
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class feed extends Model
{
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'content',
        'link',

        'url',
        'type',

        'channel_ids'
    ];

    protected $appends = [];

    protected $scenarioRules = [

        self::SCENARIO_CREATE => [
            'title' => 'required',
            'content' => 'required',
            'link' => 'required',

            'url' => 'required',
            'type' => 'required',
            'channel_ids' => 'required',
        ]
    ];

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'link' => 'string|max:255',

            'url' => 'active_url',
            'type' => [Rule::in([channel::TYPE_RSS, channel::TYPE_TWITTER])],

            'channel_ids.*' => 'string|exists:channels,_id',
        ];
    }

    public function custom()
    {

    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            /** @var $model self */

            return true;
        });
    }

    # setters


    # appends

    /**
     * @return \Illuminate\Database\Eloquent\Collection|channel[]
     */
    public function getChannelsAttribute()
    {
        return $this->app()->model('channel')->whereIn('_id', $this->getAttribute('channel_ids', []))->get();
    }
}
