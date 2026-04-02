<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        if ($term) {
            $query->where('title', 'like', '%' . $term . '%');
        }

        return $query;
    }
}
