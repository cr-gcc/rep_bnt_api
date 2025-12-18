<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    /**
     * The guard name for this role
     */
    protected $guard_name = 'api';

    /**
     * Define the many-to-many relationship with campaigns
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(
            Campaign::class,
            'role_has_campaigns',
            'role_id',
            'campaign_id'
        );
    }
}
