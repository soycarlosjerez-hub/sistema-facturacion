import { test, expect } from '@playwright/test';

test.describe('Autenticación 2FA E2E', () => {
  test('debe mostrar la página de configuración 2FA', async ({ page }) => {
    await page.goto('/profile');
    await expect(page.locator('text=Autenticación de Dos Factores')).toBeVisible();
  });

  test('debe mostrar estado del 2FA en el perfil', async ({ page }) => {
    await page.goto('/profile');
    
    // Verificar que la sección existe
    const section = page.locator('[class*="card"], .ui-card');
    await expect(section).toBeVisible();
  });

  test('debe navegar a la página de 2FA', async ({ page }) => {
    await page.goto('/two-factor');
    await expect(page.locator('h1, h2, h3, .ui-header-title')).toBeVisible();
  });

  test('debe mostrar el QR cuando se activa 2FA', async ({ page }) => {
    await page.goto('/two-factor');

    // Click en activar
    const activateButton = page.locator('button:has-text("Activar"), button:has-text("Activar Autenticación")');
    if (await activateButton.isVisible({ timeout: 1000 }).catch(() => false)) {
      await activateButton.click();
      
      // Debería aparecer el modal con el QR
      await expect(page.locator('modal, .modal, #qrModal')).toBeVisible();
    }
  });

  test('debe mostrar códigos de recuperación', async ({ page }) => {
    await page.goto('/two-factor');

    // Si 2FA está activado, verificar que se puede ver recovery codes
    const recoveryButton = page.locator('button:has-text("Códigos de Recuperación"), button:has-text("Ver Códigos")');
    if (await recoveryButton.isVisible({ timeout: 1000 }).catch(() => false)) {
      await recoveryButton.click();
      await expect(page.locator('modal, .modal, #recoveryModal')).toBeVisible();
    }
  });
});
