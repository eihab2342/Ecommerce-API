<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\products;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllOrders()
    {
        if (auth('sanctum')->check()) {
            // استرجاع جميع الطلبات مع تفاصيل العناصر الخاصة بكل طلب
            $orders = Order::with('items')->get(); // باستخدام with لتحميل العلاقات (مثل OrderItems)

            $ordersWithItems = $orders->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'client_name' => $order->name,
                    'client_email' => $order->email,
                    'client_phone' => $order->phone_number,
                    'shipping_address' => $order->shipping_address,
                    'billing_address' => $order->billing_address,
                    'village' => $order->village,
                    'city' => $order->city,
                    'governorate' => $order->governorate,
                    'payment_method' => $order->payment_method,
                    'comments' => $order->comments,
                    'total_price' => $order->total_price,
                    'original_price' => $order->original_price,
                    'status' => $order->status,
                    'ordered_at' => $order->ordered_at,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'order_id' => $item->order_id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total_price' => $item->total_price,
                            'image' => json_decode($item->image),
                        ];
                    }),
                ];
            });

            // إرجاع استجابة JSON
            return response()->json([
                'orders' => $ordersWithItems
            ]);
        }
    }

    public function getOrder($id)
    {
        if (auth('sanctum')->check()) {
            $order = Order::with(['items.product', 'coupon'])->find($id);

            if (!$order) {
                return response()->json(['message' => 'الطلب غير موجود'], 404);
            }

            // السعر الأصلي لكل المنتجات بدون خصومات
            $originalPrice = $order->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // احسب قيمة الخصم (لو موجود كوبون)
            $discountAmount = $originalPrice - $order->total_price;

            // احسب الربح
            $totalProfit = $order->items->sum(function ($item) {
                $product = $item->product;
                if ($product) {
                    $profitPerItem = ($item->price - $product->cost_price) * $item->quantity;
                    return $profitPerItem;
                }
                return 0;
            });

            // اضف القيم الإضافية على الريسبونس
            $order->original_price = $originalPrice;
            $order->discount_amount = $discountAmount;
            $order->coupon_code = optional($order->coupon)->code;
            $order->profit = $totalProfit; // الربح اللي حققناه من الطلب

            return response()->json($order);
        }

        return response()->json(['message' => 'غير مصرح'], 401);
    }


    public function getPendingOrders()
    {
        $orders = Order::where('status', 'pending')->with('items')->get();

        // التأكد من وجود طلبات معلقة
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'لا توجد طلبات معلقة'], 404);
        }

        $ordersWithItems = $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'client_name' => $order->name,
                'client_email' => $order->email,
                'client_phone' => $order->phone_number,
                'shipping_address' => $order->shipping_address,
                'billing_address' => $order->billing_address,
                'village' => $order->village,
                'city' => $order->city,
                'governorate' => $order->governorate,
                'payment_method' => $order->payment_method,
                'comments' => $order->comments,
                'total_price' => $order->total_price,
                'original_price' => $order->original_price,
                'status' => $order->status,
                'ordered_at' => $order->ordered_at,
                'items' => $order->items->map(function ($item) {
                    return [
                        'order_id' => $item->order_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total_price' => $item->total_price,
                        'image' => json_decode($item->image),
                    ];
                }),
            ];
        });

        return response()->json([
            'orders' => $ordersWithItems,
        ]);
    }


    public function getRevenue()
    {
        $revenue = Order::sum('total_price');
        return response()->json(['revenue' => $revenue]);
    }


    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,canceled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'order' => $order
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
    // **************************************************************
    // User Methods

    public function placeOrder(PlaceOrderRequest $request)
    {
        // dd($request->all());
        $validated = $request->validated();

        // الحصول على المستخدم المتصل
        $user = auth('sanctum')->user();

        $totalPrice = 0;
        $originalPrice = 0;
        foreach ($validated['items'] as $item) {
            $product = products::find($item['product_id']);
            // $totalPrice += $product->price * $item['quantity'];
            $originalPrice += $product->old_price * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => $user->id,
            'name' => $validated['clientName'],
            'email' => $validated['clientEmail'], // ← تأكد إنك جبت القيمة من المفتاح الصحيح
            'phone_number' => $validated['clientPhone'],
            'total_price' => $validated['total'],
            'original_price' => $originalPrice,
            'status' => 'pending',
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'],
            'village' => $validated['village'],
            'city' => $validated['city'],
            'governorate' => $validated['governorate'],
            'payment_method' => $validated['payment_method'],
            'comments' => $validated['comments'],
            'ordered_at' => now(),
        ]);

        // إضافة العناصر إلى جدول التفاصيل
        foreach ($validated['items'] as $item) {
            $product = products::find($item['product_id']);

            OrderItems::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                "total_price" => $product->price * $item['quantity'],
                'image' => json_encode($product->images->pluck('images_path')),
            ]);
        }


        $order->user->notify(new OrderCreatedNotification($order));

        return response()->json([
            'message' => 'تم تأكيد الطلب بنجاح!',
            'order' => $order,
        ], 201);
    }
}