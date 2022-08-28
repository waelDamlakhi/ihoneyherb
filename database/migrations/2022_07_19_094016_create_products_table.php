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
            $table->foreignId('admin_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('department_id')->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->float('AED', 8, 2);
            $table->float('USD', 8, 2);
            $table->float('SAR', 8, 2);
            $table->integer('quantity');
            $table->float('discount', 3, 2);
            $table->string('imageUrl');
            $table->string('imagePath');
            $table->string('bannerUrl');
            $table->string('bannerPath');
            $table->string('show_in');
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
