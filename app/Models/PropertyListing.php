<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;

class PropertyListing extends Model
{
    use HasNeighbors;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'property_type',
        'transaction_type',
        'price',
        'bedrooms',
        'bathrooms',
        'parking_spaces',
        'area',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'is_featured',
        'is_active',
        'embedding',
        'conditions',
        'currency',
        'lotsize'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'embedding' => Vector::class
    ];

    /**
     * Get the user that owns the property listing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images for the property listing.
     */
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    /**
     * Get the primary image for the property listing.
     */
    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    /**
     * Scope a query to only include active listings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured listings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
