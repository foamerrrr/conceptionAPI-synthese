<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShoppingListController extends Controller
{
    public function show(Request $request)
    {
        $shoppingList = ShoppingList::query()
            ->where('user_id', $request->user()->id)
            ->select(['id', 'created_at', 'updated_at'])
            ->with(['items' => fn ($query) => $query->select(['id', 'name', 'shopping_list_id'])])
            ->firstOrFail();

        $listData = Cache::remember(
            "list.{$shoppingList->id}",
            now()->addMinutes(60),
            function () use ($shoppingList) {
                return (new ShoppingListResource($shoppingList))->resolve();
            }
        );

        return response()->json(['data' => $listData]);
    }
}
