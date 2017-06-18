<?php

namespace e1\models\search;

use e1\models\channel;
use e1\models\feed;
use e1\traits\modelSearch;
use Illuminate\Validation\Rule;

class feedSearch extends feed
{
    use modelSearch;

    protected $query;

    protected $defaultValues = [];

    public function search()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $this->query = $this->setTable('feeds');

        $this->addFilter('title', 'where', 'like', "%$this->title%");
        $this->addFilter('content', 'where', 'like', "%$this->content%");
        $this->addFilter('link', 'where', '=', $this->link);
        $this->addFilter('type', 'where', '=', $this->type);
        $this->addFilter('url', 'where', '=', $this->url);

        $this->addFilter('channel_ids', 'whereIn', $this->channel_ids);

        return $this->query;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'link' => 'string|max:255',

            'url' => 'active_url',
            'type' => [Rule::in([channel::TYPE_RSS, channel::TYPE_TWITTER])],

            'channel_ids.*' => 'string',
        ];
    }
}