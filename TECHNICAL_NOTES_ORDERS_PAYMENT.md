# 🔍 TECHNICAL NOTES - Orders & Payment System

## Architecture Overview

```
Customer Orders & Payments Flow:
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  1. CREATE ORDER                                                │
│     └─ customer/orders/create                                  │
│        ├─ Select Service                                       │
│        ├─ Upload Design File (optional)                        │
│        └─ Confirm Price & Create                               │
│                                                                 │
│  2. VIEW ORDER DETAILS                                          │
│     └─ customer/orders/show                                    │
│        ├─ Status Tracking (6 steps)                            │
│        ├─ Payment Status                                        │
│        ├─ Riwayat Pembayaran Button                            │
│        └─ Cancel Button (conditional)                          │
│                                                                 │
│  3. MAKE PAYMENT                                                │
│     └─ customer/orders/{order}/payments/create                 │
│        ├─ Choose Payment Method (Transfer/QRIS/Cash)           │
│        ├─ Enter Amount                                         │
│        ├─ Upload Proof (if Transfer/QRIS)                      │
│        └─ Submit                                               │
│           │                                                    │
│           ├─ Pending Verification (menunggu_verifikasi)        │
│           │                                                    │
│           ├─ ✅ Approved (terverifikasi)                        │
│           │   └─ Order can proceed                             │
│           │                                                    │
│           └─ ❌ Rejected (ditolak)                              │
│              └─ See rejection reason & retry                   │
│                                                                 │
│  4. VIEW PAYMENT HISTORY                                        │
│     └─ customer/payments/history (all) OR                       │
│        customer/orders/{order}/payments/history (for order)    │
│        ├─ Filter by Status                                     │
│        ├─ View Details                                         │
│        └─ Download Proof (if approved)                         │
│                                                                 │
│  5. HANDLE REJECTED PAYMENT                                     │
│     └─ customer/payments/{payment}/rejected                    │
│        ├─ See Rejection Reason                                 │
│        ├─ Get Retry Steps                                      │
│        └─ Make New Payment                                     │
│                                                                 │
│  6. CANCEL ORDER (optional)                                     │
│     └─ customer/orders/{order}/cancel                          │
│        ├─ Provide Cancellation Reason                          │
│        ├─ Confirm Cancellation                                 │
│        └─ Order Status → 'dibatalkan'                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Component Structure

### Payment History Component
**File**: `app/Livewire/Customer/Payments/History.php`

**Properties**:
- `$order` (Order|null): Filter untuk order tertentu
- `$statusFilter` (string): Filter status pembayaran

**Methods**:
- `getPaymentsProperty()`: Query payments dengan filter
- `getStatusesProperty()`: Status options
- `getStatusColor()`: Color coding untuk status
- `getStatusLabel()`: Label mapping untuk status

**View Events**:
- `updatingStatusFilter()`: Reset pagination saat filter berubah

### Rejected Payment Handler Component
**File**: `app/Livewire/Customer/Payments/RejectedPaymentHandler.php`

**Responsibility**:
- Tampilkan alasan penolakan
- Validate pembayaran adalah milik customer
- Validate pembayaran status = 'ditolak'

### Order Cancellation Component
**File**: `app/Livewire/Customer/Orders/CancelOrder.php`

**Business Rules**:
1. Validasi: Hanya owner yang bisa akses
2. Validasi: Hanya order dengan status tertentu yang bisa dibatalkan
3. Validasi: Tidak ada pembayaran terverifikasi
4. Alasan pembatalan: 10-500 characters

**Data Flow**:
```
Form Submit
    ↓
validate()
    ↓
canCancelOrder()? 
    ├─ NO → abort(403)
    └─ YES → continue
    ↓
Order::update(['status' => 'dibatalkan', 'cancelled_at' => now(), ...])
    ↓
StatusLog::create(['new_status' => 'dibatalkan', 'reason' => ...])
    ↓
Redirect to orders.index with success message
```

## Database Schema

### orders table (Changes)
```sql
-- Existing columns (unchanged):
- id (PK)
- customer_id (FK → users)
- service_id (FK → services)
- order_number (UNIQUE)
- quantity
- notes
- estimated_price
- estimated_finish_date
- queue_position
- created_at
- updated_at

-- New columns:
- cancelled_at (TIMESTAMP, nullable)
  └─ Tracks when order was cancelled
  
- cancellation_reason (TEXT, nullable)
  └─ Reason provided by customer for cancellation

-- Modified column:
- status (ENUM)
  └─ Added: 'dibatalkan'
     Existing: 'menunggu_appointment', 'menunggu_bahan', 'diproses', 
              'dijahit', 'finishing', 'selesai'
```

## Key Validations

### Order Cancellation Validations
```php
// 1. Authorization check
if ($order->customer_id !== auth()->id()) {
    abort(403); // Unauthorized
}

// 2. Status check - can't cancel these statuses
$nonCancellableStatuses = ['dijahit', 'finishing', 'selesai', 'dibatalkan'];
if (in_array($order->status, $nonCancellableStatuses)) {
    abort(403); // Order in non-cancellable state
}

// 3. Payment check - can't cancel if verified payments exist
if ($order->payments->where('status', 'terverifikasi')->isNotEmpty()) {
    abort(403); // Payments already verified
}

// 4. Reason validation
'cancellationReason' => ['required', 'string', 'min:10', 'max:500']
```

### Payment History Validations
```php
// Only show customer's own payments
$query = auth()->user()
    ->payments()
    ->with(['order', 'order.service']);

// If specific order requested
if ($order && $order->customer_id !== auth()->id()) {
    abort(403); // Not your order
}
```

## Relationships

```
Customer (User)
    ↓ hasMany
    Orders
        ├─ belongsTo → Service
        ├─ hasMany → Payments
        │   ├─ belongsTo → Customer (customer_id)
        │   ├─ belongsTo → Verifier (verified_by) [Admin]
        │   └─ attributes: amount, status (belum_bayar/menunggu_verifikasi/ditolak/terverifikasi)
        ├─ hasMany → StatusLogs
        │   └─ belongsTo → User (changed_by)
        └─ hasMany → DesignFiles
```

## Status Flow Diagrams

### Order Status Flow
```
menunggu_appointment
    ↓ (After appointment made)
menunggu_bahan
    ↓ (Bahan received)
diproses
    ↓ (Processing starts)
dijahit
    ↓ (Sewing in progress)
finishing
    ↓ (Final touches)
selesai (FINAL)

At any point before 'dijahit':
    ↓ (if customer cancels)
    dibatalkan (TERMINAL)

At any point if payment verified:
    → Can't cancel
```

### Payment Status Flow
```
belum_bayar (initial) / dibuat belum bayar
    ↓ (Customer submits payment)
menunggu_verifikasi (pending admin review)
    ├─ ✅ Admin approves
    │   ↓
    │   terverifikasi (TERMINAL - success)
    │   
    └─ ❌ Admin rejects
        ↓
        ditolak (TERMINAL - failed)
        └─ Customer can make new payment (back to menunggu_verifikasi)
```

## API Routes Reference

```
GET  /payments                                      (All payment history)
GET  /payments/history                              (Same as above)
GET  /orders/{order}/payments/history              (Order-specific history)

GET  /orders/{order}/payments/create               (Create payment form)
POST /orders/{order}/payments                      (Submit payment)

GET  /payments/{payment}/rejected                  (View rejected reason)

GET  /orders/{order}/cancel                        (Cancel order form)

GET  /orders                                       (List orders)
GET  /orders/create                                (Create order form)
GET  /orders/{order}                               (View order detail)
```

## Storage

### File Uploads
- Payment proof files: `storage/app/private/payments/`
  └─ Accessible via `payments/{payment}/proof` route
  
- Design files: `storage/app/private/designs/`
  └─ Accessible via direct link (restricted by policy)

### Temporary URLs
- `$payment->proof_file_path` stored in DB
- Use `Storage::url($path)` in blade templates
- Or `Storage::temporaryUrl($path, $expiration)` for time-limited access

## Security Considerations

### Authorization
- All routes protected with `middleware(['auth', 'customer'])`
- Manual authorization checks in components:
  - Verify `auth()->id() === $order->customer_id`
  - Verify `auth()->id() === $payment->customer_id`
  - Verify `auth()->user()->isCustomer()`

### Validation
- All form inputs validated via Livewire `rules()`
- File uploads: max 5MB, allowed types (jpg, png, pdf)
- Text inputs: min/max length enforced

### Data Integrity
- Soft-delete not implemented (hard delete stays)
- Cancellation tracked (cancelled_at, cancellation_reason)
- All changes logged (order_status_logs)
- Payment rejections tracked (rejection_note)

## Performance Optimizations

### Query Optimization
```php
// Payment History
$query->with(['order', 'order.service']); // N+1 prevention

// Order Show
$order->load(['service', 'statusLogs.user', 'designFiles', 'payments']);

// Status calculation (computed, not in DB)
// This is by design - recalculated each view
```

### Pagination
- 10 items per page for payment history
- Reduces initial page load

### Caching Opportunities (Future)
- Cache service pricing
- Cache status options
- Cache admin verification queue length

## Testing Checklist

```
POSITIVE TESTS:
✅ Create order → make payment → see in history
✅ Create order → cancel before payment
✅ Make payment → see in history
✅ Filter payment history by status
✅ Download payment proof
✅ View rejected payment → make new payment
✅ Mobile responsive (Card view works)
✅ Desktop view (Table view works)

NEGATIVE TESTS:
✅ Can't cancel order with verified payment
✅ Can't cancel order with status: dijahit+
✅ Can't access other customer's payments
✅ Can't access other customer's orders
✅ Invalid cancellation reason (< 10 chars)
✅ Invalid file upload (> 5MB)

EDGE CASES:
✅ Multiple payments on one order
✅ Rejected payments retry
✅ Order with no payments
✅ Empty payment history page
✅ Pagination navigation
```

## Future Enhancements

1. **Invoice Generation**
   - Auto-generate PDF invoice for each payment
   - Email invoice to customer

2. **Payment Reminders**
   - Scheduled job to remind unpaid orders
   - Send via email/SMS after 3 days

3. **Refund Management**
   - Admin can issue refund for cancelled orders
   - Audit trail for refunds

4. **Payment Analytics**
   - Dashboard showing payment success rate
   - Revenue reports

5. **Multiple Payment Methods Integration**
   - Stripe/PayPal API integration
   - Auto-verify payments

6. **Order Amendments**
   - Allow partial cancellation
   - Change order after creation
   - Adjust pricing

---

**Created**: 2026-05-26
**Last Updated**: 2026-05-26
**Version**: 1.0
