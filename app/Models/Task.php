<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status'];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
        ];
    }

    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if ($status && TaskStatus::tryFrom($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);
            $like = '%' . $escaped . '%';

            $query->whereRaw('title LIKE ? ESCAPE ?', [$like, '\\']);
        }

        return $query;
    }

    public function formattedId(): string
    {
        return '#' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
    }

    public function excerpt(int $limit = 150): ?string
    {
        $description = trim((string) $this->description);

        if ($description === '') {
            return null;
        }

        return Str::limit(preg_replace('/\s+/u', ' ', $description) ?? $description, $limit);
    }
}
