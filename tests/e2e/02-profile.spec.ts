import { test, expect } from '@playwright/test';

test.describe('Perfil de Usuario', () => {
  test('debe mostrar perfil del usuario', async ({ page }) => {
    await page.goto('/profile');
    
    // Verificar que los elementos clave estén presentes
    await expect(page.locator('h4, h1, .ui-header-title')).toBeVisible();
    
    // Verificar sección de seguridad (2FA)
    await expect(page.locator('[data-testid="2fa-section"], .ui-card')).toBeVisible();
  });

  test('debe mostrar sección de 2FA', async ({ page }) => {
    await page.goto('/profile');
    
    // Buscar la sección de 2FA por texto
    const twoFactorSection = page.locator('text=Autenticación de Dos Factores');
    await expect(twoFactorSection).toBeVisible();
    
    // Verificar botón de activar 2FA
    const enableButton = page.locator('text=Activar 2FA');
    await expect(enableButton).toBeVisible();
  });
});
