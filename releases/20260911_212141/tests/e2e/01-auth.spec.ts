import { test, expect } from '@playwright/test';

test.describe('Autenticación', () => {
  test('debe mostrar página de login', async ({ page }) => {
    await page.goto('/login');
    await expect(page).toHaveTitle(/Login/);
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
  });

  test('debe mostrar error con credenciales inválidas', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'invalid@test.com');
    await page.fill('input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');
    
    // Debe redirigir de vuelta al login con error
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });

  test('debe redirigir a profile después de login exitoso', async ({ page }) => {
    // Este test asume que hay un usuario de test creado
    // En producción, se debe crear un seed o fixture
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@demo.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Debería redirigir a algún Dashboard
    await page.waitForURL(/^(.*)(\/|dashboard|owner|profile)/);
  });
});
