<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

// ERP key/value settings (branch, currency)
class Setting extends Model
{
    protected $table = 'erp_settings';
    protected $guarded = ['id'];

    // Loaded once per request
    public static function get($key, $default = null){
        if(!app()->bound('erp.settings')){
            app()->instance('erp.settings', static::pluck('value', 'key')->all());
        }
        return app('erp.settings')[$key] ?? $default;
    }

    public static function put($key, $value){
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        app()->forgetInstance('erp.settings');
    }
}
