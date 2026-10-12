<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   
    
public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
              ->constrained('users')
              ->onDelete('cascade');

        $table->decimal('grand_total', 10, 2);
        $table->decimal('sub_total', 10, 2);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('delivery_charges', 10, 2)->default(0);

        $table->string('name');
        $table->string('email');
        $table->string('phone');
        $table->text('address');
        $table->string('city');
        $table->string('state');
        $table->string('zip');

        $table->enum('status', ['pending','out_for_delivery','delivered','cancelled'])->default('pending');
        $table->enum('payment_method',['cod','stripe']);
        $table->enum('payment_status',['paid','not paid']);

        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};