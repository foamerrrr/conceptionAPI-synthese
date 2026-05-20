<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class ItemController extends Controller
{
    public function show(Request $request, Item $item)
    {
        $this->ensureItemBelongsToUser($request, $item);

        $itemData = Cache::remember(
            "item.{$item->id}",
            now()->addMinutes(60),
            function () use ($item) {
                return (new ItemResource($item))->resolve();
            }
        );

        return response()->json(['data' => $itemData]);
    }

    public function store(StoreItemRequest $request): ItemResource
    {
        $shoppingList = ShoppingList::firstOrCreate(
            ['user_id' => $request->user()->id],
        );

        $item = $shoppingList->items()->create($request->validated());

        Cache::forget("list.{$shoppingList->id}");

        return new ItemResource($item);
    }

    public function update(UpdateItemRequest $request, Item $item): ItemResource
    {
        $this->ensureItemBelongsToUser($request, $item);

        $item->update($request->validated());

        Cache::forget("item.{$item->id}");
        Cache::forget("list.{$item->shopping_list_id}");

        return new ItemResource($item);
    }

    public function destroy(Request $request, Item $item): Response
    {
        $this->ensureItemBelongsToUser($request, $item);

        Cache::forget("list.{$item->shopping_list_id}");
        Cache::forget("item.{$item->id}");
        $item->delete();

        return response()->noContent();
    }

    private function ensureItemBelongsToUser(Request $request, Item $item): void
    {
        $ownsItem = ShoppingList::query()
            ->where('user_id', $request->user()->id)
            ->whereKey($item->shopping_list_id)
            ->exists();

        if (! $ownsItem) {
            abort(404);
        }
    }
}
