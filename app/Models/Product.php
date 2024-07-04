<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage; // Importe o modelo ProductImage aqui

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock'];

    /**
     * Define o relacionamento com imagens do produto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
