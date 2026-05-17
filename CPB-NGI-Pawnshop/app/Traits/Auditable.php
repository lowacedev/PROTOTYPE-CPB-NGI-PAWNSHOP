<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the trait
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::createAuditLog('create', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $oldValues = $model->getOriginal();
            $newValues = $model->getAttributes();
            $changes = [];

            foreach ($newValues as $key => $value) {
                if (isset($oldValues[$key]) && $oldValues[$key] != $value) {
                    $changes[$key] = [
                        'old' => $oldValues[$key],
                        'new' => $value
                    ];
                }
            }

            if (!empty($changes)) {
                self::createAuditLog('update', $model, $oldValues, $newValues, $changes);
            }
        });

        static::deleted(function ($model) {
            self::createAuditLog('delete', $model, $model->getOriginal(), null);
        });
    }

    /**
     * Create audit log entry
     */
    private static function createAuditLog($action, $model, $oldValues = null, $newValues = null, $changes = null)
    {
        try {
            $modelName = class_basename($model);
            $id = $model->id ?? 'unknown';
            
            $description = match($action) {
                'create' => "Created {$modelName} #{$id}",
                'update' => "Updated {$modelName} #{$id}",
                'delete' => "Deleted {$modelName} #{$id}",
                default => "Performed {$action} on {$modelName} #{$id}"
            };

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => $modelName,
                'model_id' => $model->id,
                'changes' => $changes,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log: ' . $e->getMessage());
        }
    }
}
