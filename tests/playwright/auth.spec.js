// @ts-check
import { test, expect } from '@playwright/test';

/**
 * ============================================================
 *  Test Suite: Autentikasi — Login & Register
 *  Base URL  : http://127.0.0.1:8000
 *
 *  Kredensial test (harus sudah ada di database):
 *    Email    : shafa2@gmail.com
 *    Password : shafa123
 * ============================================================
 */

const BASE = 'http://127.0.0.1:8000';

// ─────────────────────────────────────────────────────────────
// Helper: generate email unik untuk test registrasi
// ─────────────────────────────────────────────────────────────
function uniqueEmail() {
  return `testuser_${Date.now()}@mailtest.com`;
}

// ─────────────────────────────────────────────────────────────    
// GROUP: Halaman Login
// ─────────────────────────────────────────────────────────────
test.describe('Login', () => {

  test.beforeEach(async ({ page }) => {
    // Pastikan selalu mulai dari halaman login yang bersih
    await page.goto(`${BASE}/login`);
    await page.waitForLoadState('domcontentloaded');
  });

  test.afterEach(async ({ page }) => {
    // Jeda sebelum pindah ke test berikutnya
    await page.waitForTimeout(1500);
  });

  // TC-L01: Halaman login tampil dengan benar
  test('TC-L01 | Halaman login menampilkan form email dan password', async ({ page }) => {
    await expect(page).toHaveTitle(/CETAKU/i);
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  // TC-L02: Login berhasil dengan kredensial valid
  test('TC-L02 | Login berhasil dengan email dan password yang valid', async ({ page }) => {
    await page.fill('input[name="email"]', 'shafa2@gmail.com');
    await page.fill('input[name="password"]', 'shafa123');
    await page.click('button[type="submit"]');

    // Setelah login berhasil, tidak boleh ada di halaman /login lagi
    await page.waitForURL((url) => !url.pathname.includes('/login'), { timeout: 10000 });
    expect(page.url()).not.toContain('/login');
  });

  // TC-L03: Login gagal dengan password salah
  test('TC-L03 | Login gagal dan tampil pesan error jika password salah', async ({ page }) => {
    await page.fill('input[name="email"]', 'shafa2@gmail.com');
    await page.fill('input[name="password"]', 'passwordsalah999');
    await page.click('button[type="submit"]');

    // Harus tetap di halaman login
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('/login');

    // Harus muncul pesan error
    const errorMsg = page.locator('.alert-danger');
    await expect(errorMsg).toBeVisible();
  });

  // TC-L04: Login gagal dengan email yang tidak terdaftar
  test('TC-L04 | Login gagal jika email tidak terdaftar', async ({ page }) => {
    await page.fill('input[name="email"]', 'tidakada@example.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('/login');

    const errorMsg = page.locator('.alert-danger');
    await expect(errorMsg).toBeVisible();
  });

  // TC-L05: Validasi field kosong — browser HTML5 required
  test('TC-L05 | Form login tidak bisa submit jika field kosong', async ({ page }) => {
    // Klik submit tanpa isi apapun
    await page.click('button[type="submit"]');

    // Field email harus invalid (HTML5 validation)
    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);

    // Tetap di halaman login
    expect(page.url()).toContain('/login');
  });

  // TC-L06: Validasi format email tidak valid (tidak ada @)
  test('TC-L06a | Form login menolak email tanpa karakter @', async ({ page }) => {
    await page.fill('input[name="email"]', 'bukanemail');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
    expect(page.url()).toContain('/login');
  });

  test('TC-L06b | Form login menolak email dengan @ tapi tanpa domain', async ({ page }) => {
    await page.fill('input[name="email"]', 'test@');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
    expect(page.url()).toContain('/login');
  });

  test('TC-L06c | Form login menolak email tanpa bagian lokal (hanya @domain)', async ({ page }) => {
    await page.fill('input[name="email"]', '@domain.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
    expect(page.url()).toContain('/login');
  });

  // TC-L07: Tombol toggle visibility password berfungsi
  test('TC-L07 | Toggle show/hide password berfungsi', async ({ page }) => {
    const passwordInput = page.locator('input[name="password"]');
    const toggleBtn = page.locator('.password-toggle').first();

    // Awalnya type=password
    await expect(passwordInput).toHaveAttribute('type', 'password');

    // Klik toggle → jadi text
    await toggleBtn.click();
    await expect(passwordInput).toHaveAttribute('type', 'text');

    // Klik lagi → balik ke password
    await toggleBtn.click();
    await expect(passwordInput).toHaveAttribute('type', 'password');
  });

  // TC-L08: Link "Daftar sekarang" mengarah ke halaman register
  test('TC-L08 | Link "Daftar sekarang" mengarah ke halaman register', async ({ page }) => {
    await page.click('a[href*="register"]');
    await expect(page).toHaveURL(/\/register/);
  });

  // TC-L09: Link "Lupa password?" mengarah ke halaman reset password
  test('TC-L09 | Link "Lupa password?" mengarah ke halaman lupa-password', async ({ page }) => {
    await page.click('a.forgot-link');
    await expect(page).toHaveURL(/\/lupa-password/);
  });

  // TC-L10: Halaman protected redirect ke login jika belum auth
  test('TC-L10 | Halaman /profile redirect ke login jika belum login', async ({ page }) => {
    await page.goto(`${BASE}/profile`);
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('/login');
  });

});

// ─────────────────────────────────────────────────────────────
// GROUP: Halaman Register (Daftar)
// ─────────────────────────────────────────────────────────────
test.describe('Register', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/register`);
    await page.waitForLoadState('domcontentloaded');
  });

  test.afterEach(async ({ page }) => {
    // Jeda sebelum pindah ke test berikutnya
    await page.waitForTimeout(1500);
  });

  // TC-R01: Halaman register tampil dengan benar
  test('TC-R01 | Halaman register menampilkan semua field yang diperlukan', async ({ page }) => {
    await expect(page).toHaveTitle(/CETAKU/i);
    await expect(page.locator('input[name="nama"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('input[name="password_confirmation"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  // TC-R02: Registrasi berhasil dengan data valid
  test('TC-R02 | Registrasi berhasil dengan data yang valid', async ({ page }) => {
    const email = uniqueEmail();

    await page.fill('input[name="nama"]', 'User Test Playwright');
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'Password123!');
    await page.click('button[type="submit"]');

    // Setelah berhasil daftar, tidak boleh ada di /register
    await page.waitForURL((url) => !url.pathname.includes('/register'), { timeout: 10000 });
    expect(page.url()).not.toContain('/register');
  });

  // TC-R03: Validasi field nama kosong
  test('TC-R03 | Form register menolak submit jika nama kosong', async ({ page }) => {
    const email = uniqueEmail();

    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'Password123!');
    await page.click('button[type="submit"]');

    const namaInput = page.locator('input[name="nama"]');
    const isInvalid = await namaInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
    expect(page.url()).toContain('/register');
  });

  // TC-R04: Validasi field email kosong
  test('TC-R04 | Form register menolak submit jika email kosong', async ({ page }) => {
    await page.fill('input[name="nama"]', 'User Test');
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'Password123!');
    await page.click('button[type="submit"]');

    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
    expect(page.url()).toContain('/register');
  });

  // TC-R05: Validasi format email tidak valid
  test('TC-R05a | Form register menolak email tanpa karakter @', async ({ page }) => {
    await page.fill('input[name="nama"]', 'User Test');
    await page.fill('input[name="email"]', 'bukanemail');
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'Password123!');
    await page.click('button[type="submit"]');

    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
  });

  test('TC-R05b | Form register menolak email dengan @ tapi tanpa domain', async ({ page }) => {
    await page.fill('input[name="nama"]', 'User Test');
    await page.fill('input[name="email"]', 'test@');
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'Password123!');
    await page.click('button[type="submit"]');

    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
  });

  test('TC-R05c | Form register menolak email tanpa bagian lokal (hanya @domain)', async ({ page }) => {
    await page.fill('input[name="nama"]', 'User Test');
    await page.fill('input[name="email"]', '@domain.com');
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'Password123!');
    await page.click('button[type="submit"]');

    const emailInput = page.locator('input[name="email"]');
    const isInvalid = await emailInput.evaluate((el) => !el.validity.valid);
    expect(isInvalid).toBe(true);
  });

  // TC-R06: Validasi password kurang dari 8 karakter
  test('TC-R06 | Registrasi gagal jika password kurang dari 8 karakter', async ({ page }) => {
    const email = uniqueEmail();

    await page.fill('input[name="nama"]', 'User Test');
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', '123');
    await page.fill('input[name="password_confirmation"]', '123');
    await page.click('button[type="submit"]');

    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('/register');

    // Harus muncul pesan error dari server
    const errorMsg = page.locator('.alert-danger');
    await expect(errorMsg).toBeVisible();
  });

  // TC-R07: Validasi password confirmation tidak cocok
  test('TC-R07 | Registrasi gagal jika konfirmasi password tidak cocok', async ({ page }) => {
    const email = uniqueEmail();

    await page.fill('input[name="nama"]', 'User Test');
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'BedaPassword!');
    await page.click('button[type="submit"]');

    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('/register');

    // Error dari server Laravel (password_confirmation tidak cocok)
    const errorMsg = page.locator('.alert-danger');
    await expect(errorMsg).toBeVisible();
  });

  // TC-R08: Registrasi gagal jika email sudah terdaftar
  test('TC-R08 | Registrasi gagal jika email sudah terdaftar', async ({ page }) => {
    // Gunakan email yang sudah pasti ada
    await page.fill('input[name="nama"]', 'User Duplikat');
    await page.fill('input[name="email"]', 'shafa2@gmail.com');
    await page.fill('input[name="password"]', 'Password123!');
    await page.fill('input[name="password_confirmation"]', 'Password123!');
    await page.click('button[type="submit"]');

    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('/register');

    // Error email sudah dipakai
    const errorMsg = page.locator('.alert-danger');
    await expect(errorMsg).toBeVisible();
  });

  // TC-R09: Toggle show/hide password di form register berfungsi
  test('TC-R09 | Toggle show/hide password di register berfungsi', async ({ page }) => {
    const passwordInput = page.locator('input[name="password"]');
    const toggleBtn = page.locator('#toggleIconPassword').locator('..');

    await expect(passwordInput).toHaveAttribute('type', 'password');
    await toggleBtn.click();
    await expect(passwordInput).toHaveAttribute('type', 'text');
    await toggleBtn.click();
    await expect(passwordInput).toHaveAttribute('type', 'password');
  });

  // TC-R10: Link "Masuk sekarang" mengarah ke halaman login
  test('TC-R10 | Link "Masuk sekarang" mengarah ke halaman login', async ({ page }) => {
    // Gunakan selector spesifik ke link teks "Masuk sekarang", bukan tombol Google
    await page.click('.login-link a');
    await expect(page).toHaveURL(/\/login$/);
  });

});
