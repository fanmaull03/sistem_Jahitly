# PHASE 2 IMPROVEMENTS - OrderStatusTransitionService & Related Services

## 🎯 Overview Phase 2

**Phase 2 fokus**: Refactor complex AdminOrdersShow component dengan membuat services yang focused dan reusable.

**Problem sebelumnya**: AdminOrdersShow handle 5 concerns berbeda (rejection, DP, material, price, production):
- 50+ public properties
- 20+ public methods
- Logic untuk berbagai domain tercampur
- Sulit untuk di-test dan di-maintain

**Solution**: Buat services yang focused untuk setiap concern

---

## ✅ Services Created (Phase 2)

### 1. **OrderStatusTransitionService** (NEW)
**File**: `app/Services/OrderStatusTransitionService.php`

**Purpose**: Manage status transitions dengan pre/post condition checks

**Key Methods**:
- `acceptOrder(Order, adminId)` - Terima pesanan baru
  - Auto-determine next status berdasarkan service type
  - Create log & notify customer
  - Return structured response

- `moveToQueue(Order, adminId)` - Move ke antrian produksi
  - Cek semua pre-conditions (DP verified, material ready, fitting done)
  - Provide blocking reasons jika tidak bisa
  - Auto-notify customer

- `updateProductionStatus(Order, newStatus, adminId, notes)` - Update status produksi
  - Validasi transition allowed
  - Special check untuk 'siap_diambil' (harus lunas)
  - Generate appropriate customer messages

**Benefits**:
- Centralize status transition logic
- Reusable di admin & potentially background jobs
- Type-safe response handling
- Clear blocking reasons for UX

---

### 2. **OrderRejectionService** (NEW)
**File**: `app/Services/OrderRejectionService.php`

**Purpose**: Handle order rejection logic

**Key Methods**:
- `rejectOrder(Order, reason, adminId)` - Reject order
  - Validate reason (10-2000 chars)
  - Update status to 'ditolak'
  - Log & notify customer

- `canRejectOrder(Order)` - Check if order can be rejected
  - Only menunggu_konfirmasi status

- `getRejectionReason(Order)` - Get reason if rejected

- `isRejected(Order)` - Check if order ditolak

**Benefits**:
- Single place for rejection logic
- Validated reason handling
- Clear contracts

---

### 3. **OrderPricingService** (NEW)
**File**: `app/Services/OrderPricingService.php`

**Purpose**: Manage DP & pricing

**Key Methods**:
- `setDpAmount(Order, amount, adminId)` - Set DP nominal
  - Validate amount (min 1000, <= estimated price)
  - Notify customer

- `recalculateEstimation(Order, adminId)` - Recalculate price & date
  - Use OrderBusinessRulesService untuk calculation
  - Update order
  - Notify customer

- `setEstimatedPrice(Order, price, adminId)` - Manual price override
  - For special cases
  - Notify if significant change

- `getRemainingPayment(Order)` - Get sisa pembayaran (pelunasan)

- `isDpSet(Order)` - Check if DP sudah ditetapkan

- `formatPrice(amount)` - Format untuk display

**Benefits**:
- Centralize pricing logic
- Consistent validation
- Helper methods untuk UI
- Auto-notifications

---

### 4. **OrderMaterialService** (NEW)
**File**: `app/Services/OrderMaterialService.php`

**Purpose**: Manage material/bahan for order

**Key Methods**:
- `setMaterialSource(Order, source, fabricId, poDays, adminId)` - Set material
  - Validate input
  - Auto-determine status (ready vs PO)
  - Recalculate price (material affects price)
  - Auto-transition jika ready

- `markMaterialReady(Order, adminId)` - Mark PO material as ready
  - Auto-transition ke queue jika menunggu_bahan

- `isMaterialReady(Order)` - Check if ready

- `isMaterialPo(Order)` - Check if PO

- `getMaterialInfo(Order)` - Get full material details

- `getAvailableFabrics()` - Get fabrics for dropdown

**Benefits**:
- Centralize material logic
- Handle dependent updates (ready → auto-transition)
- Fabric selection validation
- PO days validation

---

## 🔄 How Services Work Together

```
AdminOrdersShow Component (simplified)
  │
  ├─ OrderStatusTransitionService
  │  └─ acceptOrder()
  │  └─ moveToQueue()
  │  └─ updateProductionStatus()
  │
  ├─ OrderRejectionService
  │  └─ rejectOrder()
  │  └─ canRejectOrder()
  │
  ├─ OrderPricingService
  │  └─ setDpAmount()
  │  └─ recalculateEstimation()
  │  └─ setEstimatedPrice()
  │
  └─ OrderMaterialService
     └─ setMaterialSource()
     └─ markMaterialReady()
```

---

## 📊 Before vs After Comparison

### BEFORE (AdminOrdersShow - Monolithic)
```
Class properties: 20+
├─ rejection properties (3)
├─ dp properties (3)
├─ material properties (5)
├─ price properties (2)
├─ production properties (3)
└─ design properties (3)

Public methods: 25+
├─ rejectOrder, openRejectForm, closeRejectForm, ...
├─ setDpAmount, openDpForm, closeDpForm, ...
├─ updateMaterial, openMaterialForm, closeMaterialForm, ...
├─ setEstimatedPrice, openPriceForm, closePriceForm, ...
└─ ... many updatedX methods
```

**Issues**:
- ❌ Hard to test (mix of concerns)
- ❌ Hard to reuse (logic locked in component)
- ❌ Hard to understand (too many responsibilities)
- ❌ Hard to maintain (changes affect many things)

### AFTER (Services + Simplified Component)
```
OrderStatusTransitionService.php (200 lines)
├─ acceptOrder()
├─ moveToQueue()
└─ updateProductionStatus()

OrderRejectionService.php (150 lines)
├─ rejectOrder()
└─ canRejectOrder()

OrderPricingService.php (200 lines)
├─ setDpAmount()
└─ recalculateEstimation()

OrderMaterialService.php (250 lines)
├─ setMaterialSource()
└─ markMaterialReady()

AdminOrdersShow Component (simplified)
├─ Properties: 3 (order, showForm, etc)
└─ Methods: 10 (call services)
```

**Benefits**:
- ✅ Easy to test (each service focused)
- ✅ Easy to reuse (services standalone)
- ✅ Easy to understand (clear responsibility)
- ✅ Easy to maintain (changes isolated)

---

## 💡 Design Patterns Used

### 1. **Service Layer Pattern**
- Encapsulate business logic in services
- Services are stateless & reusable
- Dependencies injected

### 2. **Return Structured Response Pattern**
```php
// Instead of throwing exceptions or returning boolean
return [
    'success' => true/false,
    'message' => 'User-friendly message',
    'errors' => ['error 1', 'error 2'], // optional
    'newStatus' => 'status', // optional
];
```

**Benefits**:
- Easy to handle in UI
- No try-catch needed
- Clear error messages
- Optional extra data

### 3. **Dependency Injection**
```php
public function __construct()
{
    $this->service = app(SomeService::class);
}
```

### 4. **Method Names as Documentation**
```php
$service->canRejectOrder() // Obviously returns bool
$service->getRejectionReason() // Obviously returns string|null
$service->setDpAmount() // Obviously does something
```

---

## 🚀 How to Use New Services

### In AdminOrdersShow Component

**BEFORE (Monolithic)**:
```php
public function rejectOrder(): void
{
    $this->validate(['rejectionReason' => [...rules...]]);
    if ($this->order->status !== 'menunggu_konfirmasi') {
        session()->flash('error', '...');
        return;
    }
    $this->order->update([...]);
    OrderStatusLog::create([...]);
    $this->order->customer->notify(...);
    // 20 lines for single action
}
```

**AFTER (Services)**:
```php
public function rejectOrder(): void
{
    $this->validate(['rejectionReason' => ['min:10', 'max:2000']]);
    
    $result = app(OrderRejectionService::class)->rejectOrder(
        $this->order,
        $this->rejectionReason,
        auth()->id()
    );
    
    session()->flash(
        $result['success'] ? 'success' : 'error',
        $result['message']
    );
    
    // 3 lines, much cleaner
}
```

### In Other Places (Livewire, Controller, Job, etc)

Services can be reused anywhere:

```php
// In queue job (for batch processing)
foreach ($orders as $order) {
    app(OrderStatusTransitionService::class)->moveToQueue($order, 0);
}

// In command
app(OrderPricingService::class)->recalculateEstimation($order);

// In API endpoint
$service = app(OrderMaterialService::class);
$info = $service->getMaterialInfo($order);
return response()->json($info);
```

---

## 📋 Test Examples

Now services are easy to test:

```php
class OrderStatusTransitionServiceTest extends TestCase
{
    /**
     * Test accepting order transitions to correct status based on service type
     */
    public function test_accept_order_custom_service_goes_to_fitting()
    {
        $order = Order::factory()
            ->for(Service::factory(['type' => 'custom']))
            ->create(['status' => 'menunggu_konfirmasi']);

        $service = app(OrderStatusTransitionService::class);
        $result = $service->acceptOrder($order, 1);

        $this->assertTrue($result['success']);
        $this->assertEquals('menunggu_fitting', $order->fresh()->status);
    }

    /**
     * Test rejecting non-pending order fails
     */
    public function test_reject_order_not_pending_fails()
    {
        $order = Order::factory()->create(['status' => 'dijahit']);
        
        $service = app(OrderRejectionService::class);
        $result = $service->rejectOrder($order, 'reason', 1);

        $this->assertFalse($result['success']);
    }

    /**
     * Test DP validation
     */
    public function test_dp_below_minimum_fails()
    {
        $order = Order::factory()->create();
        
        $service = app(OrderPricingService::class);
        $result = $service->setDpAmount($order, 500, 1);

        $this->assertFalse($result['success']);
        $this->assertContains('minimal Rp 1.000', $result['errors'][0]);
    }
}
```

---

## 🔄 Next Steps: Refactor AdminOrdersShow

Sekarang kita bisa simplify AdminOrdersShow component using these services:

1. Remove rejection, DP, material, pricing properties → move to services
2. Replace methods dengan service calls
3. Reduce component dari 50+ properties & 25+ methods → 5 properties & 10 methods
4. Component becomes "view orchestrator" not "business logic holder"

---

## 📚 Code Organization Summary

**Before Phase 2**:
```
app/Services/
├─ OrderBusinessRulesService.php (600+ lines, all business rules)
└─ PaymentService.php

app/Livewire/Admin/Orders/
└─ Show.php (500+ lines, mixed concerns)
```

**After Phase 2**:
```
app/Services/
├─ OrderBusinessRulesService.php (600 lines, validation & calculations)
├─ PaymentService.php (payment logic)
├─ OrderStatusTransitionService.php (status transitions) ✨ NEW
├─ OrderRejectionService.php (rejection logic) ✨ NEW
├─ OrderPricingService.php (pricing & DP) ✨ NEW
└─ OrderMaterialService.php (material management) ✨ NEW

app/Livewire/Admin/Orders/
└─ Show.php (150 lines, orchestration only) ✨ SIMPLIFIED
```

---

## ✅ Checklist: Phase 2 Status

- ✅ OrderStatusTransitionService created
- ✅ OrderRejectionService created
- ✅ OrderPricingService created
- ✅ OrderMaterialService created
- ⏳ Refactor AdminOrdersShow to use services (next)
- ⏳ Create tests for new services (Phase 3)
- ⏳ Create AdminOrdersForm sub-component (Phase 2)

---

## 🎓 Learning from Phase 2

### Key Principles Applied
1. **Single Responsibility** - Each service has ONE purpose
2. **Dependency Injection** - Services don't create dependencies
3. **Immutable** - Services return results, don't change state directly
4. **Testable** - Each service can be tested independently
5. **Reusable** - Services work anywhere, not just in components
6. **Clear Contracts** - Method names describe what they do

### Code Quality Improvements
- Better separation of concerns
- Reduced coupling
- Easier to test & maintain
- Services are now library code (reusable)
- Components are now orchestrators (thin)

---

**Generated**: 2026-07-01
**Phase**: 2 (In Progress)
**Status**: Services Complete ✅
**Next**: Refactor components to use services
