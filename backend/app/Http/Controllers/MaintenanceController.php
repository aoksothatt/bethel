<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Active database connection name for maintenance operations.
     */
    protected ?string $activeConnection = null;

    public function __construct()
    {
        $this->activeConnection = DB::getDefaultConnection();
    }

    /**
     * Staged One-Time Import for 62 New Players.
     *
     * SAFETY GUARANTEES:
     * 1. Protected by auth:sanctum and explicit admin email verification.
     * 2. Protected by config('maintenance.enable_one_time_import').
     * 3. Explicit request modes: Dry-Run (default) vs Live Execution (?execute=1 + header).
     * 4. Dry-run performs zero transactions, zero row locks, and zero writes.
     * 5. Live execution verifies TiDB pessimistic mode and applies READ COMMITTED isolation.
     * 6. Live execution locks shared mutex row (players.id = 1).
     * 7. Live execution snapshots all 31 original database records and asserts deep field-by-field immutability before commit.
     * 8. Strict baseline count = 31 and composite duplicate rechecks inside atomic transaction.
     * 9. Inserts 62 records with payment_status = unpaid, notes = NULL, MEAK shirt_number = 0.
     * 10. Asserts final count = 93 before commit; rolls back completely on any mismatch.
     */
    public function import62Players(Request $request): JsonResponse
    {
        // 1. Feature flag guard (defaults to false, fails closed)
        $isFeatureEnabled = config('maintenance.enable_one_time_import') === true;
        if (!$isFeatureEnabled) {
            return response()->json([
                'error' => 'FEATURE_DISABLED',
                'message' => 'The one-time import maintenance feature is currently disabled.'
            ], 403);
        }

        // 2. Administrator identity check (fails closed, zero hardcoded fallback)
        $configuredAdmin = config('maintenance.admin_email');
        if (empty($configuredAdmin) || !is_string($configuredAdmin) || trim($configuredAdmin) === '') {
            return response()->json([
                'error' => 'ADMIN_EMAIL_NOT_CONFIGURED',
                'message' => 'Production administrator identity (ADMIN_EMAIL) is not configured. Request rejected.'
            ], 500);
        }

        $authenticatedUser = $request->user();
        if (!$authenticatedUser || empty($authenticatedUser->email)) {
            return response()->json([
                'error' => 'UNAUTHENTICATED',
                'message' => 'Authentication required.'
            ], 401);
        }

        if (strtolower(trim($authenticatedUser->email)) !== strtolower(trim($configuredAdmin))) {
            return response()->json([
                'error' => 'FORBIDDEN',
                'message' => 'The authenticated user does not match the configured administrator identity.'
            ], 403);
        }

        // 3. Load & statically validate the staged dataset
        $jsonPath = database_path('data/new-62-players.json');
        if (!file_exists($jsonPath)) {
            return response()->json([
                'error' => 'DATASET_NOT_FOUND',
                'message' => 'Staged dataset file not found at database/data/new-62-players.json.'
            ], 500);
        }

        $rawJson = file_get_contents($jsonPath);
        $newPlayers = json_decode($rawJson, true);
        if (!is_array($newPlayers) || count($newPlayers) !== 62) {
            return response()->json([
                'error' => 'INVALID_RECORD_COUNT',
                'message' => 'Dataset must contain exactly 62 records. Found: ' . (is_array($newPlayers) ? count($newPlayers) : 0)
            ], 422);
        }

        // Validate individual dataset record integrity
        $allowedSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        foreach ($newPlayers as $p) {
            if (empty($p['name']) || !isset($p['shirt_number']) || !is_int($p['shirt_number']) || $p['shirt_number'] < 0) {
                return response()->json([
                    'error' => 'DATASET_INTEGRITY_VIOLATION',
                    'message' => 'Invalid name or shirt number detected in staged dataset.'
                ], 422);
            }
            if (!in_array($p['jersey_size'], $allowedSizes, true)) {
                return response()->json([
                    'error' => 'DATASET_INTEGRITY_VIOLATION',
                    'message' => 'Invalid jersey size detected in staged dataset.'
                ], 422);
            }
        }

        // 4. Parse execution intent and explicit confirmation headers
        $isExecuteRequested = $request->query('execute') === '1';
        $confirmHeader = $request->header('X-Confirm-Import');

        if ($isExecuteRequested) {
            if (empty($confirmHeader)) {
                return response()->json([
                    'error' => 'CONFIRMATION_REQUIRED',
                    'message' => 'Live execution requested but missing required confirmation header (X-Confirm-Import).'
                ], 400);
            }
            if ($confirmHeader !== 'CONFIRM_62_PLAYERS') {
                return response()->json([
                    'error' => 'INVALID_CONFIRMATION',
                    'message' => 'Invalid confirmation header value. Live execution rejected.'
                ], 403);
            }
        }

        $connectionName = DB::getDefaultConnection();
        $this->activeConnection = $connectionName;

        // =====================================================================
        // BRANCH A: READ-ONLY DRY-RUN (Zero transactions, zero locks, zero writes)
        // =====================================================================
        if (!$isExecuteRequested) {
            $currentCount = DB::connection($connectionName)->table('players')->count();
            if ($currentCount !== 31) {
                return response()->json([
                    'mode' => 'DRY_RUN',
                    'status' => 'BASELINE_BLOCKED',
                    'message' => "Expected exactly 31 production records before import, but found {$currentCount}.",
                    'baseline_count' => $currentCount,
                    'staged_records' => 62,
                    'expected_final_count' => 93,
                    'records_modified' => 0
                ], 409);
            }

            // Read-only 62 composite duplicate check
            $clauses = [];
            $params = [];
            foreach ($newPlayers as $row) {
                $clauses[] = "(LOWER(TRIM(name)) = ? AND shirt_number = ?)";
                $params[] = strtolower(trim($row['name']));
                $params[] = (int)$row['shirt_number'];
            }
            $duplicates = DB::connection($connectionName)->select(
                "SELECT id, name, shirt_number FROM players WHERE " . implode(" OR ", $clauses),
                $params
            );

            if (!empty($duplicates)) {
                return response()->json([
                    'mode' => 'DRY_RUN',
                    'status' => 'DUPLICATES_DETECTED',
                    'message' => 'One or more staged players already exist in the database.',
                    'duplicates_count' => count($duplicates),
                    'records_modified' => 0
                ], 409);
            }

            return response()->json([
                'mode' => 'DRY_RUN',
                'status' => 'READY_FOR_IMPORT',
                'message' => 'Dry-run pre-flight passed 100%. Ready for live execution.',
                'baseline_count' => 31,
                'staged_records' => 62,
                'expected_final_count' => 93,
                'duplicate_collisions' => 0,
                'records_modified' => 0,
                'next_step' => 'Send POST with ?execute=1 and header X-Confirm-Import: CONFIRM_62_PLAYERS'
            ], 200);
        }

        // =====================================================================
        // BRANCH B: LIVE EXECUTION (Pessimistic Lock + Atomic Transaction)
        // =====================================================================
        $isMySql = $this->isMySqlDriver();
        $isolationDirectiveIssued = false;
        $transactionStarted = false;

        // 1. On TiDB/MySQL: Verify pessimistic mode (read-only) and configure next transaction isolation
        if ($isMySql) {
            $mode = $this->getTidbTxnMode();
            if ($mode === null || strtolower(trim($mode)) !== 'pessimistic') {
                return response()->json([
                    'error' => 'UNSUPPORTED_TRANSACTION_MODE',
                    'message' => "TiDB transaction mode must be 'pessimistic', found '" . ($mode ?? '') . "'. Execution safely refused."
                ], 500);
            }
        }

        // 2. Capture connection ID before isolation directive
        $connIdBefore = $this->getActiveConnectionId();

        // 3. Configure next transaction isolation level
        try {
            $isolationDirectiveIssued = $this->executeIsolationDirective();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Isolation directive execution failed: ' . get_class($e));
            $resetSuccess = $this->resetConnection($connectionName);
            if (!$resetSuccess) {
                return response()->json([
                    'error' => 'CONNECTION_RESET_FAILED',
                    'message' => 'Pending transaction directive cleanup failed. Connection could not be safely reset.'
                ], 500);
            }
            return response()->json([
                'error' => 'ISOLATION_DIRECTIVE_FAILED',
                'message' => 'Failed to configure transaction isolation level. Database connection was safely reset.'
            ], 500);
        }

        // 4. Begin atomic transaction
        try {
            $this->executeBeginTransaction();
            $transactionStarted = true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Import transaction startup failed: ' . get_class($e));
            if ($isolationDirectiveIssued) {
                $resetSuccess = $this->resetConnection($connectionName);
                if (!$resetSuccess) {
                    return response()->json([
                        'error' => 'CONNECTION_RESET_FAILED',
                        'message' => 'Pending transaction directive cleanup failed. Connection could not be safely reset.'
                    ], 500);
                }
                return response()->json([
                    'error' => 'TRANSACTION_START_FAILED',
                    'message' => 'Failed to initialize database transaction. Database connection was safely reset.'
                ], 500);
            }
            return response()->json([
                'error' => 'TRANSACTION_START_FAILED',
                'message' => 'Failed to initialize database transaction.'
            ], 500);
        }

        try {
            // 5. Verify connection ID consistency between SET TRANSACTION and beginTransaction
            $connIdInside = $this->getActiveConnectionId();
            $connectionUnavailable = false;
            $connectionMismatch = false;

            if ($isMySql) {
                if ($connIdBefore === null || $connIdInside === null) {
                    $connectionUnavailable = true;
                } elseif ($connIdBefore !== $connIdInside) {
                    $connectionMismatch = true;
                }
            } else {
                if ($connIdBefore !== $connIdInside) {
                    $connectionMismatch = true;
                }
            }

            if ($connectionUnavailable || $connectionMismatch) {
                if ($connectionUnavailable) {
                    \Illuminate\Support\Facades\Log::error("Import connection ID unavailable: before=" . var_export($connIdBefore, true) . ", inside=" . var_export($connIdInside, true));
                } else {
                    \Illuminate\Support\Facades\Log::error("Import connection mismatch: before={$connIdBefore}, inside={$connIdInside}");
                }

                if (DB::connection($connectionName)->transactionLevel() > 0) {
                    try {
                        $this->executeRollback();
                    } catch (\Throwable $rbEx) {
                        \Illuminate\Support\Facades\Log::error('Import rollback during connection verification failed: ' . get_class($rbEx));
                    }
                }

                $resetSuccess = $this->resetConnection($connectionName);
                if (!$resetSuccess) {
                    return response()->json([
                        'error' => 'CONNECTION_RESET_FAILED',
                        'message' => 'Pending transaction directive cleanup failed. Connection could not be safely reset.'
                    ], 500);
                }

                if ($connectionUnavailable) {
                    return response()->json([
                        'error' => 'CONNECTION_ID_UNAVAILABLE',
                        'message' => 'Database connection identity could not be verified. Execution rolled back.'
                    ], 500);
                }

                return response()->json([
                    'error' => 'CONNECTION_MISMATCH',
                    'message' => 'Connection instance changed between transaction configuration and startup. Execution rolled back.'
                ], 409);
            }

            // 6. Acquire exclusive pessimistic lock on shared mutex row (players.id = 1)
            $mutex = DB::connection($connectionName)->table('players')->where('id', 1)->lockForUpdate()->first();
            if (!$mutex) {
                return $this->rollbackAndRespond(
                    'MUTEX_ROW_NOT_FOUND',
                    'Shared mutex anchor row (players.id = 1) was not found. Execution rolled back.',
                    409
                );
            }

            // 7. Fresh read under READ COMMITTED: Recheck baseline count = 31
            $initialCount = DB::connection($connectionName)->table('players')->count();
            if ($initialCount !== 31) {
                return $this->rollbackAndRespond(
                    'BASELINE_MISMATCH',
                    "Expected exactly 31 records inside transaction, but found {$initialCount}. Live execution rolled back.",
                    409,
                    ['initial_count' => $initialCount]
                );
            }

            // 8. Capture complete deep snapshot of all 31 original database records
            $originalRows = DB::connection($connectionName)->table('players')->get();
            if ($originalRows->count() !== 31) {
                return $this->rollbackAndRespond(
                    'BASELINE_MISMATCH',
                    "Expected exactly 31 records inside transaction, but found {$originalRows->count()}. Live execution rolled back.",
                    409,
                    ['initial_count' => $originalRows->count()]
                );
            }

            $originalSnapshot = [];
            foreach ($originalRows as $row) {
                $originalSnapshot[(int)$row->id] = (array)$row;
            }
            $originalIds = array_keys($originalSnapshot);
            if (count($originalIds) !== 31) {
                return $this->rollbackAndRespond(
                    'BASELINE_ID_MISMATCH',
                    'Failed to capture all 31 baseline IDs.',
                    409
                );
            }

            // 9. Fresh read: Recheck all 62 composite duplicates inside locked transaction
            $clauses = [];
            $params = [];
            foreach ($newPlayers as $row) {
                $clauses[] = "(LOWER(TRIM(name)) = ? AND shirt_number = ?)";
                $params[] = strtolower(trim($row['name']));
                $params[] = (int)$row['shirt_number'];
            }
            $duplicates = DB::connection($connectionName)->select(
                "SELECT id, name, shirt_number FROM players WHERE " . implode(" OR ", $clauses),
                $params
            );

            if (!empty($duplicates)) {
                return $this->rollbackAndRespond(
                    'DUPLICATES_DETECTED',
                    'Composite duplicates detected during live transaction. Live execution rolled back.',
                    409,
                    ['duplicates_count' => count($duplicates)]
                );
            }

            // 10. Atomic append-only inserts for all 62 records
            $now = now();
            $inserted = 0;
            foreach ($newPlayers as $row) {
                DB::connection($connectionName)->table('players')->insert([
                    'name' => trim($row['name']),
                    'shirt_number' => (int)$row['shirt_number'],
                    'jersey_size' => strtoupper(trim($row['jersey_size'])),
                    'payment_status' => strtolower(trim($row['payment_status'] ?: 'unpaid')),
                    'notes' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $inserted++;
            }

            if ($inserted !== 62) {
                throw new \RuntimeException("Expected to insert 62 records, but only inserted {$inserted}. Rolling back.");
            }

            // 11. Deep field-by-field integrity comparison of all 31 original records
            $postOriginalRows = DB::connection($connectionName)->table('players')
                ->whereIn('id', $originalIds)
                ->get()
                ->keyBy('id');

            if ($postOriginalRows->count() !== 31) {
                throw new \RuntimeException("Integrity violation: Expected 31 original players post-import, but found " . $postOriginalRows->count() . ". Rolling back.");
            }

            $requiredFields = [
                'id',
                'name',
                'shirt_number',
                'jersey_size',
                'payment_status',
                'notes',
                'created_at',
                'updated_at',
            ];

            foreach ($originalSnapshot as $id => $preRow) {
                if (!$postOriginalRows->has($id)) {
                    throw new \RuntimeException("Integrity violation: Original player id={$id} missing after import. Rolling back.");
                }

                $postRow = (array)$postOriginalRows->get($id);
                foreach ($requiredFields as $field) {
                    if (!array_key_exists($field, $postRow)) {
                        throw new \RuntimeException("Integrity violation: Column '{$field}' missing on post-import player id={$id}. Rolling back.");
                    }
                    $preVal = $preRow[$field] ?? null;
                    $postVal = $postRow[$field] ?? null;
                    if (!$this->arePlayerFieldsEqual($field, $preVal, $postVal)) {
                        throw new \RuntimeException("Integrity violation: Original player id={$id} field '{$field}' was modified during import. Rolling back.");
                    }
                }
            }

            // 12. Verify post-import total count is exactly 93
            $finalCount = DB::connection($connectionName)->table('players')->count();
            if ($finalCount !== 93) {
                throw new \RuntimeException("Post-import count mismatch: expected 93, found {$finalCount}. Rolling back.");
            }

            // 13. Commit transaction
            DB::connection($connectionName)->commit();

            return response()->json([
                'mode' => 'LIVE_EXECUTION',
                'status' => 'SUCCESS',
                'message' => '62 players successfully imported into production TiDB.',
                'baseline_count' => $initialCount,
                'records_inserted' => $inserted,
                'final_count' => $finalCount
            ], 200);

        } catch (\Throwable $e) {
            $isIntegrityError = str_starts_with($e->getMessage(), 'Integrity violation:');
            if ($isIntegrityError) {
                \Illuminate\Support\Facades\Log::error('Import integrity violation: ' . get_class($e) . ' - ' . $e->getMessage());
            } else {
                \Illuminate\Support\Facades\Log::error('Import transaction failure: ' . get_class($e));
            }

            // 1. Determine whether a transaction is active on this connection
            $rollbackAttempted = false;
            $rollbackSuccess = false;

            if (DB::connection($connectionName)->transactionLevel() > 0) {
                $rollbackAttempted = true;
                try {
                    $this->executeRollback();
                    $rollbackSuccess = true;
                } catch (\Throwable $rbEx) {
                    $rollbackSuccess = false;
                    \Illuminate\Support\Facades\Log::error('Import rollback failed: ' . get_class($rbEx));
                }
            }

            // 2. If rollback was required but failed, fail closed immediately
            if ($rollbackAttempted && !$rollbackSuccess) {
                return response()->json([
                    'error' => 'IMPORT_ROLLBACK_FAILED',
                    'message' => 'Critical error: Import transaction rollback failed. System state reset was not confirmed.'
                ], 500);
            }

            // 3. If rollback succeeded, return appropriate failure response
            if ($rollbackAttempted && $rollbackSuccess) {
                if ($isIntegrityError) {
                    return response()->json([
                        'error' => 'ORIGINAL_RECORD_INTEGRITY_VIOLATION',
                        'message' => 'Import transaction failed and rolled back: Original baseline player records integrity check failed.'
                    ], 409);
                }

                return response()->json([
                    'error' => 'TRANSACTION_FAILED',
                    'message' => 'Import transaction failed and rolled back completely due to a database error.'
                ], 500);
            }

            // 4. If no transaction was active, do not claim that rollback occurred
            return response()->json([
                'error' => 'IMPORT_EXECUTION_FAILED',
                'message' => 'Import execution failed due to an error. No active transaction was open.'
            ], 500);
        }
    }

    /**
     * Compare field values ensuring type safety and distinguishing NULL from empty string.
     *
     * Rules:
     * 1. Strict NULL handling: NULL is only equal to NULL. NULL is NEVER equal to empty string '' or 0.
     * 2. Numeric handling: Handles PDO stringified integers (e.g. '1' vs 1) safely without false mismatches.
     * 3. Timestamps & strings: Strict string representation comparison.
     */
    private function arePlayerFieldsEqual(string $field, $val1, $val2): bool
    {
        // 1. Strict NULL comparison (NULL !== '')
        if ($val1 === null && $val2 === null) {
            return true;
        }
        if ($val1 === null || $val2 === null) {
            return false;
        }

        // 2. Numeric integer fields (handles PDO stringified integers like '1' vs 1)
        if (in_array($field, ['id', 'shirt_number'], true)) {
            if (!is_numeric($val1) || !is_numeric($val2)) {
                return false;
            }
            return (int)$val1 === (int)$val2;
        }

        // 3. String & timestamp fields (strict string equality)
        return (string)$val1 === (string)$val2;
    }

    /**
     * TiDB Production Compatibility Diagnostic (Phase 18/20).
     *
     * SAFETY GUARANTEES:
     * 1. Protected by auth:sanctum, strict admin email verification, and feature flag.
     * 2. Zero player records are inserted, updated, deleted, or altered.
     * 3. Zero sensitive credentials or connection strings are returned.
     * 4. Tier 1 executes read-only checks without opening any transaction.
     * 5. Tier 2 tests SET TRANSACTION ISOLATION LEVEL READ COMMITTED and lockForUpdate on players.id = 1.
     * 6. Tier 2 attempts transaction rollback in a finally block; rollback failure is treated
     *    as diagnostic failure and fails closed. Does not claim an absolute guarantee against process death.
     * 7. Any failure in Tier 1 or Tier 2 returns a failed diagnostic result with sanitized error messages.
     */
    public function verifyTidb(Request $request): JsonResponse
    {
        // 1. Diagnostic feature flag guard (defaults to false, fails closed)
        $isFeatureEnabled = config('maintenance.enable_tidb_diagnostic') === true;
        if (!$isFeatureEnabled) {
            return response()->json([
                'status' => 'FAILED',
                'error' => 'FEATURE_DISABLED',
                'message' => 'The TiDB compatibility diagnostic feature is currently disabled.'
            ], 403);
        }

        // 2. Administrator identity check (fails closed, zero hardcoded fallback)
        $configuredAdmin = config('maintenance.admin_email');
        if (empty($configuredAdmin) || !is_string($configuredAdmin) || trim($configuredAdmin) === '') {
            return response()->json([
                'status' => 'FAILED',
                'error' => 'ADMIN_EMAIL_NOT_CONFIGURED',
                'message' => 'Production administrator identity (ADMIN_EMAIL) is not configured. Request rejected.'
            ], 500);
        }

        $authenticatedUser = $request->user();
        if (!$authenticatedUser || empty($authenticatedUser->email)) {
            return response()->json([
                'status' => 'FAILED',
                'error' => 'UNAUTHENTICATED',
                'message' => 'Authentication required.'
            ], 401);
        }

        if (strtolower(trim($authenticatedUser->email)) !== strtolower(trim($configuredAdmin))) {
            return response()->json([
                'status' => 'FAILED',
                'error' => 'FORBIDDEN',
                'message' => 'The authenticated user does not match the configured administrator identity.'
            ], 403);
        }

        $connectionName = DB::getDefaultConnection();
        $this->activeConnection = $connectionName;
        $isMySql = $this->isMySqlDriver();

        // Prevent running if another transaction is already open on this connection
        if (DB::connection($connectionName)->transactionLevel() > 0) {
            return response()->json([
                'status' => 'FAILED',
                'error' => 'ACTIVE_TRANSACTION_EXISTS',
                'message' => 'An active transaction already exists on the database connection. Diagnostic aborted.'
            ], 409);
        }

        // =====================================================================
        // TIER 1: READ-ONLY CHECKS (Zero transactions, zero locks, zero writes)
        // =====================================================================
        $tier1Checks = [];

        // 1. SELECT 1 ping
        try {
            $pingOk = $this->executePing();
            $tier1Checks['database_ping'] = $pingOk ? 'OK' : 'FAILED';
            if (!$pingOk) {
                return response()->json([
                    'status' => 'FAILED',
                    'stage' => 'TIER_1_READ_ONLY',
                    'error' => 'DATABASE_PING_FAILED',
                    'message' => 'Database ping (SELECT 1) failed.',
                    'tier1_checks' => $tier1Checks
                ], 500);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Database ping check failed: ' . get_class($e));
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_1_READ_ONLY',
                'error' => 'DATABASE_PING_FAILED',
                'message' => 'Database connection ping check failed.',
                'tier1_checks' => ['database_ping' => 'FAILED']
            ], 500);
        }

        // 2. Active Database Name
        try {
            $dbName = $this->getActiveDatabaseName();
            $tier1Checks['active_database'] = $dbName ?? 'unknown';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Active database query failed: ' . get_class($e));
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_1_READ_ONLY',
                'error' => 'DATABASE_NAME_FAILED',
                'message' => 'Unable to determine active database name.',
                'tier1_checks' => $tier1Checks
            ], 500);
        }

        // 3. Active Connection ID
        try {
            $connId = $this->getActiveConnectionId();
            $tier1Checks['connection_id'] = $connId;
            if ($isMySql && $connId === null) {
                return response()->json([
                    'status' => 'FAILED',
                    'stage' => 'TIER_1_READ_ONLY',
                    'error' => 'CONNECTION_ID_FAILED',
                    'message' => 'Unable to retrieve database connection ID.',
                    'tier1_checks' => $tier1Checks
                ], 500);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Connection ID query failed: ' . get_class($e));
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_1_READ_ONLY',
                'error' => 'CONNECTION_ID_FAILED',
                'message' => 'Unable to retrieve database connection ID.',
                'tier1_checks' => $tier1Checks
            ], 500);
        }

        // 4. TiDB Transaction Mode (Must be pessimistic)
        $tidbMode = $this->getTidbTxnMode();
        if ($isMySql) {
            if ($tidbMode === null || strtolower(trim($tidbMode)) !== 'pessimistic') {
                $tier1Checks['tidb_txn_mode'] = $tidbMode ?? 'UNREADABLE';
                return response()->json([
                    'status' => 'FAILED',
                    'stage' => 'TIER_1_READ_ONLY',
                    'error' => 'UNSUPPORTED_TRANSACTION_MODE',
                    'message' => "TiDB transaction mode must be 'pessimistic', found '" . ($tidbMode ?? 'NULL') . "'. Diagnostic halted before Tier 2.",
                    'tier1_checks' => $tier1Checks
                ], 409);
            }
            $tier1Checks['tidb_txn_mode'] = 'pessimistic';
        } else {
            // Non-MySQL driver (e.g. SQLite test environment)
            if ($tidbMode !== null && strtolower(trim($tidbMode)) !== 'pessimistic') {
                $tier1Checks['tidb_txn_mode'] = $tidbMode;
                return response()->json([
                    'status' => 'FAILED',
                    'stage' => 'TIER_1_READ_ONLY',
                    'error' => 'UNSUPPORTED_TRANSACTION_MODE',
                    'message' => "Transaction mode must be 'pessimistic', found '{$tidbMode}'. Diagnostic halted before Tier 2.",
                    'tier1_checks' => $tier1Checks
                ], 409);
            }
            $tier1Checks['tidb_txn_mode'] = $tidbMode ?? 'SKIPPED_ON_SQLITE';
        }

        // 5. Baseline Player Count (Must be strictly 31)
        try {
            $playerCount = DB::connection($connectionName)->table('players')->count();
            $tier1Checks['baseline_player_count'] = $playerCount;
            if ($playerCount !== 31) {
                return response()->json([
                    'status' => 'FAILED',
                    'stage' => 'TIER_1_READ_ONLY',
                    'error' => 'BASELINE_COUNT_MISMATCH',
                    'message' => "Expected exactly 31 baseline players, but found {$playerCount}. Diagnostic halted before Tier 2.",
                    'tier1_checks' => $tier1Checks
                ], 409);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Player count query failed: ' . get_class($e));
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_1_READ_ONLY',
                'error' => 'PLAYER_COUNT_QUERY_FAILED',
                'message' => 'Failed to query player baseline count.',
                'tier1_checks' => $tier1Checks
            ], 500);
        }

        // 6. Mutex row existence (players.id = 1)
        try {
            $mutexRow = DB::connection($connectionName)->table('players')->where('id', 1)->first();
            $mutexExists = ($mutexRow !== null);
            $tier1Checks['mutex_row_exists'] = $mutexExists;
            if (!$mutexExists) {
                return response()->json([
                    'status' => 'FAILED',
                    'stage' => 'TIER_1_READ_ONLY',
                    'error' => 'MUTEX_ROW_NOT_FOUND',
                    'message' => 'Mutex anchor row (players.id = 1) was not found. Diagnostic halted before Tier 2.',
                    'tier1_checks' => $tier1Checks
                ], 409);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Mutex row query failed: ' . get_class($e));
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_1_READ_ONLY',
                'error' => 'MUTEX_ROW_QUERY_FAILED',
                'message' => 'Failed to query mutex anchor row.',
                'tier1_checks' => $tier1Checks
            ], 500);
        }

        // =====================================================================
        // TIER 2: TRANSACTION & LOCK VERIFICATION (Atomic Test with Immediate Rollback)
        // =====================================================================
        $tier2Checks = [];
        $tier2Error = false;
        $isolationDirectiveIssued = false;
        $transactionStarted = false;
        $connectionResetAttempted = false;
        $connectionResetSuccess = false;
        $rollbackAttempted = false;
        $rollbackSuccess = false;

        try {
            // 1. Record CONNECTION_ID before starting transaction
            $connIdBefore = $this->getActiveConnectionId();

            // 2. Set transaction isolation level for upcoming transaction
            $isolationDirectiveIssued = $this->executeIsolationDirective();

            // 3. Start transaction
            $this->executeBeginTransaction();
            $transactionStarted = true;

            // 4. Verify connection ID has not changed
            $connIdInside = $this->getActiveConnectionId();

            $connectionInvalid = false;
            if ($isMySql) {
                if ($connIdBefore === null || $connIdInside === null || $connIdBefore !== $connIdInside) {
                    $connectionInvalid = true;
                }
            } else {
                if ($connIdBefore !== $connIdInside) {
                    $connectionInvalid = true;
                }
            }

            if ($connectionInvalid) {
                // Discard/reset the affected connection
                $connectionResetAttempted = true;
                $connectionResetSuccess = $this->resetConnection($connectionName);
                throw new \RuntimeException("Connection instance unverified or changed between SET TRANSACTION and beginTransaction.");
            }
            $tier2Checks['connection_id_consistent'] = true;

            // 5. Inspect transaction isolation level
            if ($isMySql) {
                $rawIso = $this->getActiveIsolationLevel();
                $normalizedIso = str_replace('-', ' ', strtoupper(trim($rawIso ?? '')));
                if ($normalizedIso !== 'READ COMMITTED') {
                    throw new \RuntimeException("Expected transaction isolation 'READ COMMITTED', found unexpected level.");
                }
                $tier2Checks['isolation_level_applied'] = 'READ COMMITTED';
            } else {
                $tier2Checks['isolation_level_applied'] = 'SKIPPED_ON_SQLITE';
            }

            // 6. Test lockForUpdate() on mutex row (players.id = 1)
            $lockedRow = DB::connection($connectionName)->table('players')->where('id', 1)->lockForUpdate()->first();
            if (!$lockedRow) {
                throw new \RuntimeException("Mutex anchor row (players.id = 1) could not be locked.");
            }
            $tier2Checks['mutex_lock_acquired'] = true;

        } catch (\Throwable $e) {
            $tier2Error = true;
            \Illuminate\Support\Facades\Log::error('Tier 2 diagnostic exception: ' . get_class($e));
        } finally {
            if (DB::connection($connectionName)->transactionLevel() > 0) {
                $rollbackAttempted = true;
                try {
                    $this->executeRollback();
                    $rollbackSuccess = true;
                } catch (\Throwable $rbEx) {
                    $rollbackSuccess = false;
                    \Illuminate\Support\Facades\Log::error('Rollback failed in Tier 2: ' . get_class($rbEx));
                }
            } elseif ($isolationDirectiveIssued && !$transactionStarted) {
                // Isolation directive was issued, but transaction never started. Purge connection to prevent leakage.
                $connectionResetAttempted = true;
                $connectionResetSuccess = $this->resetConnection($connectionName);
            }
        }

        // If rollback was required but failed, fail closed immediately
        if ($rollbackAttempted && !$rollbackSuccess) {
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_2_ROLLBACK',
                'error' => 'ROLLBACK_FAILED',
                'message' => 'Critical error: Diagnostic transaction rollback failed. System state reset was not confirmed.',
                'tier1_checks' => $tier1Checks,
                'tier2_checks' => $tier2Checks
            ], 500);
        }

        // If connection reset was required but failed, fail closed immediately
        if ($connectionResetAttempted && !$connectionResetSuccess) {
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_2_CLEANUP',
                'error' => 'CONNECTION_RESET_FAILED',
                'message' => 'Pending transaction directive cleanup failed. Connection could not be safely reset.',
                'tier1_checks' => $tier1Checks,
                'tier2_checks' => $tier2Checks
            ], 500);
        }

        $tier2Checks['transaction_rolled_back'] = $rollbackSuccess || !$rollbackAttempted;

        // If an error occurred inside Tier 2
        if ($tier2Error) {
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_2_TRANSACTION_LOCK',
                'error' => 'TIER_2_VERIFICATION_FAILED',
                'message' => 'Tier 2 transaction or lock verification failed. Transaction was rolled back safely.',
                'tier1_checks' => $tier1Checks,
                'tier2_checks' => $tier2Checks
            ], 409);
        }

        // Post-rollback count verification (must strictly remain 31)
        $postCount = DB::connection($connectionName)->table('players')->count();
        $tier2Checks['post_rollback_player_count'] = $postCount;
        if ($postCount !== 31) {
            return response()->json([
                'status' => 'FAILED',
                'stage' => 'TIER_2_TRANSACTION_LOCK',
                'error' => 'INTEGRITY_TAMPERED',
                'message' => "Post-diagnostic player count is {$postCount}, expected strictly 31.",
                'tier1_checks' => $tier1Checks,
                'tier2_checks' => $tier2Checks
            ], 500);
        }

        return response()->json([
            'status' => 'PASS',
            'stage' => 'COMPLETED',
            'message' => 'TiDB production compatibility diagnostic passed 100%.',
            'tier1_checks' => $tier1Checks,
            'tier2_checks' => $tier2Checks
        ], 200);
    }

    /**
     * Helper to determine whether the active connection is MySQL or MariaDB.
     */
    protected function isMySqlDriver(): bool
    {
        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        $driver = DB::connection($conn)->getDriverName();
        return in_array($driver, ['mysql', 'mariadb'], true);
    }

    /**
     * Helper to read TiDB transaction mode safely.
     */
    protected function getTidbTxnMode(): ?string
    {
        if (!$this->isMySqlDriver()) {
            return null;
        }

        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        try {
            $row = DB::connection($conn)->selectOne("SELECT @@tidb_txn_mode as mode");
            return $row ? ($row->mode ?? null) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Helper to retrieve connection ID safely.
     */
    protected function getActiveConnectionId(): ?int
    {
        if (!$this->isMySqlDriver()) {
            return 1;
        }

        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        try {
            $row = DB::connection($conn)->selectOne("SELECT CONNECTION_ID() as conn_id");
            return $row ? (int)($row->conn_id ?? 0) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Helper to execute SELECT 1 ping.
     */
    protected function executePing(): bool
    {
        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        $pingRow = DB::connection($conn)->selectOne("SELECT 1 as ping");
        return ($pingRow && ((int)($pingRow->ping ?? 0) === 1));
    }

    /**
     * Helper to retrieve active database name safely.
     */
    protected function getActiveDatabaseName(): ?string
    {
        if (!$this->isMySqlDriver()) {
            return 'sqlite';
        }

        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        try {
            $row = DB::connection($conn)->selectOne("SELECT DATABASE() as db_name");
            return $row ? ($row->db_name ?? 'unknown') : 'unknown';
        } catch (\Throwable $e) {
            return 'unknown';
        }
    }

    /**
     * Helper to start a transaction safely.
     */
    protected function executeBeginTransaction(): void
    {
        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        DB::connection($conn)->beginTransaction();
    }

    /**
     * Helper to execute rollback safely.
     */
    protected function executeRollback(): void
    {
        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        DB::connection($conn)->rollBack();
    }

    /**
     * Safely roll back active transaction and return JSON response.
     * If rollback fails, fails closed with HTTP 500 and sanitized error.
     */
    protected function rollbackAndRespond(string $error, string $message, int $status = 409, array $extra = []): JsonResponse
    {
        $rollbackSuccess = false;
        try {
            $this->executeRollback();
            $rollbackSuccess = true;
        } catch (\Throwable $rbEx) {
            \Illuminate\Support\Facades\Log::error('Import early rollback failed: ' . get_class($rbEx));
        }

        if (!$rollbackSuccess) {
            return response()->json([
                'error' => 'IMPORT_ROLLBACK_FAILED',
                'message' => 'Critical error: Import transaction rollback failed. System state reset was not confirmed.'
            ], 500);
        }

        $payload = array_merge([
            'error' => $error,
            'message' => $message,
        ], $extra);

        return response()->json($payload, $status);
    }

    /**
     * Helper to set transaction isolation level directive.
     */
    protected function executeIsolationDirective(): bool
    {
        if ($this->isMySqlDriver()) {
            $conn = $this->activeConnection ?? DB::getDefaultConnection();
            DB::connection($conn)->statement("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
            return true;
        }
        return false;
    }

    /**
     * Helper to read active isolation level safely.
     */
    protected function getActiveIsolationLevel(): ?string
    {
        $conn = $this->activeConnection ?? DB::getDefaultConnection();
        try {
            $isoRow = DB::connection($conn)->selectOne("SELECT @@transaction_isolation as iso");
            return $isoRow ? ($isoRow->iso ?? null) : null;
        } catch (\Throwable $e) {
            try {
                $isoRow = DB::connection($conn)->selectOne("SELECT @@tx_isolation as iso");
                return $isoRow ? ($isoRow->iso ?? null) : null;
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }

    /**
     * Safely discard/reset the database connection to purge any pending session directives.
     */
    protected function resetConnection(?string $connectionName = null): bool
    {
        $target = $connectionName ?? $this->activeConnection ?? DB::getDefaultConnection();
        try {
            DB::disconnect($target);
            DB::purge($target);
            DB::reconnect($target);
            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Connection purge/reset failed: ' . get_class($e));
            return false;
        }
    }
}
