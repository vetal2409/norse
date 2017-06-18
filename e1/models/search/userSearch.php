<?php

namespace e1\models\search;

use e1\models\user;
use e1\traits\modelSearch;
use Illuminate\Validation\Rule;

class userSearch extends user
{
    use modelSearch;

    protected $query;

    protected $defaultValues = [];

    public function search()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $this->query = $this->setTable('users');

        $this->addFilter('first_name', 'where', 'like', "%$this->first_name%");
        $this->addFilter('middle_name', 'where', 'like', "%$this->middle_name%");
        $this->addFilter('last_name', 'where', 'like', "%$this->last_name%");
        $this->addFilter('email', 'where', '=', $this->email);
        $this->addFilter('role', 'where', '=', $this->role);

        return $this->query;
    }


    public function rules(): array
    {
        return [
            'first_name' => 'string|max:255',
            'middle_name' => 'string|max:255',
            'last_name' => 'string|max:255',

            'email' => "email",
            'role' => [Rule::in([self::ROLE_ADMIN, self::ROLE_USER])],

        ];
    }
}