<?php namespace App\Models\Traits;

trait SoftDeletesTrait
{
    /**
     * Filter data tanpa yang terhapus (soft delete)
     */
    public function withoutSoftDeletes()
    {
        return $this->where($this->table . '.deleted_at', null);
    }

    /**
     * Filter hanya data yang terhapus
     */
    public function onlyDeleted()
    {
        return $this->where($this->table . '.deleted_at IS NOT NULL');
    }

    /**
     * Include data yang terhapus
     */
    public function withDeleted()
    {
        return $this;
    }
}