<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\StoreProduct;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }
    public function new($id)
    {             
        $products = Product::with('category')
                    ->where('category_id', $id)
                    ->orderBy('id', 'desc')
                    ->paginate(4);
                    
        foreach ($products as $product) {
            if ($product->reviews()->exists()) {
                $product['review'] = $product->reviews()->avg('rating');
            }
        }            
        
        
        return $products;
    }
    public function topSelling($id)
    {
        $products = Product::with('category')
            ->where('category_id', $id)
            ->take(4)
            ->get();
            
        foreach ($products as $product) {
            if ($product->reviews()->exists()) {
                $product['review'] = $product->reviews()->avg('rating');
            }

            if ($product->stocks()->exists()) {
                $num_orders = 0;
                $stocks = $product->stocks()->get();
                foreach ($stocks as $stock) {
                    $num_orders += $stock->orders()->count();
                }
                $product['num_orders'] = $num_orders;
            } else {
                $product['num_orders'] = 0;
            }
        }
        return $products->sortByDesc('num_orders')->values()->all();
    }

    public function store(StoreProduct $request)
    {
        $user = auth()->authenticate();
        if ($user) {
            // $validator = $request->validated();           
            
            $category = Category::create([               
                'name' => $request->name,                
            ]);         

            return response()->json(compact('category'));
        }

        return response()->json(['Error' => 'Unauthorized']);

        // $validator = $request->validated();           
            
        // $category = Category::create([               
        //     'name' => $request->name,                
        // ]);         

        // return response()->json(compact('category'));
    }

    public function destroy($id)
    {
        $user = auth()->authenticate();
        if ($user) {
            $category = Category::findOrFail($id);
            
            if ($category) {            
                $category->delete();
            }

            return response()->json(['Message' => 'Successfully deleted Category']);

        }
        return response()->json(['Error' => 'Unauthorized']);

        // $category = Category::findOrFail($id);
            
        // if ($category) {            
        //     $category->delete();
        // }

        // return response()->json(['Message' => 'Successfully deleted Category']);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->authenticate();
        if($user){
            $category = Category::findOrFail($id);
            $category->update($request->all());
            return response()->json(['Message' => 'Successfully Update Category']);
        }

        return response()->json(['Error' => 'Unauthorized']);
        
    }
    
    public function show($id)
    {
        $category = Category::findOrFail($id); 
        
        return $category;
    }
}