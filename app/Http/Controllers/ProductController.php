<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProduct;
use App\Models\Product;
use App\Models\Stock;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    public function index(Request $request)
    {
        // $request = Request;
        $perPage = 58;
        if ($request->perPage){
            $perPage = $request->perPage;
        }
        
        
        return Product::with('category', 'stocks')
            ->orderBy('created_at','desc')
            ->paginate($perPage);
    }

    public function productByCategory($category){
        return Product::with('category', 'stocks')
            ->where('category_id', $category)
            ->orderBy('created_at','desc')
            ->paginate(4);
    }
    
    public function newestProduct(){
        return Product::with('category', 'stocks')            
            ->orderBy('created_at','desc')
            ->paginate(4);
    }

    public function topSelling()
    {                        
            
        $products = DB::table('products')
                ->join('orders','orders.stock_id', '=','products.id')
                ->join('categories','categories.id', '=', 'products.category_id')
                ->select(DB::raw('products.id, products.name, products.category_id, categories.name as category_name, photo, price, SUM(quantity) as total_quantity'))
                ->where('orders.status','=','completed')
                ->groupBy('products.id','products.name', 'products.category_id','categories.name','photo', 'price')
                ->orderBy('total_quantity','desc')
                ->limit(4)
                ->get();

                
        return $products;       
        
    }

    public function show($id)
    {
        $product = Product::with('category', 'stocks')->findOrFail($id);
        if ($product->reviews()->exists()) {
            $product['review'] = $product->reviews()->avg('rating');
            $product['num_reviews'] = $product->reviews()->count();
        }
        return $product;
    }

    public function store(StoreProduct $request)
    {
        // if ($user = auth()->authenticate()) {
        //     $validator = $request->validated();
        //     $data = ["shop03.png"];
        //     // $data = null;
        //     if ($request->hasFile('photos')) {
        //         foreach ($request->file('photos') as $photo) {
        //             $name = time() . '_' . $photo->getClientOriginalName();
        //             $photo->move('img', $name);
        //             $data[] = $name;
        //         }
        //     }
        //     $product = Product::create([
        //         'user_id' => $user->id,
        //         'category_id' => $request->category_id,
        //         'photo' => json_encode($data),
        //         'brand' => $request->brand,
        //         'name' => $request->name,
        //         'description' => $request->description,
        //         'details' => $request->details,
        //         'price' => $request->price,
        //     ]);
        //     $stock = Stock::create([
        //         'product_id' => $product->id,
        //         'size' => $request->size,
        //         'color' => $request->color,
        //         'quantity' => $request->quantity,
        //     ]);

        //     return response()->json(compact('product', 'stock'));
        // }

        // return response()->json(['Error' => 'Unauthorized']);
        $user = auth()->authenticate();

        if($user){
            $userId = 1;       

            $validatedData = $request->validate([
                'category_id' => 'required|integer',
            
                'brand' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'details' => 'required|string',
                'price' => 'required|numeric',
            ]);

            $data = $data = '["Untitled.png"]';;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $path = $file->store('public/img');
                $url = Storage::url($path);
                $originalFileName = $file->getClientOriginalName();
                
                $data = '["'. $originalFileName . '"]';
            }

            // Create the product
            $product = Product::create([
                'user_id' => $userId,
                'category_id' => $validatedData['category_id'],
                'photo' => $data,
                'brand' => $validatedData['brand'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'details' => $validatedData['details'],
                'price' => $validatedData['price'],
            ]);
                
            $stock = Stock::create([
                'product_id' => $product->id,
                'size' => $request->size,
                'color' => $request->color,
                'quantity' => $request->quantity,
            ]);

            return response()->json(compact('product', 'stock'));
            
        }
                
        return response()->json(['Error' => 'Unauthorized']);
    }
    
    public function destroy($id)
    {
        // if ($user = auth()->authenticate()) {
        //     $product = Product::findOrFail($id);
        //     // return $product->photo;
        //     if ($product) {
        //         if ($product->photo != null) {
        //             foreach (json_decode($product->photo) as $photo) {
        //                 unlink(public_path() . '\\img\\' . $photo);
        //             }
        //         }
        //         $product->delete();
        //     }

        //     return response()->json(['Message' => 'Successfully deleted product']);

        // }
        // return response()->json(['Error' => 'Unauthorized']);


        $product = Product::findOrFail($id);
        // return $product->photo;
        if ($product) {
            if ($product->photo != null) {
                foreach (json_decode($product->photo) as $photo) {
                    unlink(public_path() . '\\img\\' . $photo);
                }
            }
            $product->delete();
        }

        return response()->json(['Message' => 'Successfully deleted product']);

    }

    public function search($name){
        return Product::with('category','stocks')
            ->where('name', 'like', '%'.$name.'%')
            ->orderBy('created_at','desc')
            ->paginate(4);
    }

    public function update(Request $request, $id){
        $product = Product::findOrFail($id);
        $product->update($request->all());
        
        return response()->json(['Message' => 'Successfully Update product']);
    }
}