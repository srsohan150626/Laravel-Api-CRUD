<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;
use Validator;
use DB;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id','DESC')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric'
        ]);
        $validator = Validator::make($request->all(), [
            'image' => 'required|image64:jpeg,jpg,png'
            
        ]);

        //dd($request->all());
        if ($validator->fails())
        {
              return response()->json(['error' => $validator->errors()->getMessages()], 422);
        } else{
            $position = strpos($request->image,';');
            $sub=substr($request->image, 0,$position);
            $ext=explode('/',$sub)[1];
            $name=time().".".$ext;
            $img=Image::make($request->image)->resize(540,500);
            $upload_path='images/products/';
            $image_url=$upload_path.$name;
            $img->save($image_url);

            $product = new Product;
            $product->title = $request->title;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->image = $image_url;
            $product->save();

            return response()->json(['suceess','Products Inserted Successfully!']);
        }
        
        
    }

    public function show($id)
    {
        $product=Product::where('id',$id)->first();
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric'
        ]);

        $data=array();
        $data['title']=$request->title;
        $data['description']=$request->description;
        $data['price']=$request->price;
        $newimage=$request->newimage;
        
        if($newimage) {

        $validator = Validator::make($request->all(), [
            'newimage' => 'required|image64:jpeg,jpg,png'
        ]);

        if ($validator->fails())
        {
                return response()->json(['error' => $validator->errors()->getMessages()], 422);
        }

        $position = strpos($newimage,';');
        $sub=substr($newimage, 0,$position);
        $ext=explode('/',$sub)[1];
        $name=time().".".$ext;
        $img=Image::make($newimage)->resize(540,500);
        $upload_path='images/products/';
        $image_url=$upload_path.$name;
        $success=$img->save($image_url);
        if($success) {
            $data['image'] =$image_url;
            $img=DB::table('products')->where('id',$id)->first();
            $image_path = $img->image;
            $done=unlink($image_path);
            DB::table('products')->where('id',$id)->update($data);
        }

        }else{
            $oldimage=$request->image;
            $data['image']=$oldimage;
            $user=DB::table('products')->where('id',$id)->update($data);
        }
        return response()->json(['suceess','Products updated Successfully!']);

    }
    public function destroy($id)
    {
        $product= Product::where('id',$id)->first();
        $image= $product->image;
        unlink($image);
        Product::where('id',$id)->delete();
        return response()->json(['suceess','Products deleted Successfully!']);
    }
}
