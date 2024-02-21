<?php

namespace App\Http\Controllers;

use Chapa\Chapa\Facades\Chapa as Chapa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Order;

class ChapaController extends Controller
{
    /**
     * Initialize Rave payment process
     * @return void
     */
    protected $reference;

    public function __construct(){
        $this->reference = Chapa::generateReference();

    }
    public function initialize(Request $request)
    {
        //This generates a payment reference
        $reference = $this->reference;
        // Validate the request data
        $validator = Validator::make($request->all(), [
        
            'firstName' => 'required',
            'lastName' =>'required',
            'totalAmount' =>'required',
            'email' =>'required',
  
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Enter the details of the payment
        $data = [
            
            'amount' => $request->input('totalAmount'),
            'email' =>  $request->input('email'),
            'tx_ref' => $reference,
            'currency' => "ETB",
            'callback_url' => route('callback',[$reference]),
            'first_name' =>  $request->input('firstName'),
            'last_name' =>  $request->input('lastName'),
            "customization" => [
                "title" => 'Chapa  Test',
                "description" => "I am testing this"
            ]
        ];
        

        $payment = Chapa::initializePayment($data);


        if ($payment['status'] !== 'success') {
            // notify something went wrong
            
            return response()->json(['error' => 'Failed to initialize payment'], 500);;
        }else{
            // Create a new order
        $order = Order::create([
            'user_id' => $request->input('user_id'),
            'status' => $request->input('status'),
        ]);

        // Convert the items JSON string to an array of objects
        $items = $request->input('items');

        // Create order items
        foreach ($items as $item) {
            $order->OrderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
                'color' => $item['color'],
                'size' => $item['size'],
                'image' => $item['image'],
                'price' => $item['price'],
                'payment'=> 'paid',
            
                // Set values for other columns in the order_item table if applicable
            ]);
        }

        return response()->json(['chapaPaymentUrl' => $payment['data']['checkout_url']]);
        }


    }

    /**
     * Obtain Rave callback information
     * @return void
     */
    public function callback($reference)
    {
        
        $data = Chapa::verifyTransaction($reference);
        dd($data);

        //if payment is successful
        if ($data['status'] ==  'success') {
        
        dd($data);
        }

        else{
            //oopsie something ain't right.
        }


    }

    // public function callback($reference)
    // {
    //     $data = Chapa::verifyTransaction($reference);
    
    //     // Check if payment is successful
    //     if ($data['status'] == 'success') {
    //         return response()->json(['status' => 'success', 'data' => $data]);
    //     } else {
    //         return response()->json(['status' => 'error', 'message' => 'Payment verification failed']);
    //     }
    // }
}
