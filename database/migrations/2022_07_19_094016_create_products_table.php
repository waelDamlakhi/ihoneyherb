<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('restrict')->onUpdate('cascade');
            $table->float('AED', 8, 2);
            $table->float('USD', 8, 2);
            $table->float('SAR', 8, 2);
            $table->float('quantity', 8, 2);
            $table->float('weight', 8, 2);
            $table->string('imageUrl');
            $table->string('imagePath');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
