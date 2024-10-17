<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'priority',
        'due_date',
        'assigned_to',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function statusUpdates(): HasMany
    {
        return $this->hasMany(TaskStatusUpdate::class);
    }

    /**
     * Accessor for the 'due_date' attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getDueDateAttribute($value)
    {
        // Convert the stored value to a formatted string
        return $value ? Carbon::parse($value)->format('d-m-Y H:i') : null;
    }

    /**
     * Mutator for the 'due_date' attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setDueDateAttribute($value)
    {
        // Convert the input value to the format used in the database
        $this->attributes['due_date'] = Carbon::createFromFormat('d-m-Y H:i', $value)->format('Y-m-d H:i');
    }


    /**
     * Scope a query to filter tasks by priority.
     * @param mixed $query
     * @param mixed $priority
     * @return mixed
     */
    public function scopePriority($query, $priority)
    {
        if (!is_null($priority) && $priority !== '') {
            return $query->where('priority', $priority);
        }

        return $query;
    }

    /**
     * Scope a query to filter tasks by status.
     * @param mixed $query
     * @param mixed $status
     * @return mixed
     */
    public function scopeStatus($query, $status)
    {
        if (!is_null($status) && $status !== '') {
            return $query->where('status', $status);
        }

        return $query;
    }
}
