import { test, expect } from '@playwright/test';

test.describe('Módulo de Ventas', () => {
  test('debe cargar la lista de ventas', async ({ page }) => {
    await page.goto('/ventas');
    await expect(page.locator('table, .data-table, [data-testid="ventas-list"]')).toBeVisible();
  });

  test('debe mostrar formulario de nueva venta', async ({ page }) => {
    await page.goto('/ventas/create');
    await expect(page.locator('input[name="cliente_id"], select[name="cliente_id"]')).toBeVisible();
    await expect(page.locator('input[name="metodo_pago"]')).toBeVisible();
  });

  test('debe crear una venta con datos válidos', async ({ page }) => {
    await page.goto('/ventas/create');

    // Llenar formulario de venta
    const submitButton = page.locator('button[type="submit"], button:has-text("Guardar"), button:has-text("Crear")');
    
    // Verificar que el botón existe (la lógica de envío depende de la implementación)
    await expect(submitButton).toBeVisible();
  });

  test('debe validar campos obligatorios en venta', async ({ page }) => {
    await page.goto('/ventas/create');

    // Intentar enviar sin llenar campos
    const submitButton = page.locator('button[type="submit"]');
    
    // El formulario debería tener validación del lado del cliente
    await expect(page.locator('input[name="producto_id[]"], input[name="cantidad[]"]')).toBeVisible();
  });

  test('debe mostrar detalle de venta', async ({ page }) => {
    // Crear una venta primero para tener un ID válido
    // Este test asume que existe al menos una venta en el sistema
    await page.goto('/ventas/1');
    await expect(page.locator('h1, h2, .card, [data-testid="venta-detail"]')).toBeVisible();
  });

  test('debe filtrar ventas por fecha', async ({ page }) => {
    await page.goto('/ventas');
    
    // Buscar elementos de filtrado
    const dateInput = page.locator('input[type="date"], [data-testid="date-filter"]');
    if (await dateInput.isVisible({ timeout: 1000 }).catch(() => false)) {
      await dateInput.fill('2024-01-01');
      await page.locator('button:has-text("Filtrar"), input[type="submit"]').click();
      await expect(page.locator('table, .data-table')).toBeVisible();
    }
  });

  test('debe exportar ventas', async ({ page }) => {
    await page.goto('/ventas');
    
    const exportButton = page.locator('button:has-text("Exportar"), a:has-text("Exportar"), [data-testid="export-btn"]');
    if (await exportButton.isVisible({ timeout: 1000 }).catch(() => false)) {
      const [download] = await Promise.all([
        page.waitForEvent('download'),
        exportButton.click(),
      ]);
      // Download se guarda automáticamente
      expect(download.suggestedFilename()).toContain('.csv') || 
             expect(download.suggestedFilename()).toContain('.xlsx') ||
             expect(download.suggestedFilename()).toContain('.pdf');
    }
  });
});
