<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ReindexJob extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'reindex_jobs';
    protected $fillable = ['id','status','recreate','error','started_at','finished_at'];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->id)) {
                $m->id = (string) Str::uuid();
            }
        });
    }
}
