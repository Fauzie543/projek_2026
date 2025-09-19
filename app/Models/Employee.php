<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','nik','phone','address','hire_date','salary_monthly','role_id', 'position'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function attendances() {
        return $this->hasMany(Attendance::class);
    }
    
    public function role() {
        return $this->belongsTo(Role::class);
    }
}