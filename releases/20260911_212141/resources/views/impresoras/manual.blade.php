@extends('layouts.app')
@section('title', 'Manual de Impresoras')

@push('styles')
@include('partials.premium-ui')
<style>
.manual-content h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
}
.manual-content h4 {
    font-size: 1rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #334155;
}
.manual-content .step-box {
    background: rgba(245,158,11,0.04);
    border: 1px solid rgba(245,158,11,0.15);
    border-left: 4px solid #f59e0b;
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
}
.manual-content .step-number {
    display: inline-flex;
    width: 32px;
    height: 32px;
    background: #f59e0b;
    color: white;
    border-radius: 50%;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    margin-right: 12px;
}
.manual-content ul {
    padding-left: 1.5rem;
    margin-bottom: 0.75rem;
}
.manual-content li {
    margin-bottom: 0.4rem;
    line-height: 1.6;
}
.manual-content li::marker {
    color: #f59e0b;
}
.manual-content .tip-box {
    background: rgba(34,197,94,0.06);
    border: 1px solid rgba(34,197,94,0.2);
    border-left: 4px solid #22c55e;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin: 1rem 0;
}
.manual-content .tip-box strong {
    color: #16a34a;
}
.manual-content .alert-box {
    background: rgba(6,182,212,0.06);
    border: 1px solid rgba(6,182,212,0.2);
    border-left: 4px solid #06b6d4;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin: 1rem 0;
}
.manual-content .alert-box strong {
    color: #0891b2;
}
.manual-content .warn-box {
    background: rgba(245,158,11,0.06);
    border: 1px solid rgba(245,158,11,0.2);
    border-left: 4px solid #f59e0b;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin: 1rem 0;
}
.manual-content .warn-box strong {
    color: #d97706;
}
.manual-content .error-box {
    background: rgba(239,68,68,0.06);
    border: 1px solid rgba(239,68,68,0.2);
    border-left: 4px solid #ef4444;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin: 1rem 0;
}
.manual-content .error-box strong {
    color: #dc2626;
}
.manual-content code {
    background: rgba(0,0,0,0.06);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.9em;
}
body.dark-mode .manual-content h3 { color: #e2e8f0; }
body.dark-mode .manual-content h4 { color: #cbd5e1; }
body.dark-mode .manual-content .step-box { background: rgba(245,158,11,0.08); }
body.dark-mode .manual-content code { background: rgba(255,255,255,0.08); }
body.dark-mode .manual-sidebar {
    background: rgba(15,23,42,0.6);
    border-color: #334155;
}
body.dark-mode .manual-sidebar a { color: #94a3b8; }
body.dark-mode .manual-sidebar a:hover,
body.dark-mode .manual-sidebar a.active { color: #f59e0b; }
body.dark-mode .manual-sidebar a.active { background: rgba(245,158,11,0.1); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Manual de Configuracion de Impresoras</h4>
                    <div class="ui-header-meta">Guia completa para configurar tu impresora termica y empezar a imprimir tickets</div>
                </div>
            </div>
            <div class="ui-header-actions d-flex gap-2">
                <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Modulo
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="manual-sidebar sticky-top" style="top: 2rem; z-index: 100; background: rgba(248,250,252,0.95); backdrop-filter: blur(10px); border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem;">
                <h6 class="fw-bold mb-3" style="color: #f59e0b;">
                    <i class="bi bi-list-ul me-2"></i>Contenido
                </h6>
                <nav style="font-size: 0.85rem;">
                    <a href="#que-necesitas" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">1. Que Necesitas</a>
                    <a href="#conectar" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">2. Conectar Impresora</a>
                    <a href="#registrar" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">3. Registrar en Sistema</a>
                    <a href="#chrome" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">4. Configurar Chrome</a>
                    <a href="#probar" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">5. Probar Impresion</a>
                    <a href="#auto" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">6. Impresion Automatica</a>
                    <a href="#problemas" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">7. Problemas Frecuentes</a>
                    <a href="#consejos" class="d-block py-2 px-3 mb-2 rounded" style="text-decoration:none; color:#475569; font-weight:500;">8. Consejos</a>
                </nav>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="manual-content">
                <div class="mb-4 pb-3 border-bottom">
                    <h3 style="color: #f59e0b;">
                        <i class="bi bi-printer"></i>
                        Configurar Impresora Termica
                    </h3>
                    <p class="text-muted mb-0">Esta guia te lleva paso a paso desde la conexion fisica hasta la impresion de tickets. El sistema de Erpipos ERP usa la impresion del navegador web (Chrome/Edge), que es el metodo mas compatible y sencillo para impresoras termicas.</p>
                </div>

                <div id="que-necesitas" class="step-box">
                    <h3><span class="step-number">1</span>Que Necesitas</h3>
                    <p>Para comenzar, asegurate de tener:</p>
                    <ul>
                        <li><strong>Impresora termica</strong> de 80mm (estandar) o 58mm (pequena). Marcas comunes: EPSON TM-T20, EPSON TM-T82, Xprinter, BTP, Star TSP143.</li>
                        <li><strong>PC o laptop</strong> con Chrome o Edge instalado. La impresora debe estar conectada a este PC (USB) o a la misma red (WiFi/Ethernet).</li>
                        <li><strong>Navegador Chrome</strong> (recomendado) o Microsoft Edge. Son los mejores para impresion termica.</li>
                        <li><strong>Roll de papel termico</strong> del tamano correcto (58mm o 80mm).</li>
                        <li><strong>Acceso al sistema</strong> con un usuario que tenga permiso de "Impresoras y Plantillas".</li>
                    </ul>
                </div>

                <div id="conectar" class="step-box">
                    <h3><span class="step-number">2</span>Conectar la Impresora</h3>

                    <h4>Opcion A: USB (Recomendado)</h4>
                    <p>La opcion mas sencilla y rapida. Ideal para PCs que estan fijos en la caja.</p>
                    <ul>
                        <li>Conecta el cable USB de la impresora al PC de la caja.</li>
                        <li>Enciende la impresora (boton power).</li>
                        <li>Espera 30 segundos para que Windows instale el driver automaticamente.</li>
                        <li>Si no se instala, descarga el driver del fabricante (EPSON, Xprinter, etc).</li>
                        <li>Ve a <code>Windows > Configuracion > Dispositivos > Impresoras y Scanners</code>.</li>
                        <li>Deberia aparecer tu impresora. Si aparece con color verde, esta lista.</li>
                        <li>Haz clic derecho > <strong>"Establecer como impresora predeterminada"</strong>.</li>
                    </ul>

                    <h4>Opcion B: Red / WiFi</h4>
                    <p>Perfecto para compartir la impresora entre varios PCs o cuando la impresora esta lejos del PC.</p>
                    <ul>
                        <li>Conecta la impresora a tu red WiFi o Ethernet (cable de red).</li>
                        <li>Debes saber la direccion IP de la impresora. Normalmente:</li>
                        <ul>
                            <li>En la impresora EPSON: Manten presionado el boton WiFi 3 segundos para imprimir pagina de configuracion.</li>
                            <li>O ingresa a la interfaz web de la impresora (si tiene): usualmente http://192.168.1.xxx</li>
                            <li>O usa el software del fabricante para encontrar la IP.</li>
                        </ul>
                        <li>Anotar la IP (ej: 192.168.1.50) y el puerto (default: 9100).</li>
                        <li>Agrega la impresora en Windows: <code>Configuracion > Dispositivos > Agregar impresora</code>.</li>
                        <li>Selecciona "La impresora no esta en mi lista" > "Agregar impresora TCP/IP".</li>
                        <li>Ingresa la IP y deja el puerto 9100. Siguiente. El sistema la detecta.</li>
                    </ul>

                    <div class="alert-box">
                        <strong><i class="bi bi-info-circle me-1"></i>Nota:</strong> Chrome y Edge usan las impresoras instaladas en el sistema operativo. Si la impresora aparece en Windows, Chrome la vera automaticamente. No necesitas drivers especiales para Chrome.
                    </div>
                </div>

                <div id="registrar" class="step-box">
                    <h3><span class="step-number">3</span>Registrar la Impresora en el Sistema</h3>
                    <p>Ahora que la impresora fisica esta lista, la registramos en Erpipos ERP:</p>
                    <ul>
                        <li><strong>Ve a:</strong> <code>Configuracion > Impresoras y Plantillas</code> (o clic en "Impresoras" en el menu lateral).</li>
                        <li><strong>Clic en:</strong> Boton naranja "Nueva Impresora" arriba a la derecha.</li>
                        <li><strong>Completa el formulario:</strong></li>
                    </ul>

                    <div class="table-responsive">
                        <table class="table table-bordered" style="font-size:0.9rem;">
                            <thead style="background:#f59e0b; color:white;">
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor ejemplo</th>
                                    <th>Explicacion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Nombre</strong></td>
                                    <td>Caja 1 Termica</td>
                                    <td>Nombre descriptivo para identificarla facil.</td>
                                </tr>
                                <tr>
                                    <td><strong>Sucursal</strong></td>
                                    <td>Sucursal Centro</td>
                                    <td>Asocia a una sucursal. Si no tienes sucursales, deja "Sin sucursal (global)".</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Conexion</strong></td>
                                    <td>Local / USB / Red</td>
                                    <td>Local/USB: conectada al PC. Red: tiene IP propia.</td>
                                </tr>
                                <tr>
                                    <td><strong>Direccion IP</strong></td>
                                    <td>192.168.1.50</td>
                                    <td>Solo si es "Red". Dejar vacio para USB/Local.</td>
                                </tr>
                                <tr>
                                    <td><strong>Puerto</strong></td>
                                    <td>9100</td>
                                    <td>Default para impresoras termicas ESCPOS. No cambiar a menos que tu impresora lo requiera.</td>
                                </tr>
                                <tr>
                                    <td><strong>Tamano de Papel</strong></td>
                                    <td>80mm</td>
                                    <td>58mm (ticket pequeno) o 80mm (ticket estandar) o A4 (documento).</td>
                                </tr>
                                <tr>
                                    <td><strong>Caracteres por Linea</strong></td>
                                    <td>48</td>
                                    <td>58mm = 42 caracteres. 80mm = 48 caracteres. El sistema lo calcula automatico, pero puedes ajustarlo.</td>
                                </tr>
                                <tr>
                                    <td><strong>Auto-imprimir Ventas</strong></td>
                                    <td><i class="bi bi-toggle2-on text-success"></i> SI</td>
                                    <td>Activa esto para que cada venta se imprima sola sin que tengas que hacer clic.</td>
                                </tr>
                                <tr>
                                    <td><strong>Impresora Activa</strong></td>
                                    <td><i class="bi bi-toggle2-on text-success"></i> SI</td>
                                    <td>Si esta desactiva, no se usara para imprimir.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p>Despues de llenar todo, clic en <strong>"Guardar Impresora"</strong>.</p>

                    <div class="tip-box">
                        <strong><i class="bi bi-lightbulb me-1"></i>Tip:</strong> Si tienes varias cajas, registra una impresora por cada una. Asigna cada impresora a su sucursal correspondiente. El sistema automaticamente usara la impresora correcta al vender en esa sucursal.
                    </div>
                </div>

                <div id="chrome" class="step-box">
                    <h3><span class="step-number">4</span>Configurar Chrome para Imprimir</h3>
                    <p>Chrome necesita saber como imprimir en tu impresora termica. Configuralo asi:</p>
                    <ul>
                        <li>Abre Chrome (o Edge).</li>
                        <li>Clic en los <strong>3 puntos</strong> arriba a la derecha > <strong>Configuracion</strong>.</li>
                        <li>En el buscador de configuracion, escribe: <code>impresiones</code> o <code>printers</code>.</li>
                        <li>O ve directamente a: <code>chrome://settings/printers</code></li>
                        <li>Deberias ver tu impresora termica en la lista (si la instalaste correctamente).</li>
                        <li>Clic sobre tu impresora > Marca la casilla <strong>"Predeterminada"</strong>.</li>
                        <li>Haz clic en <strong>"Imprimir pagina de prueba"</strong> para verificar que funciona.</li>
                    </ul>

                    <h4>Configuracion avanzada de impresion (en el dialogo de impresion):</h4>
                    <p>Cuando hagas clic en "Imprimir Ticket", se abrira el dialogo de Chrome. Alli configura:</p>
                    <ul>
                        <li><strong>Destino:</strong> Selecciona tu impresora termica.</li>
                        <li><strong>Papel y tamano:</strong> Elige "Personalizado" o "Otro tamano".</li>
                        <li><strong>Ancho:</strong> <code>80mm</code> (o <code>58mm</code> si tu impresora es pequena).</li>
                        <li><strong>Alto:</strong> <code>Sin limite</code> o <code>999in</code> (infinito).</li>
                        <li><strong>Margenes:</strong> Selecciona <code>Sin margenes</code> o <code>Minimo</code>.</li>
                        <li><strong>Escalas:</strong> <code>Normal</code> (no ajustar a pagina).</li>
                    </ul>

                    <div class="alert-box">
                        <strong><i class="bi bi-info-circle me-1"></i>Importante:</strong> Esta configuracion de tamano personalizado la haras cada vez que imprimas (en el dialogo de Chrome). Puedes guardarla si Chrome la recuerda. Si no, te recomendamos usar Chrome como predeterminado y configurar siempre en el dialogo.
                    </div>
                </div>

                <div id="probar" class="step-box">
                    <h3><span class="step-number">5</span>Probar la Impresion</h3>
                    <p>Ahora viene la parte mas emocionante: imprimir tu primer ticket:</p>
                    <ul>
                        <li><strong>Ve a:</strong> <code>Ventas > Nueva Venta</code> (o terminal POS).</li>
                        <li><strong>Agrega un producto:</strong> Busca cualquier producto y agrega uno solo por RD$100.00.</li>
                        <li><strong>Cobra la venta:</strong> Selecciona efectivo y cobra.</li>
                        <li><strong>Despues de cobrar:</strong> En la vista de la venta creada, veras dos botones:
                            <ul>
                                <li><strong>"PDF"</strong> - descarga un PDF de la factura</li>
                                <li><strong>"Imprimir"</strong> - abre el ticket para imprimir</li>
                            </ul>
                        </li>
                        <li><strong>Clic en "Imprimir".</strong></li>
                        <li>Se abrira el dialogo de impresion de Chrome. Verifica que:
                            <ul>
                                <li>El destino sea tu impresora termica</li>
                                <li>Vistas previa del ticket con formato correcto</li>
                            </ul>
                        </li>
                        <li><strong>Clic en "Imprimir".</strong></li>
                        <li><strong>Si sale:</strong> Felicitaciones, esta funcionando! </li>
                        <li><strong>Si no sale:</strong> Ve a "Problemas Frecuentes" abajo.</li>
                    </ul>

                    <div class="tip-box">
                        <strong><i class="bi bi-lightbulb me-1"></i>Tip:</strong> Si activaste "Auto-imprimir Ventas" en la configuracion de la impresora, el ticket se abrira automaticamente en una nueva pestaña y se disparara el dialogo de impresion sin que tengas que hacer clic. Esto funciona para todas las ventas que crees.
                    </div>
                </div>

                <div id="auto" class="step-box">
                    <h3><span class="step-number">6</span>Configurar Impresion Automatica</h3>
                    <p>Para que NO tengas que clickear "Imprimir" en cada venta, hay dos formas:</p>

                    <h4>Metodo 1: Impresora predeterminada + Auto-imprimir</h4>
                    <ol>
                        <li>En Chrome, establece tu impresora como <strong>"Predeterminada"</strong> (paso 4 de esta guia).</li>
                        <li>En Erpipos ERP, ve a <code>Configuracion > Impresoras</code>.</li>
                        <li>Edita tu impresora y activa <strong>"Auto-imprimir Ventas: SI"</strong>.</li>
                        <li>Guarda.</li>
                        <li>Ahora, cada vez que hagas una venta, se abrirá automaticamente el dialogo de impresion con tu impresora ya seleccionada.</li>
                        <li>Solo tienes que confirmar con "Imprimir" (o Ctrl+P en Chrome puede saltar el dialogo si la impresora esta como predeterminada).</li>
                    </ol>

                    <h4>Metodo 2: Impresora 100% automatica (sin dialogo)</h4>
                    <p>Chrome no permite impresion 100% automatica sin dialogo por seguridad. Sin embargo, hay alternativas:</p>
                    <ul>
                        <li><strong>Chrome flags:</strong> No recomendado para usuarios normales. Requiere modificaciones de seguridad del navegador.</li>
                        <li><strong>QZ Tray:</strong> Software externo que permite impresion directa desde el navegador. Requiere instalacion de un servicio local y configuracion extra.</li>
                        <li><strong>Impresion mediante ESC/POS:</strong> Requiere un servicio backend que envie comandos directamente a la impresora via USB/IP. Esta funcionalidad NO esta implementada en el sistema actualmente.</li>
                    </ul>

                    <div class="warn-box">
                        <strong><i class="bi bi-exclamation-triangle me-1"></i>Limitacion:</strong> El sistema usa impresion del navegador web. La impresion sera automatica hasta el dialogo de Chrome (solo confirras con Ctrl+P o clic en Imprimir). Para impresion 100% automatica (sin interaccion), se requiere integracion con software de terceros como QZ Tray o un driver local ESCPOS.
                    </div>
                </div>

                <div id="problemas" class="step-box">
                    <h3><span class="step-number">7</span>Problemas Frecuentes</h3>

                    <div class="error-box">
                        <strong>Q: No aparece mi impresora en Chrome</strong>
                        <p>Chrome usa las impresoras del sistema operativo. Verifica que tu impresora aparezca en <code>Windows > Configuracion > Dispositivos > Impresoras y Scanners</code>. Si no aparece:</p>
                        <ul>
                            <li>Verifica que el cable USB este bien conectado.</li>
                            <li>Reinicia la impresora y el PC.</li>
                            <li>Descarga e instala el driver del fabricante.</li>
                            <li>En Windows: clic en "Agregar impresora" y busca la tuya.</li>
                        </ul>
                    </div>

                    <div class="error-box">
                        <strong>Q: Se imprime en papel doble / sale mucho papel</strong>
                        <p>En el dialogo de impresion de Chrome, haz clic en <code>"Mas configuracion"</code> y busca <code>"Tamano"</code>:</p>
                        <ul>
                            <li>Tamano personalizado: Ancho = <code>80mm</code> (o <code>58mm</code>), Alto = <code>Sin limite</code> o <code>999in</code>.</li>
                            <li>Margenes: Selecciona <code>Sin margenes</code> o <code>Minimo</code>.</li>
                            <li>Escalas: <code>Normal</code> (no "Ajustar a pagina").</li>
                        </ul>
                    </div>

                    <div class="error-box">
                        <strong>Q: La impresion sale cortada (no sale todo el ticket)</strong>
                        <p>Asegurate de que:</p>
                        <ul>
                            <li>El tamano de papel en el dialogo de impresion coincida con el tamano de papel fisico (58mm o 80mm).</li>
                            <li>Al crear la impresora en el sistema, seleccionaste el tamano correcto (58mm o 80mm).</li>
                            <li>Los "Caracteres por linea" son correctos: 58mm=42, 80mm=48.</li>
                        </ul>
                    </div>

                    <div class="error-box">
                        <strong>Q: La impresion sale en blanco</strong>
                        <ul>
                            <li>Verifica que la impresora tenga papel termico instalado correctamente (el lado que imprime debe estar hacia arriba o hacia abajo dependiendo del modelo).</li>
                            <li>Prueba imprimir una pagina de prueba desde Windows.</li>
                            <li>Verifica que la impresora no este en modo "offline".</li>
                        </ul>
                    </div>

                    <div class="error-box">
                        <strong>Q: Quiero compartir una impresora USB por red</strong>
                        <ul>
                            <li>En Windows: clic derecho en la impresora > <code>Propiedades > Comparticion > Compartir esta impresora</code>.</li>
                            <li>En el otro PC: <code>\\NOMBRE-PC\NOMBRE-IMPRESORA</code>.</li>
                            <li>En Erpipos ERP: en la impresora del segundo PC, selecciona tipo <code>Red</code> e indica la direccion compartida.</li>
                        </ul>
                    </div>

                    <div class="error-box">
                        <strong>Q: El ticket se abre pero no se imprime</strong>
                        <ul>
                            <li>Verifica que la impresora tenga papel.</li>
                            <li>Verifica que la impresora este encendida y sin errores.</li>
                            <li>En el dialogo de Chrome, confirma que el "Destino" sea tu impresora termica (no "Guardar como PDF").</li>
                            <li>Prueba con una impresora de PDF primero para verificar que el ticket se genera correctamente.</li>
                        </ul>
                    </div>

                    <div class="error-box">
                        <strong>Q: La impresion sale muy lenta</strong>
                        <ul>
                            <li>Las impresoras termicas pequenas (58mm) pueden ser mas lentas. Es normal.</li>
                            <li>Si es por red, verifica que la conexion sea estable (mejor con cable Ethernet que WiFi).</li>
                            <li>El ticket se genera automaticamente. No hay "buffer" para imprimir varias a la vez.</li>
                        </ul>
                    </div>
                </div>

                <div id="consejos" class="step-box">
                    <h3><span class="step-number">8</span>Consejos y Mejores Practicas</h3>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Usa Chrome:</strong> Chrome tiene la mejor compatibilidad con impresion termica. Firefox y Safari tienen limitaciones. Edge funciona bien tambien.
                    </div>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Prueba con venta de prueba:</strong> Siempre crea una venta pequena (RD$100) y prueba la impresion antes de usar en venta real.
                    </div>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Una impresora por caja:</strong> Asigna UNA impresora por sucursal/caja. No compartas impresoras entre sucursales.
                    </div>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Activa auto-imprimir:</strong> Si tu flujo de trabajo lo permite, activa "Auto-imprimir Ventas" para ahorrar tiempo. Cada venta se imprimira automaticamente sin que tengas que hacer clic.
                    </div>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Tamano correcto:</strong> Asegurate de que el tamano de papel en el sistema coincida con el papel fisico. 58mm = 42 caracteres, 80mm = 48 caracteres.
                    </div>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Papel termico de calidad:</strong> Usa rollos de buena calidad. El papel termico barata puede dejar marcas y la impresion se ve mal.
                    </div>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Cuidado con el calor:</strong> No expongas la impresora termica a fuentes de calor directo (sol, radiadores). El papel termico se oscurece con el calor y la impresion se arruina.
                    </div>

                    <div class="tip-box">
                        <strong><i class="bi bi-check-circle me-1"></i>Respaldo de PDF:</strong> Tambien tienes el boton "PDF" que descarga un archivo. Guárdalo como respaldo si necesitas un archivo digital de la factura.
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll and active link
    const links = document.querySelectorAll('.manual-sidebar a');
    const sections = document.querySelectorAll('.step-box[id]');

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Highlight active section on scroll
    window.addEventListener('scroll', function() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (scrollY >= sectionTop - 100) {
                current = section.getAttribute('id');
            }
        });

        links.forEach(link => {
            link.classList.remove('active');
            link.style.background = '';
            link.style.color = '';
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
                link.style.background = 'rgba(245,158,11,0.1)';
                link.style.color = '#f59e0b';
            }
        });
    });
});
</script>
@endpush
