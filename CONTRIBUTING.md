# CONTRIBUTING GUIDE - Jahitly Project

## Welcome to Jahitly Development! 👋

Dokumen ini menjelaskan standar coding dan kontribusi untuk project Jahitly.

---

## 🎯 Prinsip Utama

Semua code di project ini harus mengikuti 4 prinsip utama:

1. **Clean Code** - Code yang mudah dibaca, dipahami, dan di-maintain
2. **DRY** (Don't Repeat Yourself) - Hindari duplikasi kode
3. **KISS** (Keep It Simple, Stupid) - Sederhanakan logika yang kompleks
4. **Documentation** - Semua fungsi harus didokumentasikan dengan baik

---

## 📝 Dokumentasi Code

### Class Documentation

Setiap class HARUS memiliki dokumentasi:

```php
/**
 * ClassName - One-line description
 * 
 * Multi-line description explaining what this class does,
 * its responsibilities, and how it should be used.
 * 
 * Example:
 *   $service = new MyService();
 *   $result = $service->doSomething();
 * 
 * Catatan: Any special notes or considerations
 */
class MyClass
{
    // ...
}
```

### Method/Function Documentation

Setiap method publik HARUS memiliki PHPDoc:

```php
/**
 * Brief description of what this method does
 * 
 * Detailed explanation if needed, including:
 * - What it does
 * - Side effects
 * - Important notes
 * 
 * @param Type $paramName Description of parameter
 * @param Type $param2 Description of param 2
 * @return ReturnType Description of what is returned
 * 
 * @throws ExceptionType When/why it throws
 */
public function methodName($paramName, $param2): ReturnType
{
    // Implementation
}
```

### Property Documentation

Properties HARUS memiliki inline documentation:

```php
/** Brief description of property */
public string $propertyName;

/** @var array<string, int> Description with type info */
private array $complexProperty = [];
```

---

## 🏗️ Code Structure Standards

### Livewire Components

Struktur sections yang wajib:

```php
class MyComponent extends Component
{
    // ──────────────────────────────────────────────────────────
    // Properties
    // ──────────────────────────────────────────────────────────
    
    /** Purpose of this property */
    public string $property1 = '';

    // ──────────────────────────────────────────────────────────
    // Lifecycle Hooks
    // ──────────────────────────────────────────────────────────
    
    /**
     * Mount component
     */
    public function mount(): void { }

    // ──────────────────────────────────────────────────────────
    // Data Methods / Computed Properties
    // ──────────────────────────────────────────────────────────
    
    /**
     * Get computed property
     */
    public function getPropertyProperty() { }

    // ──────────────────────────────────────────────────────────
    // Actions
    // ──────────────────────────────────────────────────────────
    
    /**
     * Handle user action
     */
    public function handleAction() { }

    // ──────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────
    
    public function render(): View { }
}
```

### Services

```php
/**
 * ServiceName - Brief description
 * 
 * Full description of responsibilities
 */
class MyService
{
    // ──────────────────────────────────────────────────────────
    // Public Methods
    // ──────────────────────────────────────────────────────────
    
    /**
     * Public method description
     */
    public function publicMethod() { }

    // ──────────────────────────────────────────────────────────
    // Private Methods
    // ──────────────────────────────────────────────────────────
    
    /**
     * Private helper description
     */
    private function helperMethod() { }
}
```

### Controllers

```php
class MyController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // Setup & Middleware
    // ──────────────────────────────────────────────────────────
    
    public function __construct() { }

    // ──────────────────────────────────────────────────────────
    // CRUD Actions
    // ──────────────────────────────────────────────────────────
    
    /**
     * List action
     */
    public function index() { }

    /**
     * Show action
     */
    public function show(Model $model) { }

    /**
     * Create action
     */
    public function create() { }

    /**
     * Store action
     */
    public function store(StoreRequest $request) { }

    // ──────────────────────────────────────────────────────────
    // Custom Actions
    // ──────────────────────────────────────────────────────────
    
    public function customAction() { }
}
```

---

## 🔤 Naming Conventions

### Files & Classes

```php
// Class names: PascalCase
class PaymentService { }
class OrderStatusLog { }

// File names: Match class name
PaymentService.php
OrderStatusLog.php
```

### Methods

```php
// Action methods: verb + noun (imperative)
public function processPayment() { }
public function cancelOrder() { }
public function sendNotification() { }

// Query methods: get/has/is + description
public function getVerifiedPayments() { }
public function hasAnyPayment() { }
public function isActive() { }

// Modal methods: open/close + name
public function openPaymentForm() { }
public function closePaymentForm() { }

// Event handlers: updated + PropertyName
public function updatedStatusFilter() { }
```

### Variables

```php
// Local variables: camelCase
$paymentStatus = 'terverifikasi';
$totalAmount = 150000;

// Constants: UPPER_SNAKE_CASE (in enums, use PascalCase)
const PAYMENT_METHODS = ['transfer', 'cash'];
```

---

## 📦 Using Enums

**Semua status/types harus menggunakan enums**, bukan hardcoded strings.

### Creating Enums

```php
/**
 * Status Model - Enum untuk tipe status
 * 
 * Gunakan enum ini di semua tempat yang memerlukan nilai status.
 * Jangan gunakan hardcoded string!
 */
enum StatusType: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
        };
    }
}
```

### Using Enums

```php
// WRONG ❌
if ($payment->status === 'terverifikasi') { }
$order->update(['status' => 'dibatalkan']);

// RIGHT ✅
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;

if ($payment->status === PaymentStatus::TERVERIFIKASI->value) { }
$order->update(['status' => OrderStatus::DIBATALKAN->value]);

// Or directly in database:
$payment->status = PaymentStatus::TERVERIFIKASI->value;
```

---

## 🏢 Service Layer Pattern

Semua business logic kompleks HARUS berada di services, bukan di models/controllers.

### Good Pattern ✅

```php
// Service
class OrderService
{
    public function cancelOrder(Order $order, string $reason): bool { }
    public function calculateEstimatedPrice(Order $order): float { }
}

// Model
class Order extends Model
{
    public function cancel(string $reason): bool
    {
        return app(OrderService::class)->cancelOrder($this, $reason);
    }
}

// Controller/Livewire
class MyComponent extends Component
{
    public function submitCancellation()
    {
        app(OrderService::class)->cancelOrder($this->order, $reason);
    }
}
```

### Anti-Pattern ❌

```php
// Business logic di controller
class MyController extends Controller
{
    public function cancel(Order $order)
    {
        if (!in_array($order->status, [...])) { return; }
        if ($order->payments->where('status', 'terverifikasi')->exists()) { return; }
        // 50 lines of business logic here...
    }
}
```

---

## 🔐 Security Best Practices

### Authorization

Selalu validasi akses:

```php
public function mount(Order $order): void
{
    // Validasi user authenticated
    if (!auth()->check()) {
        abort(403);
    }

    // Validasi user adalah owner
    if ($order->customer_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
    }
}
```

### Data Validation

Selalu validasi input:

```php
public function submitForm()
{
    $this->validate([
        'email' => ['required', 'email'],
        'amount' => ['required', 'numeric', 'min:1000'],
    ], [
        'email.required' => 'Email harus diisi.',
        'amount.min' => 'Minimal Rp 1.000.',
    ]);
}
```

### Mass Assignment

```php
// Model - Explicitly define fillable
protected $fillable = [
    'name',
    'email',
    'status',
];

// Never $model->fill($request->all())
// Do: $model->fill($request->validated());
```

---

## 📊 Testing Guidelines

Write tests for important business logic:

```php
namespace Tests\Feature;

class OrderCancellationTest extends TestCase
{
    /**
     * Test order dapat dibatalkan jika tidak ada pembayaran terverifikasi
     */
    public function test_order_can_be_cancelled_without_verified_payment()
    {
        $order = Order::factory()->create();
        
        $this->assertTrue($order->canBeCancelled());
        
        $order->cancel('Alasan pembatalan');
        
        $this->assertTrue($order->isCancelled());
    }

    /**
     * Test order tidak dapat dibatalkan jika ada pembayaran terverifikasi
     */
    public function test_order_cannot_be_cancelled_with_verified_payment()
    {
        $order = Order::factory()->create();
        Payment::factory()
            ->for($order)
            ->status('terverifikasi')
            ->create();

        $this->assertFalse($order->canBeCancelled());
    }
}
```

---

## 🚀 Checklist untuk Pull Request

Sebelum membuat PR, pastikan:

- [ ] Semua class memiliki documentation
- [ ] Semua public method memiliki PHPDoc
- [ ] Tidak ada hardcoded status/type strings (gunakan enums)
- [ ] Business logic berada di services
- [ ] Component properly organized dalam sections
- [ ] Authorization checks ada
- [ ] Input validation ada
- [ ] Tests written untuk logic kritis
- [ ] Code follows naming conventions
- [ ] No duplicate code (DRY)
- [ ] Logic is simple (KISS)

---

## 💬 Code Review Comments - Common Issues

### ❌ Missing Documentation
```
This method needs PHPDoc with @param and @return types
```
**Fix**:
```php
/**
 * Calculate total price
 * @param Order $order
 * @return float
 */
public function calculateTotal(Order $order): float { }
```

### ❌ Hardcoded Strings
```
Use PaymentStatus enum instead of string 'terverifikasi'
```
**Fix**:
```php
use App\Enums\PaymentStatus;
$payment->status = PaymentStatus::TERVERIFIKASI->value;
```

### ❌ Duplicate Code (DRY Violation)
```
This logic is repeated in 3 places. Move to service
```
**Fix**: Create service, use across codebase

### ❌ Complex Method
```
This method is too long (100+ lines). Break into smaller methods
```
**Fix**: Split into focused, single-responsibility methods

### ❌ Business Logic in Wrong Place
```
This should be in a service, not in Livewire component
```
**Fix**: Move logic to service layer

---

## 🎓 Resources & Links

- [CODE_REFACTORING_GUIDE.md](CODE_REFACTORING_GUIDE.md) - Detail improvements
- [Laravel Docs](https://laravel.com/docs)
- [Livewire Docs](https://livewire.laravel.com)
- [Clean Code](https://www.oreilly.com/library/view/clean-code-a/9780136083238/)

---

## 📞 Questions?

Jika ada pertanyaan tentang coding standards:
1. Check [CODE_REFACTORING_GUIDE.md](CODE_REFACTORING_GUIDE.md)
2. Check existing code untuk examples
3. Ask team lead atau senior developer

---

**Last Updated**: 2026-07-01
**Version**: 1.0
