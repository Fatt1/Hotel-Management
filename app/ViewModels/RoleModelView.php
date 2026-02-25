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

  public function getOperationModules(): array
  {
    return Module::groupOperate();
  }

  public function getServiceModules(): array
  {
    return Module::groupService();
  }

  public function getSystemModules(): array
  {
    return Module::groupSystem();
  }

  public function getAssetModules(): array
  {
    return Module::groupAsset();
  }

  public function getCustomerModules(): array
  {
    return Module::groupCustomer();
  }



  public function hasClaim(string $module, string $action): bool
  {
    $value =  ActionType::fromName($action);

    $claimToFind = $module;

    return $this->role->claims->contains(function ($claim) use ($claimToFind, $value) {
      return $claim->claim_type === $claimToFind && ($claim->claim_value & $value) !== 0;
    });
  }
}
