# Jahitly Project - Code Quality Analysis Report

**Generated:** 2026-07-01  
**Scope:** PHP/Laravel Codebase Analysis  
**Focus Areas:** Models, Services, Livewire Components, Controllers, Routes

---

## 📋 Executive Summary

The Jahitly project demonstrates good foundational structure with proper use of Laravel patterns, but contains several code quality issues affecting maintainability, consistency, and adherence to SOLID principles. Key issues include:

- **DRY Violations**: Repeated payment status logic, material validation patterns, and authorization checks
- **Complex Functions**: Large Livewire components mixing concerns and business logic
- **Missing Documentation**: Inconsistent inline comments and missing method documentation
- **Code Quality Issues**: Tight coupling, missing error handling, and inconsistent patterns
- **Inconsistent Patterns**: Mixed coding styles and validation approaches

---

## 1. DRY VIOLATIONS (Repeated Code Patterns)

### 1.1 Payment Status Detection Logic

**Issue**: Payment status determination logic is repeated across multiple files with identical filtering patterns.

**Files Affected**:
- [app/Models/Order.php](app/Models/Order.php#L119-L165) - `getPaymentStatusAttribute()` method
- [app/Livewire/Customer/Orders/Show.php](app/Livewire/Customer/Orders/Show.php#L100-L125) - Local payment status calculation
- [app/Livewire/Customer\Payments\Create.php](app/Livewire/Customer\Payments\Create.php#L21-L43) - Payment type determination
- [app/Http/Controllers/PaymentController.php](app/Http/Controllers/PaymentController.php#L24-L48) - Duplicate DP verification check
- [app/Livewire/Admin/Payments/Index.php](app/Livewire/Admin/Payments/Index.php#L59-L73) - Another DP verification check

**Examples**:

```php
// Pattern 1 - Order Model (line 136-145)
$hasVerifiedDp = $payments
    ->where('payment_type', 'dp')
    ->where('status', 'terverifikasi')
    ->isNotEmpty();

// Pattern 2 - PaymentController create() (line 29-32)
$hasVerifiedDp = $order->payments
    ->where('payment_type', 'dp')
    ->where('status', 'terverifikasi')
    ->isNotEmpty();

// Pattern 3 - Create.php mount() (line 37-40)
$hasVerifiedDp = $this->order->payments
    ->where('payment_type', 'dp')
    ->where('status', 'terverifikasi')
    ->isNotEmpty();
```

**Recommendation**:
- Create helper method `hasVerifiedPaymentType(string $type)` in Payment model
- Extract to `PaymentStatusChecker` service class

---

### 1.2 Authorization & Role Checks

**Issue**: Authentication and role verification logic repeated across multiple Livewire components.

**Files Affected**:
- [app/Livewire/Customer/Orders/Show.php](app/Livewire/Customer/Orders/Show.php#L18-L26)
- [app/Livewire/Customer/Orders/Create.php](app/Livewire/Customer/Orders/Create.php#L27-L31)
- [app/Livewire/Customer/Orders/CancelOrder.php](app/Livewire/Customer/Orders/CancelOrder.php#L14-L25)
- [app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L40-L42)
- [app/Livewire/Admin/Payments/Index.php](app/Livewire/Admin/Payments/Index.php#L24-L27)

**Examples**:

```php
// Pattern repeated in Show.php (line 18-26)
if (!auth()->check() || !auth()->user()->isCustomer()) {
    abort(403, 'Anda tidak memiliki akses ke halaman ini.');
}

if ($order->customer_id !== auth()->id()) {
    abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
}

// Similar pattern in Create.php (line 27-31)
if (!auth()->check() || !auth()->user()->isCustomer()) {
    abort(403, 'Anda tidak memiliki akses ke halaman ini.');
}
```

**Recommendation**:
- Create `AuthorizeTrait` or leverage route middleware
- Use `Illuminate\Foundation\Auth\Access\AuthorizesRequests`

---

### 1.3 Material Status Validation

**Issue**: Material status validation rules and logic repeated in multiple places.

**Files Affected**:
- [app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L224-L248) - `updateMaterial()` validation
- [app/Services/OrderBusinessRulesService.php](app/Services/OrderBusinessRulesService.php#L85-L125) - `canMoveToQueue()` material check

**Pattern**:
```php
// Admin/Orders/Show.php (line 237-243)
$rules = [
    'material_source' => ['required', 'in:customer,jasa'],
    'material_status' => ['required', 'in:ready,po'],
];

if ($this->material_source === 'jasa') {
    $rules['fabric_id'] = ['required', 'exists:fabrics,id'];
}
```

**Recommendation**:
- Extract material validation to `MaterialValidator` class
- Create `MaterialStatusEnum` for valid values

---

### 1.4 Cancellation Logic Duplication

**Issue**: Order cancellation eligibility checks repeated across components.

**Files Affected**:
- [app/Livewire/Customer/Orders/Show.php](app/Livewire/Customer/Orders/Show.php#L187-L198) - `canCancelOrder()` in Show component
- [app/Livewire/Customer/Orders/CancelOrder.php](app/Livewire/Customer/Orders/CancelOrder.php#L30-L44) - Duplicate check in CancelOrder component

**Pattern**:
```php
// Show.php (line 187-198)
public function canCancelOrder(): bool
{
    $nonCancellableStatuses = ['dijahit', 'selesai_produksi', 'siap_diambil', 'selesai', 'ditolak', 'dibatalkan'];
    
    if (in_array($this->order->status, $nonCancellableStatuses)) {
        return false;
    }
    
    if ($this->order->payments->where('status', 'terverifikasi')->isNotEmpty()) {
        return false;
    }
    
    return true;
}

// CancelOrder.php (line 30-44) - IDENTICAL code
public function canCancelOrder(): bool
{
    $nonCancellableStatuses = ['dijahit', 'selesai_produksi', 'siap_diambil', 'selesai', 'ditolak', 'dibatalkan'];
    
    if (in_array($this->order->status, $nonCancellableStatuses)) {
        return false;
    }
    
    if ($this->order->payments->where('status', 'terverifikasi')->isNotEmpty()) {
        return false;
    }
    
    return true;
}
```

**Recommendation**:
- Move to Order model as `canBeCancelled()` method
- Call from both components

---

### 1.5 Status Transition Logic Validation

**Issue**: Validation of status transitions scattered across code.

**Files Affected**:
- [app/Services/OrderBusinessRulesService.php](app/Services/OrderBusinessRulesService.php#L28-65) - `canTransition()`
- [app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L60-85) - Inline status update with notification
- [app/Livewire/Admin/Payments/Index.php](app/Livewire/Admin/Payments/Index.php#L65-120) - Auto-advance logic repeated

**Issue**: Payment approval auto-advance logic in `AdminPaymentsIndex` duplicates status checks.

---

## 2. COMPLEX FUNCTIONS (KISS Principle Violations)

### 2.1 Admin Orders Show Component - God Object Pattern

**File**: [app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L1-END)

**Issue**: Massive component with 20+ public properties managing multiple concerns:

```php
// Line 10-38 - Too many state properties
public string $rejectionReason = '';
public bool $showRejectForm = false;
public string $dpAmount = '';
public bool $showDpForm = false;
public string $material_source = '';
public string $material_status = '';
public ?int $fabric_id = null;
public string $poDays = '';
public bool $showMaterialForm = false;
public string $editEstimatedPrice = '';
public bool $showPriceForm = false;
public string $productionDays = '';
public bool $showProductionForm = false;
public ?string $notes = null;
public bool $showDesignModal = false;
public ?string $designPreviewUrl = null;
public ?string $designPreviewName = null;
```

**Methods**: 15+ public action methods mixing concerns:
- Rejection logic
- DP amount setting
- Material management
- Price editing
- Production tracking
- Design preview

**Recommendation**:
- Split into multiple focused Livewire components
- Create dedicated components:
  - `AdminOrderRejection`
  - `AdminOrderPayment`
  - `AdminOrderMaterial`
  - `AdminOrderProduction`

---

### 2.2 Create Order Validation Complexity

**File**: [app/Livewire/Customer/Orders/Create.php](app/Livewire/Customer/Orders/Create.php#L46-85)

**Issue**: Complex rules() method with conditional validation spanning 40+ lines:

```php
protected function rules(): array
{
    $rules = [
        'service_id' => ['required', 'exists:services,id'],
        'quantity' => ['required', 'integer', 'min:1'],
        'notes' => ['nullable', 'string', 'max:2000'],
        'design_file' => $this->requiresDesignFile()
            ? ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120']
            : ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
    ];

    if ($this->selectedServiceType() === 'vermak') {
        $rules['selected_alterations'] = ['required', 'array', 'min:1'];
        $rules['selected_alterations.*'] = ['exists:alteration_options,id'];
    }

    return $rules;
}
```

**Problem**: Multiple method calls within rules() definition; hard to test; mixing concerns.

**Recommendation**:
- Extract to `CreateOrderRequest` Form Request class
- Move conditional logic to request class methods

---

### 2.3 Payment Approval Auto-Advance Logic

**File**: [app/Livewire/Admin/Payments/Index.php](app/Livewire/Admin/Payments/Index.php#L59-120)

**Issue**: Single `approvePayment()` method contains multiple concerns (65+ lines):
1. Payment verification
2. Order status check
3. Material status evaluation
4. Auto-status transition
5. Customer notification
6. Multiple database queries

```php
public function approvePayment(int $paymentId): void
{
    // 1. Validation
    $payment = Payment::with([...])->findOrFail($paymentId);
    if ($payment->status !== 'menunggu_verifikasi') {
        session()->flash('error', 'Pembayaran sudah diproses.');
        return;
    }
    
    // 2. Update payment
    $payment->update([...]);
    
    // 3. Notify customer
    if ($payment->customer) {
        $payment->customer->notify(new PaymentStatusUpdated(...));
    }
    
    // 4. Complex material and status check (lines 79-120)
    if ($payment->payment_type === 'dp') {
        if (in_array($order->status, ['menunggu_dp', 'menunggu_fitting'])) {
            if ($order->material_source === 'customer' || $order->material_status === 'ready') {
                // ... 20 lines of nested logic
            } else {
                // ... 10 more lines
            }
        }
    }
    
    // 5. More status transitions (lines 120+)
    if ($payment->payment_type === 'pelunasan' && $order->status === 'selesai_produksi') {
        // ... 15 more lines
    }
}
```

**Recommendation**:
- Create `PaymentApprovalService` to handle business logic
- Extract status auto-advance to `OrderStatusTransitionService`
- Keep component for UI only

---

### 2.4 Order Status Step Building

**File**: [app/Livewire/Customer/Orders/Show.php](app/Livewire/Customer/Orders/Show.php#L47-88)

**Issue**: `buildStatusSteps()` method has deep logic with repetitive array merging:

```php
private function buildStatusSteps(): void
{
    $needsFitting = $this->order->requiresFitting();
    
    $steps = [
        ['key' => 'menunggu_konfirmasi', ...],
    ];
    
    if ($needsFitting) {
        $steps[] = ['key' => 'menunggu_fitting', ...];
    }
    
    if ($this->order->service->type !== 'vermak') {
        $steps = array_merge($steps, [
            ['key' => 'menunggu_dp', ...],
            ['key' => 'menunggu_bahan', ...],
        ]);
    } else {
        $steps = array_merge($steps, [
            ['key' => 'menunggu_pakaian_dikirim', ...],
            ['key' => 'pakaian_dikirim', ...],
        ]);
    }
    
    $steps = array_merge($steps, [
        // ... 5 more items
    ]);
    
    $this->statusSteps = $steps;
}
```

**Recommendation**:
- Move to `OrderTimelineBuilder` class in services
- Implement strategy pattern for different service types

---

## 3. MISSING DOCUMENTATION

### 3.1 Incomplete Method Documentation

**Files with Missing PHPDoc**:

1. **[app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L224)**
   - `updateMaterial()` - No docblock (line 224)
   - `markMaterialReady()` - No docblock (line 258)
   - `processMoveToQueue()` - No docblock
   - Multiple update methods lack parameter and return documentation

2. **[app/Livewire/Customer/Payments/Create.php](app/Livewire/Customer/Payments/Create.php#L108)**
   - `updatedPaymentMethod()` - No docblock (line 108)
   - `updatedProofFile()` - No docblock (line 114)
   - `requiresProofFile()` - No docblock (line 140)

3. **[app/Http/Controllers/PaymentController.php](app/Http/Controllers/PaymentController.php#L140)**
   - `proofFile()` - Missing return type (line 140)
   - Method appears incomplete (abruptly ends)

4. **[app/Services/OrderBusinessRulesService.php](app/Services/OrderBusinessRulesService.php#L310)**
   - `getBookedSlots()` - Incomplete documentation (line 310+)
   - Return type not fully documented

### 3.2 Inconsistent Documentation Style

**Example Inconsistency**:

```php
// Well documented (Order Model line 119-131)
/**
 * Menghitung status pembayaran berdasarkan relasi payments.
 *
 * Return values:
 * - 'belum_bayar'    : belum ada pembayaran terverifikasi
 * - 'dp'             : hanya DP yang terverifikasi
 * - 'lunas'          : pelunasan sudah terverifikasi
 * - 'menunggu'       : ada pembayaran yang menunggu verifikasi
 */
public function getPaymentStatusAttribute(): string

// Poorly documented (Order Model line 166-170)
/**
 * Cek apakah pesanan membutuhkan fitting.
 */
public function requiresFitting(): bool

// Missing return type documentation
/**
 * Cek apakah pesanan sedang aktif (belum selesai/ditolak/dibatalkan).
 */
public function isActive(): bool
```

### 3.3 Missing Business Logic Comments

**Files Missing Context Documentation**:

1. **[app/Livewire/Customer/Orders/Create.php](app/Livewire/Customer/Orders/Create.php#L160-185)** - `submit()` method
   - No explanation for estimation logic
   - Alteration details JSON encoding not explained
   - Price calculation for Vermak not documented

2. **[app/Services/OrderBusinessRulesService.php](app/Services/OrderBusinessRulesService.php#L195-235)** - `getAvailableSlots()` method
   - Complex appointment slot calculation not documented
   - Nested loop logic unclear
   - Time formatting not explained

3. **[app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L272-298)**
   - `processMoveToQueue()` method not shown but referenced
   - Auto-transition logic not documented

---

## 4. CODE QUALITY ISSUES

### 4.1 Tight Coupling to Database Queries

**Issue**: Livewire components directly execute database queries instead of using repositories or services.

**Files Affected**:

1. **[app/Livewire/Customer/Orders/Create.php](app/Livewire/Customer/Orders/Create.php#L28-32)**
   ```php
   public function mount(): void
   {
       $this->services = Service::query()->orderBy('name')->get();
       $this->alterationOptions = \App\Models\AlterationOption::orderBy('name')->get();
   }
   ```

2. **[app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L201)**
   ```php
   public function updatedFabricId($value): void
   {
       if ($this->material_source === 'jasa' && $value) {
           $fabric = Fabric::find($value);  // Direct DB query
           if ($fabric) {
               // ...
           }
       }
   }
   ```

3. **[app/Livewire/Admin/Payments/Index.php](app/Livewire/Admin/Payments/Index.php#L59)**
   ```php
   public function approvePayment(int $paymentId): void
   {
       $payment = Payment::with(['order.payments', 'order.service', 'customer'])
           ->findOrFail($paymentId);  // Hard-coded eager loading
   }
   ```

**Recommendation**:
- Create repository layer or query builders
- Use services for complex queries
- Implement `FabricRepository::find()` pattern

---

### 4.2 Missing Input Validation & Sanitization

**Issue**: Some user inputs not properly validated before use.

1. **[app/Livewire/Customer/Orders/Create.php](app/Livewire/Customer/Orders/Create.php#L156)**
   - Design file upload allows `jpg, jpeg, png` but no SVG validation for custom designs
   - File size limit (5MB) reasonable but not documented

2. **[app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L142)**
   - `rejectionReason` accepts 10-2000 chars but no HTML sanitization mentioned
   - Should use `strip_tags()` or similar

3. **[app/Models/Order.php](app/Models/Order.php#L20-35)**
   - `notes` field max 2000 chars but should specify if HTML allowed

**Recommendation**:
- Add HTML sanitization to text fields
- Document allowed HTML tags
- Use `purify()` helper for rich text

---

### 4.3 Incomplete Error Handling

**Issue**: Many methods fail silently or return errors without proper logging.

1. **[app/Http/Controllers/PaymentController.php](app/Http/Controllers/PaymentController.php#L140-150)**
   ```php
   public function proofFile(Payment $payment)
   {
       $filePath = storage_path('app/private/' . $payment->proof_file_path);
       
       if (! file_exists($filePath)) {
           abort(404, 'File bukti pembayaran tidak ditemukan.');
       }
       
       return response()->file($filePath);  // No error handling
   }
   ```
   - File read errors not caught
   - No logging on failed access attempts

2. **[app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L195-210)**
   ```php
   public function updatedFabricId($value): void
   {
       if ($this->material_source === 'jasa' && $value) {
           $fabric = Fabric::find($value);
           if ($fabric) {  // Silent fail if fabric not found
               // ...
           }
       }
   }
   ```

**Recommendation**:
- Add try-catch blocks
- Log all failures with context
- Use `response()->notFound()` explicitly

---

### 4.4 N+1 Query Problems

**Issue**: Potential N+1 queries in several places.

1. **[app/Livewire/Customer/Orders/Show.php](app/Livewire/Customer/Orders/Show.php#L37)**
   ```php
   public function mount(Order $order): void
   {
       $this->order = $order->load(['service', 'fabric', 'statusLogs.user', 'designFiles', 'payments', 'appointment', 'testimonial']);
   }
   ```
   - Good eager loading, but in `syncProgress()` method status steps are re-calculated
   - Potential query if accessed again

2. **[app/Services/OrderBusinessRulesService.php](app/Services/OrderBusinessRulesService.php#L85-92)**
   ```php
   public function canMoveToQueue(Order $order): array
   {
       $blockingReasons = [];
       $order->loadMissing(['service', 'appointment', 'payments']);  // Load within loop-called method?
   }
   ```
   - OK as-is, but if called in loop, causes N+1

---

### 4.5 Magic Strings and Hardcoded Values

**Issue**: Business logic constants hardcoded throughout codebase.

1. **[app/Services/OrderBusinessRulesService.php](app/Services/OrderBusinessRulesService.php#L11-16)**
   ```php
   private const REQUIRES_FITTING_TYPES = ['seragam', 'custom'];
   private const OPEN_HOUR = 8;
   private const CLOSE_HOUR = 19;
   private const BREAK_START = 12;
   private const BREAK_END = 13;
   private const APPOINTMENT_DURATION_HOURS = 1;
   ```
   - Good: constants defined in service
   - Bad: Same values hardcoded elsewhere

2. **[app/Livewire/Admin/Orders/Show.php](app/Livewire/Admin/Orders/Show.php#L70-73)**
   ```php
   if ($serviceType === 'vermak') {
       $newStatus = 'menunggu_pakaian_dikirim';
   } else {
       $newStatus = app(OrderBusinessRulesService::class)->requiresFitting($serviceType)
           ? 'menunggu_fitting'
           : 'menunggu_dp';
   }
   ```
   - Status strings hardcoded
   - Should use Enum or constants

3. **[app/Livewire/Customer/Orders/Show.php](app/Livewire/Customer/Orders/Show.php#L179-180)**
   ```php
   $nonCancellableStatuses = ['dijahit', 'selesai_produksi', 'siap_diambil', 'selesai', 'ditolak', 'dibatalkan'];
   ```
   - Same status list duplicated in CancelOrder component

**Recommendation**:
- Create `OrderStatus` enum class
- Create `ServiceType` enum class
- Create `PaymentStatus` enum class

---

### 4.6 Type Inconsistencies

**Issue**: Inconsistent type declarations across similar methods.

```php
// Livewire/Customer/Orders/Create.php (line 14-17)
public ?int $service_id = null;
public ?int $quantity = 1;  // Mixed nullable with default
public ?string $notes = null;
public $design_file;  // Missing type

// Admin/Orders/Show.php (line 11-15)
public string $rejectionReason = '';  // Non-nullable with empty string
public bool $showRejectForm = false;
public string $dpAmount = '';  // Should be float?
public string $material_source = '';
public ?int $fabric_id = null;
```

**Recommendation**:
- Standardize numeric string fields vs numeric types
- Declare all public properties with strict types
- Use typed properties consistently

---

## 5. INCONSISTENT CODING PATTERNS

### 5.1 Inconsistent Method Naming

**Issue**: Similar actions use different naming conventions.

```php
// Livewire components use mixed patterns:
// Open/Close pattern
openRejectForm()   // Admin/Orders/Show.php:126
closeRejectForm()  // Admin/Orders/Show.php:131

// Show/Hide pattern
showRejectForm     // Property in same file

// Start/Cancel pattern
startReject()      // Admin/Payments/Index.php:47
cancelReject()     // Admin/Payments/Index.php:52

// Toggle could be used instead
toggleRejectForm()
```

**Recommendation**:
- Standardize to `show*/hide*` or `open*/close*`
- Document convention in CONTRIBUTING.md

---

### 5.2 Inconsistent Authorization Checks

**Issue**: Authorization checks vary in pattern and placement.

```php
// Pattern 1: Early return in mount (Livewire)
public function mount(Order $order): void
{
    if (!auth()->check() || !auth()->user()->isCustomer()) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
    
    if ($order->customer_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
    }
}

// Pattern 2: Gate::authorize() (Controller)
public function proofFile(Payment $payment)
{
    Gate::authorize('viewProof', $payment);
}

// Pattern 3: Controller constructor check
if (!auth()->check() || !auth()->user()->isAdmin()) {
    abort(403, 'Anda tidak memiliki akses ke halaman ini.');
}
```

**Recommendation**:
- Use route middleware for role checks
- Use policies for resource access
- Consistent `Gate::authorize()` or policy pattern

---

### 5.3 Inconsistent Validation Rule Messages

**Issue**: Validation messages use different formats and languages.

```php
// Indonesian
'service_id.required' => 'Layanan harus dipilih.',
'service_id.exists' => 'Layanan yang dipilih tidak valid.',

// Mixed case and style
'quantity.required' => 'Jumlah harus diisi.',
'quantity.integer' => 'Jumlah harus berupa angka.',
'quantity.min' => 'Jumlah minimal adalah 1.',

// Some with Rp format
'amount.min' => 'Nominal pembayaran minimal Rp 1.000.',

// Different style
'proof_file.required' => 'Bukti pembayaran wajib diunggah untuk metode transfer/QRIS.',
'proof_file.file' => 'Bukti pembayaran harus berupa file yang valid.',
```

**Recommendation**:
- Create translation file `resources/lang/id/validation.php`
- Use Laravel's `validation.custom` for custom messages
- Document capitalization and format conventions

---

### 5.4 Inconsistent Status Update Patterns

**Issue**: Updating order status varies across components.

```php
// Pattern 1: Livewire with status log
$this->order->update(['status' => 'dibatalkan']);
$this->order->statusLogs()->create([...]);

// Pattern 2: Different order (notification then update)
$this->order->update(['status' => 'ditolak']);
OrderStatusLog::create([...]);
$customer->notify(...);

// Pattern 3: Reload then update
$order = $order->fresh(['payments', 'service', 'appointment']);
$order->update(['status' => 'dalam_antrian']);
OrderStatusLog::create([...]);
```

**Recommendation**:
- Create `OrderStatusTransitionService::transition(Order $order, string $newStatus)`
- Always use same order: validate → update → log → notify
- Use events/listeners for notifications

---

### 5.5 Inconsistent Array Handling

**Issue**: Different approaches to array operations.

```php
// Pattern 1: Using array_merge()
$steps = array_merge($steps, [
    ['key' => 'menunggu_dp', ...],
    ['key' => 'menunggu_bahan', ...],
]);

// Pattern 2: Using array spread
$steps = [
    ...existing,
    ...newItems,
];

// Pattern 3: append()
$steps[] = [];

// Pattern 4: Using collection methods
$steps = collect($steps)->merge([...])->toArray();
```

**Recommendation**:
- Use consistent approach: array spread `[...$old, ...$new]` is modern PHP 7.4+
- Document decision in CONTRIBUTING.md

---

### 5.6 Inconsistent Enum Patterns

**Issue**: Status values are strings instead of enums.

```php
// Current: Magic strings
$payment->status = 'menunggu_verifikasi';  // Line: many places

// Should be:
$payment->status = PaymentStatus::PENDING_VERIFICATION;
```

**Files Affected**:
- Order statuses: [app/Services/OrderBusinessRulesService.php](app/Services/OrderBusinessRulesService.php#L28-65)
- Payment statuses: Multiple files
- Appointment statuses: References in queries

**Recommendation**:
- Create PHP 8.1 Enums:
  ```php
  enum OrderStatus: string {
      case PENDING_CONFIRMATION = 'menunggu_konfirmasi';
      case IN_QUEUE = 'dalam_antrian';
      // ...
  }
  ```

---

## 6. SUMMARY TABLE

| Issue Category | Severity | Files Affected | Count |
|---|---|---|---|
| DRY Violations | High | 7 files | 5 major patterns |
| Complex Functions | High | 3 files | 4 methods |
| Missing Documentation | Medium | 4 files | 15+ methods |
| Code Quality Issues | High | 6 files | 6 categories |
| Inconsistent Patterns | Medium | 8+ files | 6 patterns |

---

## 7. PRIORITY REFACTORING ROADMAP

### Phase 1 (Critical) - High Impact, Low Effort
1. Extract payment status logic to `PaymentService`
2. Create `OrderStatus` and `PaymentStatus` enums
3. Move cancellation logic to Order model
4. Add missing PHPDoc blocks

### Phase 2 (Important) - High Impact, Medium Effort
1. Split `AdminOrdersShow` component into focused components
2. Extract authorization checks to middleware/traits
3. Create `OrderStatusTransitionService`
4. Implement repository pattern for queries

### Phase 3 (Nice to Have) - Medium Impact, Medium Effort
1. Create translation files for validation messages
2. Implement event/listener pattern for notifications
3. Add comprehensive error logging
4. Create `OrderTimelineBuilder` service

### Phase 4 (Polish) - Low Impact, Low Effort
1. Standardize method naming conventions
2. Add CONTRIBUTING.md with coding standards
3. Add inline comments for complex logic
4. Create test fixtures and factories

---

## 8. RECOMMENDATIONS FOR IMPROVEMENT

### Code Architecture
- [ ] Implement single responsibility principle in Livewire components
- [ ] Create Service layer for business logic
- [ ] Use Repository pattern for data access
- [ ] Implement Event/Listener pattern for notifications

### Type Safety
- [ ] Upgrade to PHP 8.1+ enums
- [ ] Use strict types in all files
- [ ] Add PHPStan/Psalm static analysis

### Testing
- [ ] Add unit tests for services
- [ ] Add feature tests for workflows
- [ ] Add integration tests for payment flows

### Documentation
- [ ] Add CONTRIBUTING.md with standards
- [ ] Add architecture diagrams
- [ ] Document order state transitions
- [ ] Create API documentation

---

**End of Report**
