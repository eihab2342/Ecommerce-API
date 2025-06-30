<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Notification;
use App\Interfaces\PaymobServiceInterface;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Package;
use App\Models\PackageImages;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\PendingOrder;

// class PaymentController extends Controller
// {
//     protected $paymentService;

//     // حقن PaymobServiceInterface داخل الكونستركتور
//     public function __construct(PaymobServiceInterface $paymentService)
//     {
//         $this->paymentService = $paymentService;
//     }

//     // 


//     public function processPayment(Request $request)
//     {
//         $validatedData = $request->validate([
//             'user_id' => 'required|integer',
//             'first_name' => 'required|string',
//             'last_name' => 'required|string',
//             'email' => 'required|email',
//             'phone_number' => 'required|string',
//             'payment_method' => 'required|in:COD,visa,m_wallet',
//             'governorate' => 'required|string',
//             'city' => 'required|string',
//             'village' => 'required|string',
//             'shipping_address' => 'required|string',
//             'total_amount' => 'required|numeric|min:1',
//         ]);

//         // جلب المنتجات من السلة
//         $cartItems = Cart::where('user_id', $request->user_id)->with('product')->get();
//         $totalAmount = $cartItems->sum(fn($cartItem) => $cartItem->itemTotal);

//         $finalTotal = session()->get('applied_coupon.newTotal', $totalAmount);

//         if ($request->payment_method === 'COD') {
//             $order = Order::create(array_merge($validatedData, [
//                 'total_amount' => $finalTotal,
//                 'payment_status' => 'unpaid',
//                 'transaction_id' => 000000,
//                 'mid' => 000000,
//                 'payment_method' => 'COD'
//             ]));

//             foreach ($cartItems as $item) {
//                 $item_name = Product::where('id', $item->product_id)->value('name')
//                     ?? Package::where('id', $item->product_id)->value('name');
//                 $item_price = Product::where('id', $item->product_id)->value('price')
//                     ?? Package::where('id', $item->product_id)->value('price');
//                 $item_image = ProductImage::where('product_id', $item->product_id)->value('image')
//                     ?? PackageImages::where('package_id', $item->product_id)->value('image_path');

//                 OrderItems::create([
//                     'order_id' => $order->id,
//                     'product_id' => $item->product_id ?? $item->package_id,
//                     'user_id' => $item->user_id ?? Auth::id(),
//                     // 'product_id'     => $item->product_id,
//                     'product_name' => $item_name,
//                     'type' => $item->type,
//                     'quantity' => $item->quantity,
//                     'price' => $item_price,
//                     'product_image' => $item_image,
//                     'total' => $item->itemTotal,
//                     'created_at' => $item->created_at,
//                 ]);
//             }

//             if (Auth::check()) {
//                 // $user = Auth::user();
//                 $user = User::where('role', 'user')->first();

//                 $user->notify(new OrderNotification($order, 'user'));
//                 Cart::where('user_id', $user->id)->delete();
//                 session()->forget('applied_coupon');
//             }

//             $admin = User::where('role', 'admin')->first();
//             if ($admin) {
//                 $admin->notify(new OrderNotification($order, 'admin'));
//             }

//             return redirect()->route('checkout.success')->with('success', 'تم تقديم طلبك بنجاح!');
//         } else {
//             $customer_data = json_encode($validatedData);
//             $cart_items = json_encode($cartItems);
//             $final_total = json_encode($finalTotal);
//             $order_reference = random_int(100000, 999999);

//             PendingOrder::create([
//                 'order_reference' => $order_reference, // رقم عشوائي بين 100000 و 999999
//                 'user_id' => Auth::id(),
//                 'customer_data' => $customer_data,
//                 'cart_items' => $cart_items,
//                 'final_total' => (float) $final_total,
//             ]);
//         }

//         $orderId = $this->paymentService->createOrder($validatedData);
//         $clientSecret = $this->paymentService->createIntention($order_reference, $validatedData);

//         return redirect("https://accept.paymob.com/unifiedcheckout/?publicKey=" . env('PAYMOB_PUBLIC_KEY') . "&clientSecret=" . $clientSecret);
//     }




//     // public function processPayment(Request $request)
//     // {
//     //     $validatedData = $request->validate([
//     //         'user_id' => 'required|integer',
//     //         'first_name' => 'required|string',
//     //         'last_name' => 'required|string',
//     //         'email' => 'required|email',
//     //         'phone_number' => 'required|string',
//     //         'payment_method' => 'required|in:COD,visa,m_wallet',
//     //         'governorate' => 'required|string',
//     //         'city' => 'required|string',
//     //         'village' => 'required|string',
//     //         'shipping_address' => 'required|string',
//     //         'total_amount' => 'required|numeric|min:1',
//     //     ]);

//     //     // تخزين بيانات العميل في الجلسة
//     //     // جلب المنتجات من السلة
//     //     $cartItems = Cart::where('user_id', $request->user_id)->with('product')->get();
//     //     $totalAmount = $cartItems->sum(function ($cartItem) {
//     //         return $cartItem->itemTotal;
//     //     });

//     //     $finalTotal = session()->get('applied_coupon.newTotal', $totalAmount);

//     //     if ($request->payment_method === 'COD') {
//     //         $order = Order::create(array_merge($validatedData, ['total_amount' => $finalTotal, 'payment_status' => 'unpaid', 'transaction_id' => 000000, 'mid' => 000000, 'payment_method' => 'COD']));
//     //         foreach ($cartItems as $item) {
//     //             OrderItems::create([
//     //                 'order_id' => $order->id,
//     //                 'user_id' => $item->user_id,
//     //                 'product_id' => $item->product_id,
//     //                 'product_name' => $item->product->name,
//     //                 'type' => $item->type,
//     //                 'quantity' => $item->quantity,
//     //                 'price' => $item->product->price,
//     //                 'product_image' => $item->product->images->first()->image,
//     //                 'total' => $item->itemTotal,
//     //             ]);
//     //         }
//     //         return redirect()->route('checkout.success')->with('success', 'تم تقديم طلبك بنجاح!');
//     //     } else {
//     //         $customer_data = json_encode($validatedData);
//     //         $cart_items = json_encode($cartItems);
//     //         $final_total = json_encode($finalTotal);
//     //         $order_reference = random_int(100000, 999999);

//     //         $pendingOrder = PendingOrder::create([
//     //             'order_reference' =>  $order_reference, // رقم عشوائي بين 100000 و 999999
//     //             'user_id'         =>  Auth::id(),
//     //             'customer_data'   =>  $customer_data,
//     //             'cart_items'      =>  $cart_items,
//     //             'final_total'     => (float) $final_total,
//     //         ]);
//     //     }

//     //     $orderId = $this->paymentService->createOrder($validatedData);
//     //     $clientSecret = $this->paymentService->createIntention($order_reference, $validatedData);

//     //     return redirect("https://accept.paymob.com/unifiedcheckout/?publicKey=" . env('PAYMOB_PUBLIC_KEY') . "&clientSecret=" . $clientSecret);
//     // }

//     public function handleWebhook(Request $request)
//     {
//         Log::info('Webhook Received: ', $request->all());

//         if (!$request->boolean('success')) {
//             return response()->json(['error' => 'عملية الدفع لم تنجح'], 400);
//         }

//         $merchant_order_id = $request->input('merchant_order_id');
//         $transactionId = $request->input('id');
//         $payment_method = $request->input('source_data_type');

//         // 
//         $order_data = PendingOrder::where('order_reference', $merchant_order_id)->first();
//         if (!$order_data) {
//             return response()->json(['message' => 'Order not found'], 404);
//         }
//         // 
//         $customer_data = json_decode($order_data->customer_data, true);
//         $cart_items = json_decode($order_data->cart_items, true);

//         // 
//         $customer_name = $customer_data['first_name'] . ' ' . $customer_data['last_name'];
//         $customer_phone_number = $customer_data['phone_number'];
//         $customer_address = $customer_data['shipping_address'] . " - " . $customer_data['village'] . " - " . $customer_data['city'] . " - " . $customer_data['governorate'];
//         $customer_governorate = $customer_data['governorate'];
//         // dd($customer_governorate);
//         //عرض رسال ه للعميل بنجاح المعامله مسبقا في حال حاول ارسال البيانات مرة اخرى
//         $orderExists = Order::where('transaction_id', $transactionId)->exists();
//         if ($orderExists) {
//             session()->flash('success', 'تمت معالجة الطلب مسبقًا بنجاح.');

//             return view('payment-status.success', [
//                 'request' => $request,
//                 'customer_name' => $customer_name, // ✅ تمرير المتغير بشكل صحيح
//                 'customer_phone_number' => $customer_phone_number,
//                 'customer_address' => $customer_address,
//                 'customer_governorate' => $customer_governorate,
//             ]);
//         }

//         $order = Order::create([
//             'user_id' => $customer_data['user_id'],
//             'mid' => $merchant_order_id,
//             'transaction_id' => $transactionId,
//             'first_name' => $customer_data['first_name'],
//             'last_name' => $customer_data['last_name'],
//             'email' => $customer_data['email'],
//             'phone_number' => $customer_data['phone_number'],
//             'total_amount' => $customer_data['total_amount'],
//             'payment_status' => 'paid',
//             'payment_method' => $payment_method,
//             'shipping_address' => $customer_data['shipping_address'],
//             'village' => $customer_data['village'],
//             'city' => $customer_data['city'],
//             'governorate' => $customer_data['governorate'],
//         ]);

//         foreach ($cart_items as $item) {
//             $item_name = Product::where('id', $item['product_id'])->value('name')
//                 ?? Package::where('id', $item['product_id'])->value('name');
//             $item_price = Product::where('id', $item['product_id'])->value('price')
//                 ?? Package::where('id', $item['product_id'])->value('price');
//             $item_image = ProductImage::where('product_id', $item['product_id'])->value('image')
//                 ?? PackageImages::where('package_id', $item['product_id'])->value('image_path');

//             OrderItems::create([
//                 'order_id' => $order->id,
//                 'product_id' => $item['product_id'] ?? $item['package_id'],
//                 'user_id' => $item['user_id'] ?? Auth::id(),
//                 // 'product_id'     => $item['product_id'],
//                 'product_name' => $item_name,
//                 'type' => $item['type'],
//                 'quantity' => $item['quantity'],
//                 'price' => $item_price,
//                 'product_image' => $item_image,
//                 'total' => $item['itemTotal'],
//                 'created_at' => $item['created_at'],
//             ]);
//         }

//         if (Auth::check()) {
//             // $user = Auth::user();
//             $user = User::where('role', 'user')->first();
//             $user->notify(new OrderNotification($order, 'user'));
//             Cart::where('user_id', $user->id)->delete();
//             session()->forget('applied_coupon');
//         }

//         $admin = User::where('role', 'admin')->first();
//         if ($admin) {
//             $admin->notify(new OrderNotification($order, 'admin'));
//         }

//         return view('payment-status.success', [
//             'request' => $request,
//             'customer_name' => $customer_data['first_name'] . ' ' . $customer_data['last_name'],
//             'customer_phone_number' => $customer_data['phone_number'],
//             'customer_address' => $customer_data['shipping_address'] . " - " . $customer_data['village'] . " - " . $customer_data['city'] . " - " . $customer_data['governorate'],
//         ]);
//     }

//     // public function handleResponse(Request $request)
//     // {
//     //     return view('user.checkout-success');
//     // }
// }