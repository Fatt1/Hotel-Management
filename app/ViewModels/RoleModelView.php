<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Enums\ActionType;
use App\Enums\Module;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class RoleModelView
{
  private Role $role;

  public function __construct(Role $role)
  {
    $this->role = $role;
  }

  public function getRole(): Role
  {
    return $this->role;
  }
  public function roleClaim()
  {
    return $this->role->claims;
  }

  public function hasClaim(string $module, string $action): bool
  {
    $value =  ActionType::fromName($action);
    $matchedClaim = $this->role->claims->where('claim_name', $module)->first();
    if (!$matchedClaim) {
      return false;
    }
    $claimValueDb = (int) $matchedClaim->claim_value;
    $hasPermission = ($claimValueDb & $value) !== 0;
    return $hasPermission;
  }
}
