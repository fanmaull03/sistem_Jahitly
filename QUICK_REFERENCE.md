# QUICK REFERENCE - Jahitly Code Patterns

Panduan cepat untuk common tasks & patterns di Jahitly project.

---

## ✨ Common Patterns

### 1. Check Order Status

**WRONG ❌**:
```php
if ($order->status === 'selesai') { }
if ($order->status === 'dibatalkan' || $order->status === 'ditolak') { }
```

**RIGHT ✅**:
```php
use App\Enums\OrderStatus;

// Single status check
if ($order->status === OrderStatus::SELESAI->value) { }

// Multiple status check
$terminalStatuses = [
    OrderStatus::DIBATALKAN->value,
    OrderStatus::DITOLAK->value,
    OrderStatus::SELESAI->value,
];
if (in_array($order->status, $terminalStatuses)) { }

// Use model method
if ($order->isActive()) { } // Already checks terminal statuses
```

---

### 2. Check Payment Status

**WRONG ❌**:
```php
$verified = $order->payments
    ->where('status', 'terverifikasi')
    ->where('payment_type', 'pelunasan')
    ->first();
```

**RIGHT ✅**:
```php
use App\Services\PaymentService;

$service = app(PaymentService::class);
$verified = $service->getVerifiedFinalPayment($order);

// Or check if any verified exists
if ($service->hasAnyVerifiedPayment($order)) { }

// Or check if order can be cancelled
if ($service->canOrderBeCancelled($order)) { }
```

---

### 3. Cancel Order

**WRONG ❌**:
```php
// Logic scattered in component
$this->order->update([
    'status' => 'dibatalkan',
    'cancelled_at' => now(),
    'cancellation_reason' => $reason,
]);
$this->order->statusLogs()->create([...]);
```

**RIGHT ✅**:
```php
// Use model method
$this->order->cancel($reason);

// Or in service
app(OrderService::class)->cancelOrder($this->order, $reason);
```

---

### 4. Livewire Authorization in Mount

**Template**:
```php
public function mount(Order $order): void
{
    // Check user authenticated
    if (!auth()->check() || !auth()->user()->isCustomer()) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    // Check user is owner
    if ($order->customer_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
    }

    $this->order = $order->load([...]); // Eager load relations
}
```

---

### 5. Calculate Payment Status

**WRONG ❌**:
```php
$hasLunas = $order->payments
    ->where('payment_type', 'pelunasan')
    ->where('status', 'terverifikasi')
    ->isNotEmpty();

if ($hasLunas) {
    $status = 'lunas';
} // ... more conditions
```

**RIGHT ✅**:
```php
$paymentStatus = app(PaymentService::class)
    ->calculateOrderPaymentStatus($order);
    
// Returns: 'lunas' | 'dp' | 'menunggu' | 'belum_bayar'
```

---

### 6. Livewire Component Structure

**Template**:
```php
/**
 * ComponentName - Brief description
 * 
 * Full description and features.
 */
class MyComponent extends Component
{
    // ──────────────────────────────────────────────────────────
    // Properties
    // ──────────────────────────────────────────────────────────

    /** @var Model */
    public Model $model;
    
    /** Filter value for searching */
    public string $searchFilter = '';

    // ──────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────

    /**
     * Mount component
     */
    public function mount(Model $model): void
    {
        // Validation logic
        $this->model = $model;
    }

    // ──────────────────────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────────────────────

    /**
     * Get items for display
     */
    public function getItemsProperty()
    {
        return $this->model->where('filter', $this->searchFilter)->get();
    }

    // ──────────────────────────────────────────────────────────
    // Actions
    // ──────────────────────────────────────────────────────────

    /**
     * Save action
     */
    public function save(): void
    {
        $this->validate([...]);
        $this->model->update([...]);
    }

    // ──────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.component', [...])->layout('layouts.app');
    }
}
```

---

### 7. Validation with Custom Messages

**Template**:
```php
$this->validate(
    [
        'email' => ['required', 'email'],
        'amount' => ['required', 'numeric', 'min:1000'],
        'reason' => ['required', 'string', 'min:10', 'max:500'],
    ],
    [
        'email.required' => 'Email harus diisi.',
        'email.email' => 'Format email tidak valid.',
        'amount.required' => 'Jumlah harus diisi.',
        'amount.numeric' => 'Jumlah harus berupa angka.',
        'amount.min' => 'Jumlah minimal Rp 1.000.',
        'reason.required' => 'Alasan harus diisi.',
        'reason.min' => 'Alasan minimal 10 karakter.',
        'reason.max' => 'Alasan maksimal 500 karakter.',
    ]
);
```

---

### 8. Get Eloquent Relations Safely

**WRONG ❌**:
```php
public function mount(Order $order): void
{
    $this->order = $order; // Might cause N+1 queries
    $this->service = $this->order->service->name; // Not eager loaded
}
```

**RIGHT ✅**:
```php
public function mount(Order $order): void
{
    // Eager load relations
    $this->order = $order->load([
        'service',
        'customer',
        'payments',
        'statusLogs.user',
    ]);
}
```

---

### 9. Flash Messages

**Livewire**:
```php
// Success
session()->flash('success', 'Data berhasil disimpan.');

// Error
session()->flash('error', 'Terjadi kesalahan.');

// Info
session()->flash('info', 'Silakan verifikasi email.');
```

**Blade Access**:
```blade
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
```

---

### 10. Query with Pagination

**Livewire**:
```php
use Livewire\WithPagination;

class MyComponent extends Component
{
    use WithPagination;

    public string $searchFilter = '';

    public function updatingSearchFilter(): void
    {
        $this->resetPage(); // Reset to page 1 when filter changes
    }

    public function getItemsProperty()
    {
        return $this->user->items()
            ->where('name', 'like', '%' . $this->searchFilter . '%')
            ->latest()
            ->paginate(15);
    }
}
```

---

### 11. Service Injection

**Livewire**:
```php
use App\Services\PaymentService;

class MyComponent extends Component
{
    /**
     * Using service method
     */
    public function checkPayment(): void
    {
        $service = app(PaymentService::class);
        $verified = $service->getVerifiedPayments($this->order);
    }
}
```

**Controller**:
```php
public function show(PaymentService $service, Order $order)
{
    $verified = $service->getVerifiedPayments($order);
}
```

---

### 12. Notify User

**Livewire**:
```php
use App\Notifications\OrderStatusUpdated;

public function updateStatus($newStatus): void
{
    $this->order->update(['status' => $newStatus]);
    
    // Notify customer
    $this->order->customer->notify(
        new OrderStatusUpdated(
            $this->order,
            'Order #' . $this->order->order_number . ' updated'
        )
    );
}
```

---

## 🎯 Debugging Tips

### Check What's in Livewire Property

```php
public function debugProperty(): void
{
    dd([
        'order' => $this->order,
        'payments' => $this->order->payments,
        'status' => $this->order->status,
    ]);
}
```

### Check Database Queries

```bash
# In local env, enable query logging
# config/logging.php: 'queries' => true
# Then check storage/logs/laravel.log
```

### Reload Component

```php
// Reset all properties
$this->reset();

// Reset specific property
$this->reset('searchFilter');

// Dispatch refresh
$this->dispatch('refreshComponent');
```

---

## 📊 Common Mistakes

### ❌ Mistake 1: Hardcoded Strings
```php
if ($order->status === 'selesai') { } // Wrong
```
**Fix**: Use enum
```php
if ($order->status === OrderStatus::SELESAI->value) { } // Right
```

### ❌ Mistake 2: Duplicate Queries
```php
$verified = $order->payments->where('status', 'terverifikasi');
$verified = $order->payments->where('status', 'terverifikasi'); // Called twice!
```
**Fix**: Use service
```php
$verified = app(PaymentService::class)->getVerifiedPayments($order); // Once
```

### ❌ Mistake 3: No Authorization
```php
public function show(Order $order): View
{
    return view('order.show', ['order' => $order]); // Anyone can see!
}
```
**Fix**: Add authorization
```php
public function show(Order $order): View
{
    $this->authorize('view', $order);
    return view('order.show', ['order' => $order]);
}
```

### ❌ Mistake 4: N+1 Query Problem
```php
$orders = Order::all(); // Loads all
foreach ($orders as $order) {
    $service = $order->service; // 1 query per order!
}
```
**Fix**: Eager load
```php
$orders = Order::with('service')->get();
foreach ($orders as $order) {
    $service = $order->service; // Already loaded
}
```

---

## 📞 Need Help?

1. Check [CONTRIBUTING.md](CONTRIBUTING.md) - Full guidelines
2. Check [CODE_REFACTORING_GUIDE.md](CODE_REFACTORING_GUIDE.md) - Detailed explanations
3. Look at similar code in project - Best examples
4. Ask team lead - When stuck

---

**Last Updated**: 2026-07-01
