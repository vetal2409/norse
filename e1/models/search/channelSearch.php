<?php

namespace e1\models\search;

use e1\models\channel;
use e1\traits\modelSearch;
use Illuminate\Validation\Rule;

class channelSearch extends channel
{
    use modelSearch;

    protected $query;

    protected $defaultValues = [];

    public function search()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $this->query = $this->setTable('channels');

        $this->addFilter('name', 'where', 'like', "%$this->name%");
        $this->addFilter('type', 'where', '=', $this->type);
        $this->addFilter('url', 'where', '=', $this->url);

        return $this->query;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'type' => [Rule::in([self::TYPE_RSS, self::TYPE_TWITTER])],
            'url' => 'active_url',
        ];
    }

}