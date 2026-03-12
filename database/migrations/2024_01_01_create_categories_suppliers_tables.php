<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── CATEGORIES ────────────────────────────────────────────
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id('category_id');
                $table->string('category_name', 100)->unique();
                $table->string('icon', 60)->default('fa-tag');
                $table->string('color_hex', 10)->default('#17b8dc');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── SUBCATEGORIES ─────────────────────────────────────────
        if (!Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id('subcategory_id');
                $table->foreignId('category_id')
                      ->constrained('categories', 'category_id')
                      ->cascadeOnDelete();
                $table->string('subcategory_name', 100);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['category_id', 'subcategory_name']);
            });
        }

        // ── SUPPLIERS ─────────────────────────────────────────────
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id('supplier_id');
                $table->string('supplier_name', 150)->unique();
                $table->string('contact_person', 120)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email', 120)->nullable();
                $table->text('address')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // ── SEED: DEFAULT CATEGORIES (only if empty) ──────────────
        if (DB::table('categories')->count() === 0) {
            $now = now();
            DB::table('categories')->insert([
                ['category_name' => 'Engine Parts',   'icon' => 'fa-gears',          'color_hex' => '#17b8dc', 'description' => 'Pistons, gaskets, camshafts and all internal engine components.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Brake System',   'icon' => 'fa-hand-back-fist', 'color_hex' => '#dc2626', 'description' => 'Brake pads, discs, drums, calipers and brake fluids.',              'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Electrical',     'icon' => 'fa-bolt',           'color_hex' => '#6366f1', 'description' => 'CDI units, starters, batteries, wiring harnesses.',                 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Body Parts',     'icon' => 'fa-shield',         'color_hex' => '#78716c', 'description' => 'Fairings, fenders, covers and frame components.',                   'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Suspension',     'icon' => 'fa-car-side',       'color_hex' => '#16a34a', 'description' => 'Fork seals, shock absorbers, swing arms.',                         'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Filters',        'icon' => 'fa-filter',         'color_hex' => '#4ade80', 'description' => 'Oil filters, air filters and fuel filters.',                        'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Exhaust',        'icon' => 'fa-wind',           'color_hex' => '#f97316', 'description' => 'Mufflers, exhaust pipes, headers and gaskets.',                    'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
                ['category_name' => 'Tires & Wheels', 'icon' => 'fa-circle-dot',     'color_hex' => '#d97706', 'description' => 'Tires, rims, inner tubes and wheel bearings.',                     'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);

            // ── SEED: SUBCATEGORIES ───────────────────────────────
            $categories = DB::table('categories')->pluck('category_id', 'category_name');

            $subcats = [
                'Engine Parts'   => ['Pistons & Rings', 'Gaskets', 'Camshaft & Valves', 'Crankshaft', 'Cylinder Head'],
                'Brake System'   => ['Brake Pads', 'Brake Discs', 'Brake Drums', 'Calipers', 'Brake Fluids'],
                'Electrical'     => ['CDI Units', 'Batteries', 'Starters', 'Lighting', 'Wiring Harness'],
                'Body Parts'     => ['Fairings', 'Fenders', 'Side Covers', 'Fuel Tanks', 'Seat Covers'],
                'Suspension'     => ['Fork Seals', 'Shock Absorbers', 'Swing Arms', 'Steering'],
                'Filters'        => ['Oil Filters', 'Air Filters', 'Fuel Filters'],
                'Exhaust'        => ['Mufflers', 'Exhaust Pipes', 'Headers'],
                'Tires & Wheels' => ['Front Tires', 'Rear Tires', 'Rims', 'Inner Tubes'],
            ];

            foreach ($subcats as $catName => $subs) {
                $catId = $categories[$catName] ?? null;
                if (!$catId) continue;
                foreach ($subs as $sub) {
                    DB::table('subcategories')->insert([
                        'category_id'      => $catId,
                        'subcategory_name' => $sub,
                        'is_active'        => true,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }
            }
        }

        // ── SEED: SUPPLIERS (only if empty) ───────────────────────
        if (DB::table('suppliers')->count() === 0) {
            $now = now();
            DB::table('suppliers')->insert([
                ['supplier_name' => 'Yamaha Parts PH',     'contact_person' => 'Jose Santos',    'phone' => '09171234567', 'email' => 'jose@yamaha-ph.com',     'address' => 'Quezon City, Metro Manila',     'status' => 'active',   'created_at' => $now, 'updated_at' => $now],
                ['supplier_name' => 'Honda Parts Direct',  'contact_person' => 'Maria Reyes',    'phone' => '09281234567', 'email' => 'maria@hondadirect.ph',   'address' => 'Makati City, Metro Manila',      'status' => 'active',   'created_at' => $now, 'updated_at' => $now],
                ['supplier_name' => 'Suzuki Moto PH',      'contact_person' => 'Pedro Cruz',     'phone' => '09391234567', 'email' => 'pedro@suzukimoto.ph',    'address' => 'Pasig City, Metro Manila',       'status' => 'active',   'created_at' => $now, 'updated_at' => $now],
                ['supplier_name' => 'Universal Parts Co.', 'contact_person' => 'Ana Dela Cruz',  'phone' => '09451234567', 'email' => 'ana@universalparts.com', 'address' => 'Caloocan City, Metro Manila',    'status' => 'inactive', 'created_at' => $now, 'updated_at' => $now],
                ['supplier_name' => 'Kawasaki Supply PH',  'contact_person' => 'Carlo Bautista', 'phone' => '09561234567', 'email' => 'carlo@kawasaki-ph.com',  'address' => 'Mandaluyong City, Metro Manila', 'status' => 'active',   'created_at' => $now, 'updated_at' => $now],
                ['supplier_name' => 'TVS Motors PH',       'contact_person' => 'Lena Torres',    'phone' => '09671234567', 'email' => 'lena@tvsmotors.ph',      'address' => 'Valenzuela City, Metro Manila',  'status' => 'active',   'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('suppliers');
    }
};
