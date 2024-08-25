<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author',
        'isbn',
        'category_id',
        'quantity',
        'available_quantity',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            $book->slug = static::generateSlug($book->title);
        });

        static::updating(function ($book) {
            if ($book->isDirty('title')) {
                $book->slug = static::generateSlug($book->title, $book->id);
            }
        });
    }

    protected static function generateSlug($title, $id = 0)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;

        // Check if slug exists and increment it
        $count = static::where('slug', 'LIKE', "$slug%")
            ->where('id', '!=', $id)
            ->count();

        return $count ? "{$originalSlug}-{$count}" : $slug;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the borrowings for the book.
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Get the reservations for the book.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope a query to only include books of a given category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $categoryName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfCategory($query, $categoryName)
    {
        return $query->whereHas('category', function ($q) use ($categoryName) {
            $q->where('name', $categoryName);
        });
    }

    /**
     * Scope a query to only include books with a specific author.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $authorName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfAuthor($query, $authorName)
    {
        return $query->where('author', 'LIKE', "%{$authorName}%");
    }
}
