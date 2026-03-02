<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchLog extends Model
{
    protected $table = "branch_logs";
    protected $fillable = [
         'branch_id', 'admin_id', 'action', 'module', 'reference_id', 'old_data', 'new_data', 'ip_address', 'user_agent'
    ];
}
