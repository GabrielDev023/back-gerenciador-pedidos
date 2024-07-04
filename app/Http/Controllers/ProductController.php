<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Carrega todos os produtos com suas imagens relacionadas
        $products = Product::with('images')->get();

        return ProductResource::collection($products);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Permitir imagens, mas não obrigar
        ]);
    
        // Criar o produto inicialmente
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->save();
    
        // Verificar se houve envio de imagens
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product_images', 'public');
    
                // Criar uma nova instância de ProductImage
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = $imagePath;
                $productImage->save();
            }
        }
    
        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'product' => new ProductResource($product),
        ], 201);
    }
    
    
    
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('images')->findOrFail($id);
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $product = Product::findOrFail($id);
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->save();

        // Verificar se houve envio de imagens
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product_images', 'public');

                // Criar uma nova instância de ProductImage
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = $imagePath;
                $productImage->save();
            }
        }

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'product' => new ProductResource($product),
        ], 200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Crie um método destroy no ProductController que permita excluir um produto existente.
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Produto excluído com sucesso.',
        ], 200);
    }
}
