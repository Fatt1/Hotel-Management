<?php
namespace App\Enums;
enum ActionType: int{
  case VIEW = 1;
  case CREATE = 2;
  case EDIT = 4;
  case DELETE = 8;

public static function sum(array $actions): int
 {
  $sum = 0;
  foreach($actions as $action) {
   $sum |= $action->value;
  }
  return  $sum;
 }

  public function label(): string {
        return match($this) {
            self::VIEW => 'XEM',
            self::CREATE => 'THÊM',
            self::EDIT => 'SỬA',
            self::DELETE => 'XÓA',
        };
  } 

 public static function fromName(string $actionName): ?int
 {
   foreach(self::cases() as $action) {
      if(strtolower($actionName) === strtolower($action->name)) {
        return $action->value;
      }
   }
  return null;
 }
}