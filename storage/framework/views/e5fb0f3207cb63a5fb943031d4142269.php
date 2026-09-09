<?php $__env->startSection('title', 'Seguimiento #' . $tracking->orden_id); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* Tracking Show Styles */
.timeline-container {
    position: relative;
    padding-left: 2.5rem;
}
.timeline-container::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    padding-bottom: 1.75rem;
}
.timeline-item:last-child { padding-bottom: 0; }
.timeline-dot {
    position: absolute;
    left: -2.5rem;
    top: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2.5px solid;
    background: #fff;
    z-index: 1;
}
.timeline-dot.completed { background: #16a34a; border-color: #16a34a; }
.timeline-dot.current { background: #0ea5e9; border-color: #0ea5e9; animation: uiPulse 2s infinite; }
.timeline-dot.pending { background: #fff; border-color: #cbd5e1; }
.timeline-dot.failed { background: #dc2626; border-color: #dc2626; }
@keyframes uiPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(14,165,233,.4); }
    50% { box-shadow: 0 0 0 8px rgba(14,165,233,0); }
}
.timeline-time {
    font-size: .75rem;
    color: #94a3b8;
    font-weight: 500;
}
.timeline-title {
    font-weight: 600;
    font-size: .9rem;
    color: #1e293b;
}
.timeline-desc {
    font-size: .82rem;
    color: #64748b;
    margin-top: .15rem;
}
.info-card {
    background: rgba(241,245,249,.5);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    border: 1px solid #e2e8f0;
}
.map-frame {
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    min-height: 300px;
}
.status-badge-lg {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .5rem 1rem;
    border-radius: 9999px;
    font-size: .85rem;
    font-weight: 700;
}
@media (max-width: 767.98px) {
    .timeline-container { padding-left: 2rem; }
    .timeline-dot { left: -2rem; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-signpost-2"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Seguimiento #<?php echo e($tracking->orden_id); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-clock me-1"></i>
                        <span>Creado <?php echo e($tracking->created_at->format('d/m/Y H:i')); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('delivery-tracking.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="row g-4 mb-4">
        
        <div class="col-lg-7">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-bag-check me-2"></i>Información de la Orden
                    </h6>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-hash me-2 text-muted"></i>Número de Orden</span>
                        <span class="ui-detail-value fw-bold">#<?php echo e($tracking->orden_id); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-person me-2 text-muted"></i>Cliente</span>
                        <span class="ui-detail-value"><?php echo e($tracking->orden?->cliente?->nombre ?? 'N/A'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-geo-alt me-2 text-muted"></i>Dirección</span>
                        <span class="ui-detail-value"><?php echo e($tracking->orden?->direccion_entrega ?? 'N/A'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-telephone me-2 text-muted"></i>Teléfono</span>
                        <span class="ui-detail-value"><?php echo e($tracking->orden?->cliente?->telefono ?? 'N/A'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-tag me-2 text-muted"></i>Tipo de Orden</span>
                        <span class="ui-detail-value">
                            <span class="ui-badge ui-badge-info">
                                <?php echo e(ucfirst($tracking->orden?->tipo_orden ?? 'delivery')); ?>

                            </span>
                        </span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label"><i class="bi bi-currency-dollar me-2 text-muted"></i>Total</span>
                        <span class="ui-detail-value fw-bold">$<?php echo e(number_format($tracking->orden?->total ?? 0, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-5">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-person-badge me-2"></i>Driver Asignado
                    </h6>
                    <?php if($tracking->driver): ?>
                        <div class="text-center mb-3">
                            <div class="driver-avatar mx-auto mb-2" style="width:64px;height:64px;font-size:1.3rem;">
                                <?php echo e(strtoupper(substr($tracking->driver->nombre, 0, 1) . substr($tracking->driver->apellido, 0, 1))); ?>

                            </div>
                            <h5 class="fw-bold mb-1"><?php echo e($tracking->driver->nombre); ?> <?php echo e($tracking->driver->apellido); ?></h5>
                            <div class="small text-muted">
                                <i class="bi bi-telephone me-1"></i><?php echo e($tracking->driver->telefono); ?>

                            </div>
                            <?php if($tracking->driver->whatsapp): ?>
                            <div class="mt-1">
                                <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $tracking->driver->whatsapp)); ?>" target="_blank" class="text-decoration-none" style="color:#25D366;">
                                    <i class="bi bi-whatsapp me-1"></i>WhatsApp
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <hr>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Licencia</span>
                            <span class="ui-detail-value"><?php echo e($tracking->driver->licencia_conducir ?? '—'); ?></span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Estado</span>
                            <span class="ui-detail-value">
                                <?php if($tracking->driver->activo): ?>
                                    <span class="ui-badge ui-badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="ui-badge ui-badge-neutral">Inactivo</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                            <p class="mb-0">Sin driver asignado</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($tracking->latitud && $tracking->longitud): ?>
    <div class="ui-card mb-4" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                    <i class="bi bi-map me-2"></i>Ubicación Actual
                </h6>
            </div>
            <div class="map-frame">
                <iframe
                    src="https://www.google.com/maps?q=<?php echo e($tracking->latitud); ?>,<?php echo e($tracking->longitud); ?>&z=15&output=embed"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                    <i class="bi bi-diagram-3 me-2"></i>Historial de Seguimiento
                </h6>
                <span class="status-badge-lg ui-badge-<?php echo e($tracking->status === 'entregado' ? 'success' : ($tracking->status === 'en_camino' ? 'info' : ($tracking->status === 'fallido' ? 'danger' : ($tracking->status === 'cancelado' ? 'warning' : 'neutral')))); ?>">
                    <?php
                        $statusLabels = [
                            'creado' => 'Creado', 'en_camino' => 'En Camino',
                            'entregado' => 'Entregado', 'fallido' => 'Fallido', 'cancelado' => 'Cancelado'
                        ];
                    ?>
                    <i class="bi bi-<?php echo e($tracking->status === 'entregado' ? 'check-circle' : ($tracking->status === 'en_camino' ? 'truck' : ($tracking->status === 'fallido' ? 'x-circle' : ($tracking->status === 'cancelado' ? 'slash-circle' : 'clock')))); ?> me-1"></i>
                    <?php echo e($statusLabels[$tracking->status] ?? $tracking->status); ?>

                </span>
            </div>

            <div class="timeline-container">
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="timeline-item">
                    <div class="timeline-dot <?php echo e($event->completed ? 'completed' : ($event->is_current ? 'current' : 'pending')); ?>"></div>
                    <div class="timeline-time"><?php echo e($event->created_at->format('d/m/Y H:i:s')); ?></div>
                    <div class="timeline-title"><?php echo e($event->descripcion); ?></div>
                    <?php if($event->nota): ?>
                    <div class="timeline-desc"><?php echo e($event->nota); ?></div>
                    <?php endif; ?>
                    <?php if($event->usuario): ?>
                    <div class="timeline-desc">
                        <i class="bi bi-person me-1"></i>Por: <?php echo e($event->usuario); ?>

                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div class="ui-card mt-4" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                <i class="bi bi-lightning me-2"></i>Acciones Rápidas
            </h6>
            <div class="d-flex gap-2 flex-wrap">
                <?php if(in_array($tracking->status, ['creado', 'cancelado'])): ?>
                <form action="<?php echo e(route('delivery-tracking.updateStatus', $tracking)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" value="en_camino">
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                        <i class="bi bi-truck me-1"></i>Marcar en Camino
                    </button>
                </form>
                <?php endif; ?>

                <?php if($tracking->status === 'en_camino'): ?>
                <button type="button" class="ui-btn ui-btn-solid rounded-pill" data-bs-toggle="modal" data-bs-target="#confirmDeliveryModal">
                    <i class="bi bi-check-all me-1"></i>Confirmar Entrega
                </button>
                <?php endif; ?>

                <?php if(in_array($tracking->status, ['creado', 'en_camino'])): ?>
                <form action="<?php echo e(route('delivery-tracking.updateStatus', $tracking)); ?>" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Marcar esta entrega como fallida?')">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" value="fallido">
                    <button type="submit" class="ui-btn ui-btn-danger rounded-pill">
                        <i class="bi bi-x-circle me-1"></i>Marcar Fallido
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="confirmDeliveryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 bg-success text-white rounded-top-4">
                <h6 class="modal-title fw-bold"><i class="bi bi-check-all me-2"></i>Confirmar Entrega</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('delivery-tracking.updateStatus', $tracking)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" value="entregado">
                    <div class="mb-3">
                        <label class="ui-label">Foto de Entrega (opcional)</label>
                        <input type="file" name="foto_entrega" accept="image/*" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="ui-label">Firma del Cliente (opcional)</label>
                        <input type="file" name="firma_entrega" accept="image/*" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="ui-label">Notas de Entrega</label>
                        <textarea name="nota_entrega" class="ui-textarea" rows="2" placeholder="Detalles adicionales de la entrega..."></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                            <i class="bi bi-check-lg me-1"></i>Confirmar Entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/delivery-tracking/show.blade.php ENDPATH**/ ?>