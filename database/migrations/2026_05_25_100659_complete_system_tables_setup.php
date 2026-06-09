<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ============================================
        // 1. TEST PATTERNS TABLE
        // ============================================
        if (!Schema::hasTable('test_patterns')) {
            Schema::create('test_patterns', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique(); // MDCAT, ECAT, etc.
                $table->text('description')->nullable();
                $table->decimal('biology_percentage', 5, 2)->default(34);
                $table->decimal('chemistry_percentage', 5, 2)->default(27);
                $table->decimal('physics_percentage', 5, 2)->default(27);
                $table->decimal('english_percentage', 5, 2)->default(9);
                $table->decimal('reasoning_percentage', 5, 2)->default(3);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // ============================================
        // 2. TEST TYPES TABLE
        // ============================================
        if (!Schema::hasTable('test_types')) {
            Schema::create('test_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique(); // Free, Member, Premium
                $table->integer('mcq_count'); // 25, 90, 180
                $table->integer('duration_minutes'); // Time limit
                $table->decimal('price', 10, 2)->default(0); // 0 for free
                $table->text('description')->nullable();
                $table->enum('tier', ['free', 'member', 'premium'])->default('free');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // ============================================
        // 3. COMMERCIAL SLOTS TABLE
        // ============================================
        if (!Schema::hasTable('commercial_slots')) {
            Schema::create('commercial_slots', function (Blueprint $table) {
                $table->id();
                $table->integer('slot_number')->unique(); // 1-10
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->string('target_url')->nullable();
                $table->enum('position', [
                    'dashboard',
                    'test_selection',
                    'result_page',
                    'leaderboard',
                    'analytics'
                ])->default('dashboard');
                $table->boolean('active')->default(false);
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->integer('priority')->default(0);
                $table->bigInteger('impressions')->default(0);
                $table->bigInteger('clicks')->default(0);
                $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
                $table->timestamps();
            });
        }

        // ============================================
        // 4. UPDATE EXAM SESSIONS TABLE
        // ============================================
        if (Schema::hasTable('exam_sessions')) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('exam_sessions', 'mcq_sequence')) {
                Schema::table('exam_sessions', function (Blueprint $table) {
                    $table->json('mcq_sequence')->nullable();
                });
            }

            if (!Schema::hasColumn('exam_sessions', 'expires_at')) {
                Schema::table('exam_sessions', function (Blueprint $table) {
                    $table->timestamp('expires_at')->nullable()->after('started_at');
                });
            }

            if (!Schema::hasColumn('exam_sessions', 'time_taken_minutes')) {
                Schema::table('exam_sessions', function (Blueprint $table) {
                    $table->integer('time_taken_minutes')->default(0);
                });
            }

            // Update status enum to include all values
            try {
                \DB::statement("ALTER TABLE exam_sessions MODIFY status ENUM('pending', 'ongoing', 'completed', 'expired', 'auto_submitted') DEFAULT 'pending'");
            } catch (\Exception $e) {
                // Enum already correct or different DB system
            }
        }

        // ============================================
        // 5. UPDATE ANSWER LOGS TABLE
        // ============================================
        if (Schema::hasTable('answer_logs')) {
            // Rename 'order' to 'question_order' if it exists
            if (Schema::hasColumn('answer_logs', 'order') && !Schema::hasColumn('answer_logs', 'question_order')) {
                Schema::table('answer_logs', function (Blueprint $table) {
                    $table->renameColumn('order', 'question_order');
                });
            }

            // Add question_order if it doesn't exist
            if (!Schema::hasColumn('answer_logs', 'question_order')) {
                Schema::table('answer_logs', function (Blueprint $table) {
                    $table->integer('question_order')->default(0);
                });
            }

            // Add unique constraint if it doesn't exist
            try {
                Schema::table('answer_logs', function (Blueprint $table) {
                    $table->unique(['exam_session_id', 'mcq_id']);
                });
            } catch (\Exception $e) {
                // Constraint already exists
            }
        }

        // ============================================
        // 6. CREATE ADDITIONAL TABLES FOR FEATURES
        // ============================================

        // Achievements Table
        if (!Schema::hasTable('achievements')) {
            Schema::create('achievements', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->string('badge_icon')->nullable();
                $table->enum('type', [
                    'score',
                    'streak',
                    'subject_master',
                    'speed',
                    'accuracy'
                ]);
                $table->integer('threshold')->default(0); // Requirement value
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // User Achievements Table
        if (!Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
                $table->timestamp('earned_at')->useCurrent();
                $table->timestamps();
                $table->unique(['user_id', 'achievement_id']);
            });
        }

        // Parent Student Relations Table
        if (!Schema::hasTable('parent_student_relations')) {
            Schema::create('parent_student_relations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                $table->unique(['parent_id', 'student_id']);
            });
        }

        // MCQ Comments Table
        if (!Schema::hasTable('mcq_comments')) {
            Schema::create('mcq_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('mcq_id')->constrained()->onDelete('cascade');
                $table->text('comment');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();
            });
        }

        // Admin Settings Table
        if (!Schema::hasTable('admin_settings')) {
            Schema::create('admin_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->enum('type', ['boolean', 'string', 'number', 'json'])->default('string');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Notifications Table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->enum('type', ['exam', 'achievement', 'subscription', 'system'])->default('system');
                $table->boolean('read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Drop in reverse order (respecting foreign keys)
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('admin_settings');
        Schema::dropIfExists('mcq_comments');
        Schema::dropIfExists('parent_student_relations');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('commercial_slots');
        Schema::dropIfExists('test_types');
        Schema::dropIfExists('test_patterns');
    }
};