<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;
    use HasFactory;
    public $timestamps = false;

    protected $casts =[
        'imported_t' => 'datetime',
        'created_t' => 'timestamp',
        'last_modified_t' => 'timestamp',
    ];
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }
    protected $fillable = [
        'code',
        'status',
        'imported_t',
        'url',
        'creator',
        'created_t',
        'last_modified_t',
        'product_name',
        'quantity',
        'brands',
        'categories',
        'labels',
        'cities',
        'purchase_places',
        'stores',
        'ingredients_text',
        'traces',
        'serving_size',
        'serving_quantity',
        'nutriscore_score',
        'nutriscore_grade',
        'main_category',
        'image_url',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return array_merge($this->toArray(),[
            'id' => (string) $this->id,
            'imported_t' => $this->imported_t->timestamp,
        ]);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->last_modified_t = now();
        });
    }
}
