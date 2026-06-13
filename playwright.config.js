// @ts-check
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  // Folder tempat test berada
  testDir: './tests/playwright',

  // Timeout per test
  timeout: 40 * 1000,

  // Timeout untuk expect/assertion
  expect: {
    timeout: 5000,
  },

  // Jalankan test secara paralel
  fullyParallel: false,

  // Gagalkan build jika ada test.only tertinggal di CI
  forbidOnly: !!process.env.CI,

  // Retry saat gagal di CI
  retries: process.env.CI ? 1 : 0,

  // Jumlah worker
  workers: 1,

  // Format reporter
  reporter: [
    ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ['list'],
  ],

  use: {
    // Base URL aplikasi Laravel
    baseURL: 'http://127.0.0.1:8000',

    // Simpan screenshot saat test gagal
    screenshot: 'only-on-failure',

    // Simpan trace saat retry pertama
    trace: 'on-first-retry',

    // Headless mode (false = buka browser, true = background)
    headless: false,

    // Delay antar aksi (ms) — supaya bisa mengikuti tiap langkah test
    launchOptions: {
      slowMo: 1200,
    },

    // Viewport standar
    viewport: { width: 1280, height: 720 },
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
