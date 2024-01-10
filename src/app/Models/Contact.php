<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    public function easternOrderedName(): string
    {
        return $this->last_name . '　' . $this->first_name;
    }

    public function genderName(): string
    {
        return match ($this->gender) {
            1 => '男性',
            2 => '女性',
            3 => 'その他',
        };
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePartialMatch(Builder $query, string $keyword): void
    {
        $pattern = '%' . addcslashes($keyword, '%_\\') . '%';
        $query
            ->where('first_name', 'like', $pattern)
            ->orWhere('last_name', 'like', $pattern)
            ->orWhere('email', 'like', $pattern);
    }

    public function scopeExactMatch(Builder $query, string $keyword): void
    {
        $pattern = addcslashes($keyword, '%_\\');
        $query
            ->where('first_name', 'like', $pattern)
            ->orWhere('last_name', 'like', $pattern)
            ->orWhere('email', 'like', $pattern);
    }

    public function scopeSearchByKeywords(Builder $query, ?string $keywordsString): void
    {
        if (is_null($keywordsString)) return;
        $keywords = preg_split('/\p{Z}+/u', $keywordsString, flags: PREG_SPLIT_NO_EMPTY);
        foreach ($keywords as $keyword) {
            if (preg_match('/^\\[([^\\]]*)\\]$/', $keyword, $matches)) {
                $query->exactMatch($matches[1]);
            } else {
                $query->partialMatch($keyword);
            }
        }
    }

    public function scopeSearchByGender(Builder $query, ?int $gender): void
    {
        if (is_null($gender)) return;
        $query->where('gender', $gender);
    }

    public function scopeSearchByCategory(Builder $query, ?int $categoryId): void
    {
        if (is_null($categoryId)) return;
        $query->where('category_id', $categoryId);
    }

    public function scopeSearchByDate(Builder $query, ?DateTimeInterface $datetime): void
    {
        if (is_null($datetime)) return;
        $query->whereDate('created_at', $datetime->format('Y-m-d'));
    }
}
