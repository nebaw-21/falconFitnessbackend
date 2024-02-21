<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Create a new order
    public function addOrders(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.name' => 'required',
            'items.*.image' => 'required',
            'items.*.price' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new order
        $order = Order::create([
            'user_id' => $request->input('user_id'),
            'status' => $request->input('status'),
        ]);

        // Convert the items JSON string to an array of objects
        $items = $request->input('items');

        // Create order items  user
        foreach ($items as $item) {
            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
                'color' => $item['color'],
                'size' => $item['size'],
                'image' => $item['image'],
                'price' => $item['price'],
            
                // Set values for other columns in the order_item table if applicable
            ]);
        }

        // Return the created order
        return response()->json(['order' => $order], 200);
    }
    
    public function userOrderHistory($id)
    {
        try {
            // Fetch order details from orders table
            $user = User::findOrFail($id);
            $orderIds = $user->orders->pluck('id');
    
            $orderItemsData = [];
    
            foreach ($orderIds as $orderId) {
                $order = Order::findOrFail($orderId);
    
                $orderStatus = $order->status;
                $orderDate = $order->created_at;
    
                $orderItems = $order->orderItems->toArray();
    
                // Attach orderDate and orderStatus to each orderItem array
                $orderItemsWithInfo = array_map(function ($orderItem) use ($orderDate, $orderStatus) {
                    $orderItem['orderDate'] = $orderDate;
                    $orderItem['orderStatus'] = $orderStatus;
                    return $orderItem;
                }, $orderItems);
    
                $orderItemsData = array_merge($orderItemsData, $orderItemsWithInfo);
            }
    
            // Sort the order items based on their order date (descending)
            usort($orderItemsData, function ($a, $b) {
                return strtotime($b['orderDate']) - strtotime($a['orderDate']);
            });
    
            $orderHistory = [
                'orderItemData' => $orderItemsData,
            ];
    
            return response()->json($orderHistory, 200);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    public function displayAllOrder()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        
        $orderData = [];
    
        foreach ($orders as $order) {
            $orderId = $order->id;
            $orderDate = $order->created_at;
            $status = $order->status;
            $name = $order->User->fname;
            $lname = $order->User->lname;
    
            $orderData[] = [
                'orderId' => $orderId,
                'orderDate' => $orderDate,
                'status' => $status,
                'name' => $name,
                'lname' => $lname,
            ];
        }
    
        return $orderData;
    }
    

    public function orderDetailForAdmin($id)
    {
        $order = Order::find($id);
    
        $orderData = [];
    
        if ($order) {
            $orderItems = $order->orderItems;
    
            $orderItemsData = [];
            $totalPrice = 0; // Variable to store the total price
    
            foreach ($orderItems as $orderItem) {
                $quantity = intval($orderItem->quantity); // Convert quantity to integer
                $name = $orderItem->name;
                $color = $orderItem->color;
                $size = $orderItem->size;
                $image = $orderItem->image;
                $price = floatval($orderItem->price); // Convert price to float
                $payment= $orderItem->payment;
                $totalPrice += $price * $quantity; // Multiply price by quantity and accumulate the total price
    
                $orderItemsData[] = [
                    'quantity' => $quantity,
                    'name' => $name,
                    'color' => $color,
                    'size' => $size,
                    'image' => $image,
                    'price' => $price * $quantity, // Multiply price by quantity for each item
                ];
            }
    
            $orderId = $order->id;
            $orderDate = $order->created_at;
            $status = $order->status;
            $fname = $order->User->fname;
            $lname = $order->User->lname;
            $phone = $order->User->phone;
            $email = $order->User->email;
            $country = $order->User->country;
            $city = $order->User->city;
    
            $orderData = [
                'orderId' => $orderId,
                'orderDate' => $orderDate,
                'status' => $status,
                'fname' => $fname,
                'lname' => $lname,
                'phone' => $phone,
                'email' => $email,
                'country' => $country,
                'city' => $city,
                'orderItems' => $orderItemsData,
                'totalPrice' => $totalPrice, // Include the total price in the response data
                'payment' =>$payment,
            ];
        }
    
        return $orderData;
    }
    

public function SearchOrder($key = null)
{
    $query = Order::query();

    // If $key is provided, filter orders based on the 'fname' or 'lname' of the related user
    if ($key) {
        $query->whereHas('user', function ($q) use ($key) {
            $q->where('fname', 'LIKE', '%' . $key . '%')
              ->orWhere('lname', 'LIKE', '%' . $key . '%');
        });
    }

    $orders = $query->with(['user'])->get();

    $orderData = [];

    foreach ($orders as $order) {
        $orderId = $order->id;
        $orderDate = $order->created_at;
        $status = $order->status;
        $name = $order->user->fname;
        $lname = $order->user->lname;

        $orderData[] = [
            'orderId' => $orderId,
            'orderDate' => $orderDate,
            'status' => $status,
            'name' => $name,
            'lname' => $lname,
        ];
    }

    return $orderData;
}

public function updateOrderStatus(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'order_id' => 'required|exists:orders,id',
        'status' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find the order by its ID
    $order = Order::find($request->input('order_id'));

    // Check if the order exists
    if (!$order) {
        return response()->json(['message' => 'Order not found.'], 404);
    }

    // Update the status of the order
    $order->update(['status' => $request->input('status')]);

    // Return the updated order
    return response()->json(['order' => $order], 200);
}



}