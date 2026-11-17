<?php

namespace App\Services;

use App\Jobs\SendBackInStockNotificationJob;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private CacheService $cache
    ) {}
    private const MAX_IMAGES = 5;

    public function create(array $data): Product
    {
        $imageFiles = $data['images'] ?? null;
        unset($data['images']);
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = ($data['status'] ?? '') === 'active';
        $data['featured'] = (bool) ($data['featured'] ?? false);
        $data['in_stock'] = (bool) ($data['in_stock'] ?? true);
        $data['is_on_sale'] = (bool) ($data['is_on_sale'] ?? false);
        $data['stock'] = (int) ($data['stock'] ?? 0);

        $product = Product::create($data);

        if (! empty($imageFiles) && is_array($imageFiles)) {
            $this->storeImages($product, $imageFiles);
        }

        $this->cache->invalidateProductRelated();
        return $product;
    }

    public function update(Product $product, array $data): void
    {
        $wasOutOfStock = $product->stock <= 0 || $product->in_stock === false;

        $imageFiles = $data['images'] ?? null;
        unset($data['images']);
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = ($data['status'] ?? '') === 'active';
        $data['featured'] = (bool) ($data['featured'] ?? false);
        $data['in_stock'] = (bool) ($data['in_stock'] ?? true);
        $data['is_on_sale'] = (bool) ($data['is_on_sale'] ?? false);
        $data['stock'] = (int) ($data['stock'] ?? 0);

        $product->update($data);

        $isNowInStock = $product->stock > 0 && $product->in_stock;
        if ($wasOutOfStock && $isNowInStock) {
            $this->sendBackInStockNotifications($product);
        }

        if (! empty($imageFiles) && is_array($imageFiles)) {
            $currentCount = $product->images()->count();
            if ($currentCount + count($imageFiles) > self::MAX_IMAGES) {
                throw new \InvalidArgumentException(
                    'A product may have at most ' . self::MAX_IMAGES . ' images. You have ' . $currentCount . '; you can add at most ' . (self::MAX_IMAGES - $currentCount) . ' more.'
                );
            }
            $this->storeImages($product, $imageFiles);
        }

        $this->cache->invalidateProductRelated();
    }

    public function delete(Product $product): void
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $product->delete();
        $this->cache->invalidateProductRelated();
    }

    public function canAddMoreImages(Product $product, int $newCount): bool
    {
        return $product->images()->count() + $newCount <= self::MAX_IMAGES;
    }

    /**
     * @param  array<int, UploadedFile>  $files
     */
    private function storeImages(Product $product, array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $path = $file->store('products', 'public');
            $product->images()->create(['image_path' => $path]);
        }
    }

    private function sendBackInStockNotifications(Product $product): void
    {
        $product->stockNotifications()
            ->whereNull('notified_at')
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(100, function ($notifications) use ($product) {
                foreach ($notifications as $notification) {
                    SendBackInStockNotificationJob::dispatch($product->id, $notification->id);
                }
            });

        Log::info('Queued back-in-stock notifications', ['product_id' => $product->id]);
    }
}
