<?php

namespace TwenyCode\LaravelBlueprint\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && !$model->isDirty('slug')) {
                $model->generateSlug();
            }
        });
    }

    /**
     * Generate slug from name
     */
    public function generateSlug(): void
    {
        $base = $this->name ?? $this->getAttribute('name');

        if (!$base) {
            return;
        }

        $slug = Str::slug($base);
        $this->slug = $slug;

        // Ensure uniqueness within the appropriate scope
        $this->ensureUniqueSlug();
    }

    /**
     * Ensure slug is unique within module or global scope
     */
    protected function ensureUniqueSlug(): void
    {
        $originalSlug = $this->slug;
        $counter = 1;

        while ($this->slugExists($this->slug)) {
            $this->slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
    }

    /**
     * Check if slug already exists
     * Takes into account global scopes automatically
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where('slug', $slug);

        // Exclude current model if updating
        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        // Global scopes (like module filtering) are applied automatically
        return $query->exists();
    }

    /**
     * Manually set slug (overrides auto-generation)
     */
    public function setCustomSlug(string $slug): self
    {
        $this->slug = Str::slug($slug);
        $this->ensureUniqueSlug();
        return $this;
    }


}
