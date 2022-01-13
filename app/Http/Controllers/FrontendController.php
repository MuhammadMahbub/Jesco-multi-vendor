<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Rating;
use App\Models\Wishlist;
use App\Models\Size;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    function index()
    {
        $categories = Category::where('status', 'show')->get();
        $allproducts = Product::all();
        $banners = Banner::where('status', 'show')->limit(3)->get();
        return view('frontend.index', compact('categories', 'allproducts', 'banners'));
    }
    function contact()
    {
        return view('frontend.contact');
    }
    function shop()
    {
        if (isset($_GET['min_price']) || isset($_GET['max_price'])) {
            $min = $_GET['min_price'];
            $max = $_GET['max_price'];
            $categories = Category::where('status', 'show')->get();
            $sizes = Size::all();
            $allproducts = Product::whereBetween('product_price', [$_GET['min_price'], $_GET['max_price']])
                ->get();
            return view('frontend.shop', compact('categories', 'allproducts', 'min', 'max', 'sizes'));
        } else {
            $min = "";
            $max = "";
            $sizes = Size::all();
            $categories = Category::where('status', 'show')->get();
            $allproducts = Product::all();
            return view('frontend.shop', compact('categories', 'allproducts', 'min', 'max', 'sizes'));
        }
    }
    function productsize($product_size)
    {

        foreach (Size::where('size', $product_size)->get() as $item) {
            echo $allproducts = Product::find($item->product_id)->product_photo;
        };
        dd();
        $min = "";
        $max = "";
        $sizes = Size::all();
        $categories = Category::where('status', 'show')->get();
        foreach (Size::where('size', $product_size)->get() as $item) {
            $allproducts = Product::find($item->product_id);
        };
        return view('frontend.shop', compact('categories', 'allproducts', 'min', 'max', 'sizes'));
    }

    function product_details($slug)
    {
        $wishlist_status = Wishlist::where('user_id', auth()->id())->where('product_id', Product::where('product_slug', $slug)->first()->id)->exists();
        if ($wishlist_status) {
            $wishlist_id = Wishlist::where('user_id', auth()->id())->where('product_id', Product::where('product_slug', $slug)->first()->id)->first()->id;
        } else {
            $wishlist_id = "";
        }
        $cat_id = Product::where('product_slug', $slug)->firstOrFail()->category_id;
        $related_products = Product::where('product_slug', '!=', $slug)->where('category_id', $cat_id)->get();
        $productdetails = Product::where('product_slug', $slug)->firstOrFail();
        $reviews = Rating::where('product_id', Product::where('product_slug', $slug)->firstOrFail()->id)->get();
        return view('frontend.productdetails', compact('productdetails', 'related_products', 'wishlist_status', 'wishlist_id', 'reviews'));
    }

    function categorywiseproducts($category_id)
    {
        $category_name = Category::findOrFail($category_id);
        $categorywiseproducts =  Product::where('category_id', $category_id)->get();
        return view('frontend.categorywiseproducts', compact('categorywiseproducts', 'category_name'));
    }
}