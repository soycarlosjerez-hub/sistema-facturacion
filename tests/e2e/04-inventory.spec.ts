import { test, expect } from '@playwright/test';

test.describe('Módulo de Inventario', () => {
  test('debe cargar la lista de productos', async ({ page }) => {
    await page.goto('/productos');
    await expect(page.locator('table, .data-table, [data-testid="productos-list"]')).toBeVisible();
  });

  test('debe mostrar formulario de nuevo producto', async ({ page }) => {
    await page.goto('/productos/create');
    await expect(page.locator('input[name="nombre"], input[name="sku"], input[name="precio"]')).toBeVisible();
  });

  test('debe crear un producto con datos válidos', async ({ page }) => {
    await page.goto('/productos/create');

    // Llenar formulario
    await page.fill('input[name="nombre"]', 'Producto Test');
    await page.fill('input[name="sku"]', 'PROD-TEST-001');
    await page.fill('input[name="precio"]', '1000');
    await page.fill('input[name="costo"]', '500');
    await page.fill('input[name="stock"]', '50');

    await page.locator('button[type="submit"]').click();

    // Debería redirigir a la lista de productos con mensaje de éxito
    await expect(page.locator('alert-success, .alert-success, [data-testid="success-msg"]')).toBeVisible();
  });

  test('debe validar campos obligatorios en producto', async ({ page }) => {
    await page.goto('/productos/create');

    // Intentar enviar sin llenar campos obligatorios
    await page.locator('button[type="submit"]').click();

    // Debería mostrar errores de validación
    await expect(page.locator('invalid-feedback, .error, [data-testid="error-msg"]')).toBeVisible();
  });

  test('debe buscar productos', async ({ page }) => {
    await page.goto('/productos');

    // Buscar producto por nombre o SKU
    const searchInput = page.locator('input[name="search"], input[type="search"]');
    await expect(searchInput).toBeVisible();

    await searchInput.fill('Test');
    await page.keyboard.press('Enter');
    // La tabla debería actualizarse con resultados filtrados
  });

  test('debe editar producto existente', async ({ page }) => {
    await page.goto('/productos/1/edit');

    // Modificar datos
    await page.fill('input[name="nombre"]', 'Producto Editado');

    await page.locator('button[type="submit"]').click();

    // Debería redirigir con mensaje de éxito
    await expect(page.locator('alert-success, .alert-success')).toBeVisible();
  });

  test('debe eliminar producto (con confirmación)', async ({ page }) => {
    await page.goto('/productos/1');

    const deleteButton = page.locator('button:has-text("Eliminar"), [data-testid="delete-btn"]');
    await deleteButton.click();

    // Debería mostrar modal de confirmación
    await expect(page.locator('modal, .modal, [data-testid="delete-confirm"]')).toBeVisible();

    // Confirmar eliminación
    await page.locator('button:has-text("Confirmar"), button:has-text("Sí")').click();

    // Debería redirigir con mensaje de éxito
    await expect(page.locator('alert-success, .alert-success')).toBeVisible();
  });
});
