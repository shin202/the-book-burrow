<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Requests\Coupon\ApplyCouponRequest;
use App\Http\Resources\Cart\CartResource;
use App\Http\Resources\Coupon\CouponResource;
use App\Models\Book;
use App\Services\BookService;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(CouponService $couponService, BookService $bookService)
    {
        $cart = CartResource::make(CartService::instance());
        $availableCoupons = $couponService->availables();

        $relatedBooks = null;
        if ($cart->items->isNotEmpty()) {
            $relatedBooks = $bookService->related($cart->items->first()?->model->slug);
        }

        return Inertia::render('Landing/Cart/Index', [
            'isAuthenticated' => Route::has('login') && auth()->check(),
            'cart' => $cart,
            'availableCoupons' => CouponResource::collection($availableCoupons),
            'relatedBooks' => $relatedBooks,
        ]);
    }

    public function store(StoreCartItemRequest $request)
    {
        $cartItem = CartService::instance()->add(
            $request->validated('product_id'),
            quantity: $request->validated('quantity'),
        )->associate(Book::class);

        $cartItem->name = $cartItem->model->title;
    }

    public function update(UpdateCartItemRequest $request, string $id)
    {
        CartService::instance()->update($id, $request->validated('quantity'));
    }

    public function clear()
    {
        CartService::instance()->destroy();
    }

    public function destroy(string $id)
    {
        CartService::instance()->remove($id);
    }

    public function applyCoupon(ApplyCouponRequest $request)
    {
        $request->fulfill();

        return redirect()->back();
    }

    public function destroyCoupon()
    {
        $coupon = Session::pull('coupons');
        CartService::instance()->clearDiscount($coupon['cartItemId']);
    }
}
