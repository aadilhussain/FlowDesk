<?php

use App\Enums\WorkspaceRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('user_workspace', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default(WorkspaceRole::Member->value);
            $table->timestamps();

            $table->unique(['workspace_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });

        if (Schema::hasTable('tasks') && ! Schema::hasColumn('tasks', 'workspace_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('workspace_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->nullOnDelete();

                $table->index(['workspace_id', 'status']);
            });

            $this->backfillExistingTasks();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'workspace_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropIndex(['workspace_id', 'status']);
                $table->dropConstrainedForeignId('workspace_id');
            });
        }

        Schema::dropIfExists('user_workspace');
        Schema::dropIfExists('workspaces');
    }

    private function backfillExistingTasks(): void
    {
        $now = now();

        DB::table('tasks')
            ->select('user_id')
            ->whereNull('workspace_id')
            ->distinct()
            ->orderBy('user_id')
            ->each(function (object $row) use ($now): void {
                $workspaceId = DB::table('workspaces')->insertGetId([
                    'owner_id' => $row->user_id,
                    'name' => 'Personal Workspace',
                    'slug' => Str::slug('personal-'.$row->user_id.'-workspace'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('user_workspace')->insert([
                    'workspace_id' => $workspaceId,
                    'user_id' => $row->user_id,
                    'role' => WorkspaceRole::Owner->value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('tasks')
                    ->where('user_id', $row->user_id)
                    ->whereNull('workspace_id')
                    ->update([
                        'workspace_id' => $workspaceId,
                        'updated_at' => $now,
                    ]);
            });
    }
};
