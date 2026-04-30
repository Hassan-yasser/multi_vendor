<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubSubCategory extends Model
{
    use HasFactory;

    protected $table = 'sub_sub_categories';

    protected $fillable = [
        'sub_category_id',
        'name',
        'slug',
        'description',
        'image',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * @return BelongsTo<SubCategory, $this>
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
}
