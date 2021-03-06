<?php

namespace Flashtag\Data;

use Illuminate\Database\Eloquent\Model;
use Storage;

/**
 * Class Category
 *
 * @property int $id
 * @property \Illuminate\Database\Eloquent\Collection $tags
 * @property \Flashtag\Data\Media $media
 */
class Category extends Model
{
    /**
     * Fields protected from mass-assignment.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function fields()
    {
        return $this->morphToMany(Field::class, 'fieldable')
            ->withPivot('value');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function media()
    {
        return $this->morphOne(Media::class, 'media_attachable');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function addImage($image)
    {
        $this->removeImage();
        $name = 'category__'.$this->id.'__'.$this->slug.'.'.$this->imageExtension($image);
        $image->move(public_path('images/media'), $name);

        // TODO: generate thumbnails

        $this->updateMedia('image', $name);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image
     * @return string|null
     */
    private function imageExtension($image)
    {
        $parts = explode('.', $image->getClientOriginalName());

        return array_pop($parts);
    }

    /**
     * Remove an image and delete it.
     */
    public function removeImage()
    {
        if ($this->media && $this->media->type == 'image' && $this->media->url) {
            $img = '/public/images/media/' . $this->media->url;

            if (is_file(base_path($img))) {
                Storage::delete($img);
            }
        }

        $this->updateMedia();
    }

    /**
     * @param string $type
     * @param string $url
     */
    public function updateMedia($type = null, $url = null)
    {
        $media = $this->media ?: new Media();

        $media->type = $type;
        $media->url = $url;

        $this->media()->save($media);
    }

    /**
     * @return bool
     */
    public function hasMedia()
    {
        $this->media;

        return !empty($this->media) && !empty($this->media->type);
    }

    /**
     * @param string $category_slug
     * @return Category
     */
    public static function getBySlug($category_slug)
    {
        return static::where('slug', $category_slug)->firstOrFail();
    }
}
