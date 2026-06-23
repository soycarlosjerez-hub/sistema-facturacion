<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sucursal_id',
        'business_type_id',
        'business_instance_id',
        'instance_role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function businessInstance()
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public function instanceRole()
    {
        return $this->belongsTo(InstanceRole::class);
    }

    protected function getAuditableIgnored(): array
    {
        return ['password', 'remember_token', 'updated_at'];
    }
}
