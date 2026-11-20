# Runtime Validation Report

**Generated:** 2026-11-20  
**Target:** the single driver trip-update feature at `POST /api/update_perjalanan.php`

## Summary

| Framework | Status | Exit code | Results |
|---|---|---:|---|
| PHPUnit 12.5.33 | PASS | 0 | 8 tests, 25 assertions, 0 failures. |
| Pest 4.7.8 | PASS | 0 | 8 tests, 25 assertions, 0 failures. |

**Overall:** PASS for the selected feature's integration-test scope under both test syntaxes.

## Test evidence

- PHPUnit test source: `tests/api/UpdatePerjalananIntegrationTest.php`
- Pest test source: `tests/api/UpdatePerjalananPestTest.php`
- Shared isolated fixtures and HTTP/database helpers: `tests/api/TripUpdateTestContext.php`
- Suite configuration: `phpunit.xml`
- Command:
  ```powershell
  $env:SKK_TEST_DB_PORT='3317'
  $env:SKK_TEST_API_URL='http://127.0.0.1:8083/api/update_perjalanan.php'
  composer test
  ```
- Result: PHPUnit `OK (8 tests, 25 assertions)`; Pest `8 passed (25 assertions)`; process exit code 0.
- Environment: PHP 8.4.14, PHPUnit 12.5.33, Pest 4.7.8, PHP built-in server, and isolated MySQL 8.0.30 initialized from `database.sql`.
- Unique test fixtures are deleted after each test. Both suites call the real endpoint over HTTP and verify persisted values in MySQL.

## Framework comparison scope

Both frameworks execute the same eight scenarios for this one feature: missing/invalid token, assignment ownership, trip start, lower odometer rejection, empty update, trip finish with equal odometer, and partial note update. Pest is built on PHPUnit, so this is a comparison of the two test APIs/syntaxes, not two independent test engines.

## Scope and gaps

- The tests are integration tests using real HTTP and isolated MySQL. They do not use test doubles and do not claim unit-test coverage.
- PHPUnit/Pest coverage instrumentation was not enabled, so the Statement and Branch Coverage percentages in the Word document are manual source/test-trace calculations, not generated coverage reports.
- Docker was unavailable. The local MySQL server was isolated from the application's default database port.
- No production application code was changed for the test run.
