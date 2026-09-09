<?php $__env->startSection('title', 'Detalle del Equipo'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#38bdf8;--accent-rgb:56,189,248;--accent-hover:#0ea5e9;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-phone"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($equipo->marca); ?> <?php echo e($equipo->modelo); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-upc-scan me-1"></i>
                        <?php echo e($equipo->serial_imei); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('equipos.edit', $equipo)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(56,189,248,.2);border-color:rgba(56,189,248,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="<?php echo e(route('equipos.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="ui-card mb-4" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3" style="color:#0891b2;"><i class="bi bi-phone me-2"></i>Identificación del Equipo</h5>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="detail-label">Serial / IMEI</div>
                            <div class="detail-value font-monospace fw-bold"><?php echo e($equipo->serial_imei); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Serial ESN</div>
                            <div class="detail-value font-monospace"><?php echo e($equipo->serial_esn ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Marca</div>
                            <div class="detail-value fw-semibold"><?php echo e($equipo->marca); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Modelo</div>
                            <div class="detail-value fw-semibold"><?php echo e($equipo->modelo); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Color</div>
                            <div class="detail-value"><?php echo e($equipo->color ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Almacenamiento</div>
                            <div class="detail-value"><?php echo e($equipo->almacenamiento_gb ? $equipo->almacenamiento_gb . ' GB' : '—'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ui-card mb-4" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#7c3aed"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3" style="color:#7c3aed;"><i class="bi bi-cpu me-2"></i>Especificaciones Técnicas</h5>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="detail-label">Tipo de Dispositivo</div>
                            <div class="detail-value"><?php echo e(ucfirst(str_replace('_', ' ', $equipo->tipo_dispositivo ?? 'Sin especificar'))); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Procesador</div>
                            <div class="detail-value"><?php echo e($equipo->procesador ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Memoria RAM</div>
                            <div class="detail-value"><?php echo e($equipo->memoria_ram ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Tipo de Almacenamiento</div>
                            <div class="detail-value"><?php echo e($equipo->almacenamiento_tipo ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Capacidad</div>
                            <div class="detail-value"><?php echo e($equipo->almacenamiento_capacidad ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Sistema Operativo</div>
                            <div class="detail-value"><?php echo e($equipo->sistema_operativo ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Puertos</div>
                            <div class="detail-value"><?php echo e($equipo->puertos ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Peso</div>
                            <div class="detail-value"><?php echo e($equipo->peso_gramos ? $equipo->peso_gramos . ' g' : '—'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="background:#059669"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3" style="color:#059669;"><i class="bi bi-cash-stack me-2"></i>Precios y Estado</h5>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">
                                <?php
                                    $estadoColors = [
                                        'disponible' => 'success',
                                        'vendido' => 'info',
                                        'en_reparacion' => 'warning',
                                        'dañado' => 'danger',
                                        'reservado' => 'primary',
                                        'mantenimiento' => 'secondary',
                                    ];
                                ?>
                                <span class="badge bg-<?php echo e($estadoColors[$equipo->estado] ?? 'secondary'); ?> bg-opacity-10 text-<?php echo e($estadoColors[$equipo->estado] ?? 'secondary'); ?> rounded-pill">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $equipo->estado))); ?>

                                </span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Precio de Venta</div>
                            <div class="detail-value fw-bold" style="color:#059669;font-size:1.1rem;">RD$ <?php echo e(number_format($equipo->precio_venta, 2)); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Precio de Compra</div>
                            <div class="detail-value"><?php echo e($equipo->precio_compra ? 'RD$ ' . number_format($equipo->precio_compra, 2) : '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Proveedor</div>
                            <div class="detail-value"><?php echo e($equipo->proveedor?->nombre ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Fecha de Compra</div>
                            <div class="detail-value"><?php echo e($equipo->fecha_compra ? $equipo->fecha_compra->format('d/m/Y') : '—'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="detail-label">Factura de Compra</div>
                            <div class="detail-value"><?php echo e($equipo->factura_compra ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#ca8a04"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2" style="color:#ca8a04;"></i>Garantía</h6>
                    <div class="mb-2">
                        <div class="detail-label">Tipo</div>
                        <div class="detail-value"><?php echo e($equipo->garantia_tipo ? ucfirst($equipo->garantia_tipo) : 'Sin garantía'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Desde</div>
                        <div class="detail-value"><?php echo e($equipo->garantia_desde ? $equipo->garantia_desde->format('d/m/Y') : '—'); ?></div>
                    </div>
                    <div class="mb-0">
                        <div class="detail-label">Hasta</div>
                        <div class="detail-value"><?php echo e($equipo->garantia_hasta ? $equipo->garantia_hasta->format('d/m/Y') : '—'); ?></div>
                    </div>
                </div>
            </div>

            
            <div class="ui-card mb-4" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#dc2626"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lock me-2" style="color:#dc2626;"></i>Bloqueos</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?php if($equipo->bloqueado_icloud): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>iCloud</span>
                        <?php endif; ?>
                        <?php if($equipo->bloqueado_fr): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>FR</span>
                        <?php endif; ?>
                        <?php if(!$equipo->bloqueado_icloud && !$equipo->bloqueado_fr): ?>
                            <span class="text-muted small">Sin bloqueos</span>
                        <?php endif; ?>
                    </div>
                    <?php if($equipo->observaciones): ?>
                        <hr>
                        <div class="detail-label">Observaciones</div>
                        <div class="detail-value small"><?php echo e($equipo->observaciones); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <i class="bi bi-phone" style="font-size:2.5rem;color:#38bdf8;"></i>
                    <h6 class="fw-bold mt-2 mb-0"><?php echo e($equipo->marca); ?> <?php echo e($equipo->modelo); ?></h6>
                    <small class="text-muted">Registrado <?php echo e($equipo->created_at->diffForHumans()); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/equipos/show.blade.php ENDPATH**/ ?>