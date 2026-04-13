<?php

namespace App\Observers;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GlobalActivityObserver
{
    /**
     * Handle events after all transactions are committed.
     */
    public $afterCommit = true;

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        if (Auth::check() && $this->shouldLog($model)) {
            $this->logActivity('created', $model, 'Created new ' . $this->getModelName($model));
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if (Auth::check() && $this->shouldLog($model)) {
            $changes = $model->getChanges();
            unset($changes['updated_at']); // Hapus updated_at dari changes
            
            $this->logActivity('updated', $model, 'Updated ' . $this->getModelName($model), [
                'changes' => $changes,
                'original' => $model->getOriginal()
            ]);
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        if (Auth::check() && $this->shouldLog($model)) {
            $this->logActivity('deleted', $model, 'Deleted ' . $this->getModelName($model), [
                'data' => $model->getOriginal()
            ]);
        }
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        if (Auth::check() && $this->shouldLog($model)) {
            $this->logActivity('restored', $model, 'Restored ' . $this->getModelName($model));
        }
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        if (Auth::check() && $this->shouldLog($model)) {
            $this->logActivity('force_deleted', $model, 'Permanently deleted ' . $this->getModelName($model));
        }
    }

    /**
     * Log activity
     */
    private function logActivity(string $action, Model $model, string $description, array $metadata = []): void
    {
        // Batasi metadata yang disimpan
        $limitedMetadata = [
            'model' => class_basename($model),
            'model_id' => $model->getKey(),
        ];
        
        // Tambahkan data spesifik berdasarkan action
        switch ($action) {
            case 'updated':
                $changes = $model->getChanges();
                unset($changes['updated_at']);
                
                if (!empty($changes)) {
                    $limitedMetadata['changed_fields'] = array_keys($changes);
                    // Simpan hanya 2 field pertama untuk hemat space
                    $limitedMetadata['sample_changes'] = array_slice($changes, 0, 2);
                }
                break;
                
            case 'deleted':
                // Simpan hanya beberapa field penting
                $original = $model->getOriginal();
                $importantFields = ['name', 'kode_kategori', 'kode_transaksi', 'title', 'nama'];
                
                foreach ($importantFields as $field) {
                    if (isset($original[$field])) {
                        $limitedMetadata[$field] = Str::limit($original[$field], 50);
                    }
                }
                break;
        }
        
        Activity::create([
            'user_id' => Auth::id(),
            'action' => $action . '_' . $this->getModelType($model),
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $limitedMetadata
        ]);
    }

    /**
     * Get model name for display
     */
    private function getModelName(Model $model): string
    {
        $modelName = class_basename($model);
        
        $names = [
            'User' => 'user profile',
            'Kategori' => 'category',
            'KodeTransaksi' => 'transaction code',
            'MutasiKas' => 'cash mutation',
            'Perencanaan' => 'planning',
            'PerencanaanDetail' => 'planning detail',
        ];
        
        return $names[$modelName] ?? strtolower($modelName);
    }

    /**
     * Get model type for action
     */
    private function getModelType(Model $model): string
    {
        return strtolower(class_basename($model));
    }

    /**
     * Check if should log activity for this model
     */
    private function shouldLog(Model $model): bool
    {
        // Jangan log Activity model sendiri (agar tidak infinite loop)
        if ($model instanceof \App\Models\Activity) {
            return false;
        }
        
        // Jangan log untuk model tertentu jika perlu
        $excludedModels = [
            // Tambahkan model yang tidak perlu di-log di sini
        ];
        
        return !in_array(get_class($model), $excludedModels);
    }

}