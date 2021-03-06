<?php

namespace Flashtag\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PostList
 *
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Illuminate\Database\Eloquent\Collection $posts
 */
class PostList extends Model
{
    protected $guarded = ['id', 'updated_at', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_post_list')->withPivot('order');
    }
}
