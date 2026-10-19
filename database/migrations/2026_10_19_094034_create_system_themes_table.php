<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('description', 255);
            $table->timestamps();
        });


        DB::table('system_themes')->insert([
            [
                'name' => 'navy-gold',
                'description' => 'Navy header, gold accents. Corporate and traditional.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'burgundy-stone',
                'description' => 'Deep wine with a warm stone accent. Formal and refined.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'slate-teal',
                'description' => 'Blue-grey with a teal accent. Modern and calm.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'charcoal-copper',
                'description' => 'Near-black with copper accents. Bold and understated.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'royal-purple',
                'description' => 'Deep purple with gold. Premium look.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'forest-sage',
                'description' => 'Dark green with an olive-sage accent. Natural and softer than the default.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'ocean-blue',
                'description' => 'Corporate blue with a sky accent. Familiar and clean.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'midnight-rose',
                'description' => 'Dark indigo with a rose accent. Elegant.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'espresso-cream',
                'description' => 'Brown with a tan accent on warm backgrounds. Cozy and classic.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'graphite',
                'description' => 'Neutral greys. The most minimal option.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'crimson-ash',
                'description' => 'Deep red with a cool ash-grey accent. Serious and strong.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'indigo-amber',
                'description' => 'Rich indigo with an amber accent. Energetic but professional.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'teal-coral',
                'description' => 'Deep teal with a coral-orange accent. Fresh and friendly.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'plum-mauve',
                'description' => 'Dark plum with a soft mauve accent. Creative and warm.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'terracotta-sand',
                'description' => 'Clay red-brown with a sand accent. Earthy and inviting.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'steel-orange',
                'description' => 'Steel blue-grey with an orange accent. Industrial and clear.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'olive-bronze',
                'description' => 'Dark olive with a bronze accent. Heritage and grounded.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'black-lime',
                'description' => 'Near-black with a lime accent. Bold and modern.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'sky-light',
                'description' => 'Soft blue with a pale sky accent. Airy and clean.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'mint-fresh',
                'description' => 'Gentle green with a mint accent. Fresh and calm.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'blush-pink',
                'description' => 'Dusty rose with a light pink accent. Soft and friendly.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'lavender-mist',
                'description' => 'Muted violet with a lavender accent. Light and creative.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'peach-apricot',
                'description' => 'Warm apricot with a peach accent. Sunny and welcoming.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'butter-olive',
                'description' => 'Soft olive with a butter-yellow accent. Gentle and natural.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'cloud-grey',
                'description' => 'Light slate with a pale grey-blue accent. Quiet and neutral.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'aqua-sand',
                'description' => 'Soft teal with a sand accent. Beachy and relaxed.',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_themes');
    }
};
