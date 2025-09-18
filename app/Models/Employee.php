<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','nik','phone','address','hire_date','salary_monthly','role'];

    public function user() { return $this->belongsTo(User::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
}