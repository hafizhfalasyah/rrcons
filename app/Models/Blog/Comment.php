<?php

namespace App\Models\Blog;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    // Status moderasi: hanya "approved" yang tampil di website
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_SPAM = 'spam';
    const STATUSES = [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_SPAM];

    // status & moderator tidak bisa diisi dari form publik
    protected $fillable = ['article_id', 'name', 'email', 'content'];

    // komentar baru selalu menunggu persetujuan
    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Ubah status moderasi dan catat siapa serta kapan.
     */
    public function moderate(string $status, User $moderator): void
    {
        $this->forceFill([
            'status' => $status,
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
        ])->save();
    }
}
