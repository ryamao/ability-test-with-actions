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

    /** 名前を「姓」「名」の順番で文字列化する。 */
    public function easternOrderedName(): string
    {
        return $this->last_name . '　' . $this->first_name;
    }

    /** 性別を文字列で返す。 */
    public function genderName(): string
    {
        return match ($this->gender) {
            1 => '男性',
            2 => '女性',
            3 => 'その他',
        };
    }

    /** 関連する Category モデルを返す。 */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** 「姓」「名」「メールアドレス」を部分一致検索する。 */
    public function scopePartialMatch(Builder $query, string $keyword): void
    {
        $pattern = '%' . addcslashes($keyword, '%_\\') . '%';
        $query
            ->where('first_name', 'like', $pattern)
            ->orWhere('last_name', 'like', $pattern)
            ->orWhere('email', 'like', $pattern);
    }

    /** 「姓」「名」「メールアドレス」を完全一致検索する。 */
    public function scopeExactMatch(Builder $query, string $keyword): void
    {
        $pattern = addcslashes($keyword, '%_\\');
        $query
            ->where('first_name', 'like', $pattern)
            ->orWhere('last_name', 'like', $pattern)
            ->orWhere('email', 'like', $pattern);
    }

    /**
     * 検索文字列で「姓」「名」「メールアドレス」を検索する。
     * 検索の仕様については README.md を見てください。
     */
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

    /** 性別で検索する。 */
    public function scopeSearchByGender(Builder $query, ?int $gender): void
    {
        if (is_null($gender)) return;
        $query->where('gender', $gender);
    }

    /** お問い合わせの種類で検索する。 */
    public function scopeSearchByCategory(Builder $query, ?int $categoryId): void
    {
        if (is_null($categoryId)) return;
        $query->where('category_id', $categoryId);
    }

    /** お問い合わせの年月日で検索する。 */
    public function scopeSearchByDate(Builder $query, ?DateTimeInterface $datetime): void
    {
        if (is_null($datetime)) return;
        $query->whereDate('created_at', $datetime->format('Y-m-d'));
    }
}
