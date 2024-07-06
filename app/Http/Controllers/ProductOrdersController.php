<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Stock;
use Illuminate\Http\Request;
// use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use DB;

// use Stripe;
// Stripe\Stripe::setApiKey(\config('values.STRIPE_SECRET'));
class ProductOrdersController extends Controller
{
    public function calculateOrderAmount(array $items): int
    {
        $price = 0;
        $checkoutItems = [];
        foreach ($items as $item) {
            if ($item['quantity'] > 0) {
                $checkoutItems[] = ['stock_id' => $item['stock_id'], 'quantity' => $item['quantity']];
            } else {
                abort(500);
            }

        }

        // $user = JWTAuth::parseToken()->authenticate();
        $user = auth()->authenticate();

        $cartList = $user->cartItems()
            ->with('stock.product')
            ->get();
        foreach ($cartList as $cartItem) {
            foreach ($checkoutItems as $checkoutItem) {
                if ($cartItem->stock_id == $checkoutItem['stock_id']) {
                    $price += $cartItem->stock->product->price * $checkoutItem['quantity'];
                }
            }

        }
        return $price * 100;
    }
    public function stripePost(Request $request)
    {
        // try {
        //     $paymentIntent = \Stripe\PaymentIntent::create([
        //         'amount' => $this->calculateOrderAmount($request->toArray()),
        //         'currency' => 'usd',
        //         'description' => "Test payment from bug-busters.localhost",
        //     ]);
        //     $output = [
        //         'clientSecret' => $paymentIntent->client_secret,
        //     ];
        //     echo json_encode($output);
        // } catch (Error $e) {
        //     http_response_code(500);
        //     echo json_encode(['error' => $e->getMessage()]);
        // }

       
    }
    public function store(Request $request)
    {
        // $user = JWTAuth::parseToken()->authenticate();
        $user = auth()->authenticate();
        // $note = $request->note;
        $note = "";
        
        Order::create([
            'user_id' => $user->id,
            'stock_id' => $request->stock_id, 
            'quantity' => $request->quantity,
            'note' => $note,
            'status' => 'completed',
        ]);
        Stock::findOrFail($request->stock_id)->decrement('quantity', $request->quantity);
        $user->cartItems()->where('stock_id', $request->stock_id)->delete();
        
    }

    public function show(Request $request){
        $user = auth()->authenticate();
        $orders = $user->orders()->with('stock.product')->get();
        return $orders;
    }

    public function index(){
        $orders = DB::table('orders')
                ->join('users','users.id', '=','orders.user_id')
                ->join('products','products.id', '=', 'orders.stock_id')
                ->select(DB::raw('users.name, products.category_id, products.name, photo, price, brand, status, orders.created_at'))            
                ->groupBy('users.name', 'products.category_id', 'products.name', 'photo', 'price', 'brand', 'status', 'orders.created_at')
                ->orderBy('orders.created_at','desc')
                ->limit(4)
                ->get();



        return $orders;
    }
    
}