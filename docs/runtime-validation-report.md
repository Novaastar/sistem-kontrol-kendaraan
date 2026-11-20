# White-Box Testing Lab Evidence

**Generated:** 2026-11-20  
**Selected feature:** driver trip updates through `POST /api/update_perjalanan.php`

## Result summary

| Test tier | Framework | Status | Exit code | Results |
|---|---|---|---:|---|
| Unit | PHPUnit 12.5.33 | PASS | 0 | 7 tests, 14 assertions; service tested with Stub and Spy repositories. |
| Integration | PHPUnit 12.5.33 | PASS | 0 | 8 tests, 25 assertions; real HTTP endpoint and isolated MySQL. |
| Integration | Pest 4.7.8 | PASS | 0 | 8 tests, 25 assertions; same endpoint scenarios and isolated MySQL. |
| PHPUnit combined suite | PHPUnit 12.5.33 | PASS | 0 | 15 tests, 39 assertions. |

Pest is built on PHPUnit, so the Pest comparison describes test syntax/API rather than an independent test engine.

## Why both unit and integration tests are used

The original endpoint combines HTTP request handling, authentication through shared global helpers, a global `mysqli` connection, database writes, and `json_out()` termination. Testing that endpoint as an isolated unit would require bootstrapping those global/process-level dependencies. For the Lab Session, the feature rules were extracted to `api/TripUpdateService.php` behind the `TripUpdateRepository` interface. This makes the business behavior unit-testable without HTTP or a database. The PHPUnit unit tests replace that repository with a Stub or Spy.

The real endpoint is still exercised by integration tests because unit doubles cannot verify bearer-token handling, HTTP status/JSON behavior, SQL compatibility, or persistence. Integration fixtures use an isolated MySQL data directory on port 3317, unique rows, and cleanup after each test; they do not use the application database on port 3306.

## Test-double implementation

| Type | Implementation | Why selected |
|---|---|---|
| Stub | `TripUpdateRepositoryStub` in `tests/api/TripUpdateServiceTest.php` | Returns a predetermined owned-trip row (or no row) so the unit test can verify odometer rejection and not-found behavior without database access. Its write methods fail if a rejected request attempts persistence. |
| Spy | `TripUpdateRepositorySpy` in `tests/api/TripUpdateServiceTest.php` | Records repository calls and arguments so the unit test can verify the exact trip fields persisted and the `berjalan` status transition for trip start. |

## Test files and commands

- Unit tests: `tests/api/TripUpdateServiceTest.php`
- PHPUnit integration tests: `tests/api/UpdatePerjalananIntegrationTest.php`
- Pest integration tests: `tests/api/UpdatePerjalananPestTest.php`
- Shared isolated fixtures: `tests/api/TripUpdateTestContext.php`
- Production service and repository adapter: `api/TripUpdateService.php`, `api/MysqliTripUpdateRepository.php`
- Test configuration: `phpunit.xml`

```powershell
composer test:unit
# Requires Xdebug enabled for CLI with coverage mode.
composer test:unit:coverage
# One-off alternative when loading the matching Windows DLL directly:
$env:XDEBUG_MODE='coverage'
php -d zend_extension="C:\path\php_xdebug.dll" vendor\bin\phpunit --testsuite unit --path-coverage --colors=never --coverage-text=docs\coverage-report\phpunit-unit.txt
$env:SKK_TEST_DB_PORT='3317'
$env:SKK_TEST_API_URL='http://127.0.0.1:8083/api/update_perjalanan.php'
composer test:phpunit
composer test:pest
```

Environment: PHP 8.4.14, PHPUnit 12.5.33, Pest 4.7.8, PHP built-in server, and isolated MySQL 8.0.30 initialized from `database.sql`.

## GitHub evidence

- Testing branch: `https://github.com/Novaastar/sistem-kontrol-kendaraan/tree/testing`
- Dedicated unit-test implementation commit: `https://github.com/Novaastar/sistem-kontrol-kendaraan/commit/cc07274376967263151bfdd345f4588e7feb1de8`

## Pre-Lab coverage boundary

Statement Coverage (42/42) and Branch Coverage (21/21 executable outcomes) in the Pre-Lab section are manual source/test-trace calculations for the endpoint before the Lab Session refactor. The attached PHPUnit coverage report is separate: Xdebug 3.5.3 was loaded for the PHPUnit process, and the report is filtered to the unit-tested `api/TripUpdateService.php` only. It reports 100.00% lines (35/35), 96.97% branches (32/33), 0.76% paths (9/1179), and 66.67% methods (2/3). The HTTP integration tests run in a separate PHP server process and are not included in this unit coverage report. The generated `--coverage-text` output is attached at `docs/coverage-report/phpunit-unit.txt` and rendered at the end of the White-Box document.
