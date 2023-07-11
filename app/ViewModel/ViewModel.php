<?php

namespace App\ViewModel;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
abstract class ViewModel implements Arrayable
{
    /**
     * データをキーバリューのarrayに変換します
     * @return array<string, mixed>
     */
    abstract public function toMap(): array;

    /**
     * 再帰的にarrayに変換します
     * @return array<string, mixed>
     */
    final public function toArray(): array
    {
        $result = $this->toMap();
        foreach ($result as $key => $value) {
            if ($value instanceof Arrayable) {
                $result[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $result[$key] = collect($value)->toArray();
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
