<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',50);
            $table->integer('author_id')->unsigned()->nullable()->index();
            $table->string('isbn',30);
            $table->integer('quantity')->unsigned()->default(0);
            $table->decimal('overdue_fine', 5, 3)->default(0.0);
            $table->string('shelf_location',20);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('authors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('books');
    }
}
