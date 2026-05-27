<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE maintenance_orders MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending'");

        DB::table('maintenance_orders')->where('status', 'Pending')->update(['status' => 'pending']);
        DB::table('maintenance_orders')->where('status', 'In_Progress')->update(['status' => 'in_progress']);
        DB::table('maintenance_orders')->where('status', 'Done')->update(['status' => 'done']);
        DB::table('maintenance_orders')->where('status', 'Cancelled')->update(['status' => 'cancelled']);

        Schema::table('maintenance_orders', function (Blueprint $table) {
            $table->string('priority', 10)->default('medium')->after('complaint_photo');
            $table->dateTime('sla_deadline')->nullable()->after('completion_date');
            $table->string('sla_status', 20)->default('on_track')->after('sla_deadline');
            $table->decimal('estimated_cost', 12, 2)->nullable()->after('cost');
            $table->text('estimated_description')->nullable()->after('estimated_cost');
            $table->string('cost_status', 20)->default('none')->after('payment_status');
            $table->foreignId('cost_approved_by')->nullable()->constrained('users')->after('cost_status');
            $table->timestamp('cost_approved_at')->nullable()->after('cost_approved_by');
            $table->date('scheduled_date')->nullable()->after('cost_approved_at');
            $table->text('admin_notes')->nullable()->after('scheduled_date');
            $table->text('rejection_reason')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_orders', function (Blueprint $table) {
            $table->dropColumn([
                'priority', 'sla_deadline', 'sla_status',
                'estimated_cost', 'estimated_description', 'cost_status',
                'cost_approved_by', 'cost_approved_at', 'scheduled_date',
                'admin_notes', 'rejection_reason',
            ]);
        });

        DB::table('maintenance_orders')->where('status', 'pending')->update(['status' => 'Pending']);
        DB::table('maintenance_orders')->where('status', 'in_progress')->update(['status' => 'In_Progress']);
        DB::table('maintenance_orders')->where('status', 'done')->update(['status' => 'Done']);
        DB::table('maintenance_orders')->where('status', 'cancelled')->update(['status' => 'Cancelled']);

        DB::statement("ALTER TABLE maintenance_orders MODIFY COLUMN status ENUM('Pending','In_Progress','Done','Cancelled') NOT NULL DEFAULT 'Pending'");
    }
};
