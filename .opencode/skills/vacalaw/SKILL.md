---
name: vacalaw
description: Specialist frontend multi-tecnologia para desarrollo web profesional. Cubre CSS, Bootstrap 5.3, JavaScript ES6+, Vue 3 y Flutter. Aplica mejores practicas oficiales de documentacion MDN, getbootstrap.com, vuejs.org y docs.flutter.dev. Trigger keywords: "frontend", "CSS", "Bootstrap", "JavaScript", "Vue", "Flutter", "componente", "layout", "responsive", "widget", "estilo", "UI".
---

# vacalaw — Especialista Frontend Multi-Tecnologia

## Trigger

Cuando el usuario solicite ayuda con cualquiera de estas tecnologias:
- **CSS** — Flexbox, Grid, animaciones, variables CSS, responsive design
- **Bootstrap 5.3** — Grid system, componentes, utilidades, color modes
- **JavaScript ES6+** — Arrow functions, destructuring, modulos, Promesas, async/await, Fetch API
- **Vue 3** — Composition API, `<script setup>`, SFC, Pinia, Router, reactividad
- **Flutter** — Widgets, estado, navegacion, Material 3, responsive, testing

**Accion**: Aplicar las mejores practicas oficiales de documentacion para la tecnologia solicitada.

---

## Reglas Generales

### 1. Backup Obligatorio
Antes de cualquier modificacion, verificar que existen backups de los archivos a cambiar.

### 2. Analisis Previo
Leer el contexto existente (archivos relacionados, convenciones del proyecto) antes de implementar cambios.

### 3. Documentacion Referenciada
Seguir siempre las mejores practicas de las fuentes oficiales:
- CSS: MDN Web Docs — developer.mozilla.org
- Bootstrap: getbootstrap.com/docs/5.3
- JavaScript: MDN JavaScript Guide
- Vue 3: vuejs.org/guide
- Flutter: docs.flutter.dev

---

## Mejores Practicas CSS (MDN)

### Flexbox
```css
/* Contenedor flex con distribucion equilibrada */
.container {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
    align-items: stretch;
}

/* Item flexible con orden controlado */
.item {
    flex: 1 1 300px;
    min-width: 250px;
}
```

### CSS Grid
```css
/* Grid responsivo automatico */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* Grid con areas nombradas */
.layout {
    display: grid;
    grid-template-areas:
        "header header"
        "sidebar main"
        "footer footer";
    grid-template-columns: 250px 1fr;
    grid-template-rows: auto 1fr auto;
    min-height: 100vh;
}
```

### Variables CSS
```css
:root {
    --color-primary: #3b82f6;
    --color-primary-hover: #2563eb;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --radius: 0.5rem;
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --transition: all 0.2s ease;
}
```

### Responsive First
```css
/* Mobile-first con media queries */
.element {
    padding: var(--spacing-md);
    font-size: 1rem;
}

@media (min-width: 768px) {
    .element {
        padding: var(--spacing-lg);
        font-size: 1.125rem;
    }
}

@media (min-width: 1024px) {
    .element {
        max-width: 1200px;
        margin: 0 auto;
    }
}
```

### Animaciones
```css
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease forwards;
}
```

---

## Mejores Practicas Bootstrap 5.3

### Grid System
```html
<!-- Grid responsivo con breakpoints -->
<div class="container">
    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-4">Columna 1</div>
        <div class="col-12 col-md-6 col-lg-4">Columna 2</div>
        <div class="col-12 col-lg-4">Columna 3</div>
    </div>
</div>
```

### Componentes Esenciales
```html
<!-- Cards -->
<div class="card border-0 shadow-sm">
    <img src="..." class="card-img-top" alt="...">
    <div class="card-body">
        <h5 class="card-title">Titulo</h5>
        <p class="card-text">Descripcion.</p>
        <a href="#" class="btn btn-primary">Accion</a>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="modalId" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Titulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Contenido</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Dropdowns -->
<div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        Menu
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Opcion 1</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Opcion 2</a></li>
    </ul>
</div>
```

### Utilidades Clave
| Utilidad | Uso |
|----------|-----|
| `.d-flex`, `.justify-content-*`, `.align-items-*` | Flexbox |
| `.gap-*`, `.row-cols-*` | Espaciado |
| `.bg-*`, `.text-*`, `.border-*` | Color |
| `.rounded-*`, `.shadow-*` | Forma |
| `.p-*`, `.m-*`, `.mx-auto` | Spacing |
| `.fw-bold`, `.fst-italic`, `.text-uppercase` | Tipografia |
| `.d-none`, `.d-md-block` | Responsive display |

### Color Modes (Bootstrap 5.3+)
```javascript
// Activar dark mode
document.documentElement.setAttribute('data-bs-theme', 'dark');
// O 'light' para modo claro
```

---

## Mejores Practicas JavaScript ES6+ (MDN)

### Arrow Functions & Destructuring
```javascript
// Arrow function
const sumar = (a, b) => a + b;

// Destructuring de objetos
const { nombre, edad } = persona;

// Destructuring de arrays
const [primero, segundo] = lista;

// Parametros por defecto
const greet = (name = 'World') => `Hola, ${name}!`;
```

### Modulos
```javascript
// Exportar
export const PI = 3.14159;
export default class Calculator {}

// Importar
import Calculator from './calculator.js';
import { PI, sqrt } from 'math.js';
```

### Promesas & Async/Await
```javascript
// Promise
const fetchData = () => {
    return new Promise((resolve, reject) => {
        fetch('/api/data')
            .then(res => res.json())
            .then(data => resolve(data))
            .catch(err => reject(err));
    });
};

// Async/Await (preferido)
const loadData = async () => {
    try {
        const response = await fetch('/api/data');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
```

### Fetch API
```javascript
// GET
const getData = async (url) => {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.json();
};

// POST con CSRF
const postData = async (url, body) => {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(body)
    });
    return await res.json();
};
```

### Spread & Rest Operators
```javascript
// Spread para copiar/concatenar
const copia = [...arr, nuevoElemento];
const merged = { ...obj1, ...obj2 };

// Rest para argumentos
function sum(...nums) {
    return nums.reduce((a, b) => a + b, 0);
}
```

### Optional Chaining & Nullish Coalescing
```javascript
// Optional chaining
const ciudad = usuario?.direccion?.ciudad ?? 'No especificada';

// Nullish coalescing (solo null/undefined)
const valor = entrada ?? 'predeterminado';
```

---

## Mejores Practicas Vue 3

### Composition API con `<script setup>`
```vue
<script setup>
import { ref, computed, onMounted, watch } from 'vue'

// Estado reactivo
const count = ref(0)
const items = ref([])
const loading = ref(false)

// Computados
const total = computed(() => items.value.reduce((sum, item) => sum + item.price, 0))

// Metodos
const fetchData = async () => {
    loading.value = true
    try {
        const res = await fetch('/api/items')
        items.value = await res.json()
    } finally {
        loading.value = false
    }
}

// Ciclo de vida
onMounted(() => {
    fetchData()
})

// Watchers
watch(count, (newVal, oldVal) => {
    console.log(`Count changed from ${oldVal} to ${newVal}`)
})
</script>

<template>
    <div>
        <p>Count: {{ count }}</p>
        <button @click="count++">Incrementar</button>
        <p>Total: ${{ total.toFixed(2) }}</p>
    </div>
</template>

<style scoped>
/* Estilos locales al componente */
</style>
```

### Directivas Clave
| Directiva | Uso |
|-----------|-----|
| `v-bind:` / `:` | Ligadura de atributos dynamicos |
| `v-on:` / `@` | Event listeners |
| `v-model` | Two-way binding en forms |
| `v-if` / `v-else-if` / `v-else` | Condicional |
| `v-for` | Renderizado de listas |
| `v-show` | Mostrar/ocultar con CSS |
| `v-html` | Renderizar HTML crudo (prevencion XSS) |
| `v-slot` / `#` | Slots nombrados |

### Componentes Reutilizables
```vue
<!-- Padre.vue -->
<script setup>
import ChildComp from './ChildComp.vue'
</script>

<template>
    <ChildComp :message="texto" @custom-event="handleEvent">
        <template #header>Encabezado personalizado</template>
    </ChildComp>
</template>
```

### Composables (Logica Reutilizable)
```javascript
// useFetch.js
import { ref, onMounted } from 'vue'

export function useFetch(url) {
    const data = ref(null)
    const error = ref(null)
    const loading = ref(true)

    const execute = async () => {
        loading.value = true
        try {
            const res = await fetch(url)
            data.value = await res.json()
        } catch (e) {
            error.value = e.message
        } finally {
            loading.value = false
        }
    }

    onMounted(execute)
    return { data, error, loading, execute }
}
```

### Pinia (State Management)
```javascript
// stores/counter.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCounterStore = defineStore('counter', () => {
    const count = ref(0)
    const doubled = computed(() => count.value * 2)

    function increment() {
        count.value++
    }

    return { count, doubled, increment }
})
```

---

## Mejores Practicas Flutter

### Estructura de Widget
```dart
class MiWidget extends StatefulWidget {
    const MiWidget({super.key});

    @override
    State<MiWidget> createState() => _MiWidgetState();
}

class _MiWidgetState extends State<MiWidget> {
    int _counter = 0;

    void _increment() {
        setState(() => _counter++);
    }

    @override
    Widget build(BuildContext context) {
        return Scaffold(
            appBar: AppBar(title: const Text('Titulo')),
            body: Center(child: Text('$_counter')),
            floatingActionButton: FloatingActionButton(
                onPressed: _increment,
                child: const Icon(Icons.add),
            ),
        );
    }
}
```

### Provider (State Management Simple)
```dart
class CounterModel with ChangeNotifier {
    int _count = 0;
    int get count => _count;

    void increment() {
        _count++;
        notifyListeners();
    }
}

// Uso
ChangeProvider(
    create: (_) => CounterModel(),
    child: Consumer<CounterModel>(
        builder: (context, counter, _) => Text('${counter.count}'),
    ),
)
```

### Material 3 Theming
```dart
MaterialApp(
    theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blue),
        useMaterial3: true,
        fontFamily: 'Roboto',
    ),
    darkTheme: ThemeData.dark(useMaterial3: true),
    themeMode: ThemeMode.system,
)
```

### Responsive Design
```dart
LayoutBuilder(
    builder: (context, constraints) {
        if (constraints.maxWidth < 600) {
            return MobileLayout();
        } else if (constraints.maxWidth < 1200) {
            return TabletLayout();
        }
        return DesktopLayout();
    },
)
```

### Async en Widgets
```dart
FutureBuilder<Data>(
    future: fetchData(),
    builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
            return const CircularProgressIndicator();
        }
        if (snapshot.hasError) {
            return Text('Error: ${snapshot.error}');
        }
        return ListView.builder(
            itemCount: snapshot.data!.length,
            itemBuilder: (context, index) {
                return ListTile(title: Text(snapshot.data![index].name));
            },
        );
    },
)
```

### Testing
```dart
// Unit Test
test('increment counter', () {
    final model = CounterModel();
    expect(model.count, 0);
    model.increment();
    expect(model.count, 1);
});

// Widget Test
testWidgets('renders counter', (tester) async {
    await tester.pumpWidget(const MaterialApp(home: MiWidget()));
    expect(find.text('0'), findsOneWidget);
    await tester.tap(find.byType(FloatingActionButton));
    await tester.pump();
    expect(find.text('1'), findsOneWidget);
});
```

---

## Decision Tree por Tecnologia

| Escenario | Tecnologia recomendada |
|-----------|----------------------|
| Estilos basicos de layout | CSS Flexbox/Grid |
| Prototipado rapido | Bootstrap 5.3 |
| Interactividad compleja | Vue 3 + Composition API |
| Aplicacion multiplataforma movil | Flutter |
| Integracion progresiva | Bootstrap + Vue 3 |
| Dashboard administrativo | Bootstrap + DataTables + Vue |
| App movil nativa | Flutter con Material 3 |

---

## Checklist de Calidad

Antes de entregar codigo frontend, verificar:

- [ ] Responsive design probado en movil/tablet/desktop
- [ ] Contraste de colores accesible (WCAG AA minimo)
- [ ] Semantica HTML correcta (header, nav, main, section, footer)
- [ ] Events cleanup cuando corresponda
- [ ] Loading states implementados
- [ ] Error handling presente
- [ ] Accesibilidad basica (aria-labels, alt texts)
- [ ] Consistencia en naming conventions
- [ ] Sin codigo duplicado (DRY)
- [ ] Compatible con navegadores objetivo
