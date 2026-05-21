<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * 1. Drop foreign key & unique lama
         */
        Schema::table('promo_products', function (Blueprint $table) {
            // Drop FKs first to avoid index dependency issues
            $table->dropForeign(['course_id']);
            $table->dropForeign(['class_id']);
            $table->dropForeign(['promo_id']);
        });

        Schema::table('promo_products', function (Blueprint $table) {
            // drop unique lama
            $table->dropUnique(
                'promo_products_promo_id_course_id_class_id_unique'
            );
        });

        /**
         * 2. Tambah kolom baru
         */
        Schema::table('promo_products', function (Blueprint $table) {

            $table->unsignedBigInteger('product_id')
                ->nullable()
                ->after('promo_id');

            $table->enum('type_product', ['course', 'class'])
                ->nullable()
                ->after('product_id');
        });

        /**
         * 3. Migrasi data lama
         *
         * Struktur lama:
         * promo_id | course_id | class_id
         *
         * Struktur baru:
         * promo_id | product_id | type_product
         *
         * Karena struktur lama memungkinkan
         * course_id dan class_id dalam satu row,
         * maka data akan dipecah menjadi row baru.
         */

        $promoProducts = DB::table('promo_products')->get();

        foreach ($promoProducts as $item) {

            /**
             * Simpan data course pada row lama
             */
            if ($item->course_id) {

                DB::table('promo_products')
                    ->where('id', $item->id)
                    ->update([
                        'product_id' => $item->course_id,
                        'type_product' => 'course',
                    ]);
            }

            /**
             * Jika ada class_id,
             * buat row baru
             */
            if ($item->class_id) {

                DB::table('promo_products')->insert([
                    'promo_id' => $item->promo_id,
                    'product_id' => $item->class_id,
                    'type_product' => 'class',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        /**
         * 4. Hapus kolom lama
         */
        Schema::table('promo_products', function (Blueprint $table) {
            $table->dropIndex('promo_products_class_id_foreign');
            $table->dropIndex('promo_products_course_id_foreign');

            $table->dropColumn([
                'course_id',
                'class_id',
            ]);
        });

        /**
         * 5. Tambahkan FK & unique baru
         */
        Schema::table('promo_products', function (Blueprint $table) {

            // ubah jadi NOT NULL
            $table->unsignedBigInteger('product_id')
                ->nullable(false)
                ->change();

            $table->enum('type_product', ['course', 'class'])
                ->nullable(false)
                ->change();

            // Tambahkan kembali FK promo_id
            $table->foreign('promo_id')
                ->references('id')
                ->on('promos')
                ->onDelete('cascade');

            // unique baru
            $table->unique([
                'promo_id',
                'product_id',
                'type_product',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('promo_products', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
        });

        Schema::table('promo_products', function (Blueprint $table) {
            $table->dropUnique(
                'promo_products_promo_id_product_id_type_product_unique'
            );
        });

        Schema::table('promo_products', function (Blueprint $table) {

            $table->foreignId('course_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('class_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
        });

        $promoProducts = DB::table('promo_products')->get();

        foreach ($promoProducts as $item) {

            if ($item->type_product === 'course') {

                DB::table('promo_products')
                    ->where('id', $item->id)
                    ->update([
                        'course_id' => $item->product_id,
                    ]);
            }

            if ($item->type_product === 'class') {

                DB::table('promo_products')
                    ->where('id', $item->id)
                    ->update([
                        'class_id' => $item->product_id,
                    ]);
            }
        }

        Schema::table('promo_products', function (Blueprint $table) {

            $table->dropColumn([
                'product_id',
                'type_product',
            ]);

            $table->foreign('promo_id')
                ->references('id')
                ->on('promos')
                ->onDelete('cascade');

            $table->unique([
                'promo_id',
                'course_id',
                'class_id',
            ]);
        });
    }
};
