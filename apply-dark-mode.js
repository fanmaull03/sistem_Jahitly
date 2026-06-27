/**
 * This script adds dark mode classes to all remaining blade templates.
 * It applies consistent search-and-replace patterns across files.
 */

const fs = require('fs');
const path = require('path');

const viewsDir = path.join(__dirname, 'resources', 'views');

// Files to process (remaining admin + customer + profile views)
const files = [
    'livewire/admin/orders/show.blade.php',
    'livewire/admin/payments/index.blade.php',
    'livewire/admin/queue/index.blade.php',
    'livewire/admin/appointments/index.blade.php',
    'livewire/admin/fabrics/index.blade.php',
    'livewire/admin/vermaks/index.blade.php',
    'livewire/customer/orders/index.blade.php',
    'livewire/customer/orders/show.blade.php',
    'livewire/customer/orders/create.blade.php',
    'livewire/customer/orders/cancel-order.blade.php',
    'livewire/customer/payments/create.blade.php',
    'livewire/customer/payments/history.blade.php',
    'livewire/customer/payments/rejected-payment-handler.blade.php',
    'livewire/customer/appointments/create.blade.php',
    'profile/edit.blade.php',
    'profile/partials/update-profile-information-form.blade.php',
    'dashboard.blade.php',
];

// Replacement rules: [pattern, replacement]
// Order matters — more specific patterns first
const rules = [
    // Backgrounds
    ['bg-white shadow-sm"', 'bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800"'],
    ['bg-white p-6 shadow-sm"', 'bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800"'],
    ['border-stone-200 bg-white p-6"', 'border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800"'],
    ['border-stone-200 bg-white p-5"', 'border-stone-200 bg-white p-5 dark:border-stone-700 dark:bg-stone-800"'],
    ['border-stone-200 bg-white p-4"', 'border-stone-200 bg-white p-4 dark:border-stone-700 dark:bg-stone-800"'],
    ['border-stone-200 bg-white"', 'border-stone-200 bg-white dark:border-stone-700 dark:bg-stone-800"'],
    ['border-slate-200 bg-white"', 'border-slate-200 bg-white dark:border-stone-700 dark:bg-stone-800"'],

    // Table headers
    ['bg-slate-50 text-slate-600"', 'bg-slate-50 text-slate-600 dark:bg-stone-700/50 dark:text-stone-400"'],
    ['bg-stone-50 text-stone-600"', 'bg-stone-50 text-stone-600 dark:bg-stone-700/50 dark:text-stone-400"'],
    ['bg-stone-50 text-xs', 'bg-stone-50 dark:bg-stone-700/50 text-xs'],

    // Table body dividers
    ['divide-y divide-slate-200"', 'divide-y divide-slate-200 dark:divide-stone-700"'],
    ['divide-y divide-stone-200"', 'divide-y divide-stone-200 dark:divide-stone-700"'],

    // Table row hover
    ['hover:bg-slate-50"', 'hover:bg-slate-50 dark:hover:bg-stone-700/50"'],
    ['hover:bg-stone-50"', 'hover:bg-stone-50 dark:hover:bg-stone-700/50"'],
    ['hover:bg-stone-100"', 'hover:bg-stone-100 dark:hover:bg-stone-700"'],

    // Borders
    ['border-t border-slate-200 px', 'border-t border-slate-200 dark:border-stone-700 px'],
    ['border-t border-stone-200 px', 'border-t border-stone-200 dark:border-stone-700 px'],
    ['border-b border-stone-200', 'border-b border-stone-200 dark:border-stone-700'],
    ['border border-dashed border-stone-300 bg-stone-50', 'border border-dashed border-stone-300 bg-stone-50 dark:border-stone-600 dark:bg-stone-700/30'],
    ['border-stone-100 bg-stone-50 p-', 'border-stone-100 bg-stone-50 dark:border-stone-700 dark:bg-stone-700/30 p-'],

    // Primary text
    ['text-stone-900"', 'text-stone-900 dark:text-stone-100"'],
    ['text-slate-900"', 'text-slate-900 dark:text-stone-100"'],
    ['text-gray-900"', 'text-gray-900 dark:text-stone-100"'],
    ['text-gray-800"', 'text-gray-800 dark:text-stone-200"'],

    // Secondary text
    ['text-stone-700"', 'text-stone-700 dark:text-stone-300"'],
    ['text-slate-700"', 'text-slate-700 dark:text-stone-300"'],
    ['text-stone-800"', 'text-stone-800 dark:text-stone-200"'],
    ['text-slate-800"', 'text-slate-800 dark:text-stone-200"'],

    // Muted text
    ['text-stone-600"', 'text-stone-600 dark:text-stone-400"'],
    ['text-slate-600"', 'text-slate-600 dark:text-stone-400"'],
    ['text-stone-500"', 'text-stone-500 dark:text-stone-400"'],
    ['text-slate-500"', 'text-slate-500 dark:text-stone-400"'],
    ['text-stone-400"', 'text-stone-400 dark:text-stone-500"'],
    ['text-slate-400"', 'text-slate-400 dark:text-stone-500"'],

    // Background surfaces
    ['bg-stone-50 p-', 'bg-stone-50 dark:bg-stone-700/30 p-'],
    ['bg-stone-100"', 'bg-stone-100 dark:bg-stone-700/30"'],
    ['bg-slate-100"', 'bg-slate-100 dark:bg-stone-700/30"'],

    // Stone 100 badges/tags
    ['bg-stone-100 px-', 'bg-stone-100 dark:bg-stone-700 px-'],
    ['bg-stone-200 text-stone-', 'bg-stone-200 dark:bg-stone-700 text-stone-'],

    // Buttons/links with stone borders
    ['border-stone-200 px-3 py-2 text-sm font-semibold text-stone-700"', 'border-stone-200 px-3 py-2 text-sm font-semibold text-stone-700 dark:border-stone-600 dark:text-stone-300"'],
    ['border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700"', 'border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 dark:border-stone-600 dark:text-stone-300"'],
    ['border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50"', 'border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50 dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700"'],
    ['border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700', 'border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-stone-600 dark:text-stone-300'],

    // Form/card label text  
    ['text-sm font-semibold text-stone-500"', 'text-sm font-semibold text-stone-500 dark:text-stone-400"'],
    ['text-sm font-semibold text-slate-500"', 'text-sm font-semibold text-slate-500 dark:text-stone-400"'],

    // Blue accent
    ['bg-blue-600/10 text-blue-600"', 'bg-blue-600/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400"'],

    // Amber card sections
    ['border-amber-200 bg-amber-50', 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20'],
    ['border-amber-100 bg-white/70', 'border-amber-100 bg-white/70 dark:border-amber-800 dark:bg-stone-700/50'],
    ['text-amber-700"', 'text-amber-700 dark:text-amber-400"'],
    ['text-amber-900"', 'text-amber-900 dark:text-amber-300"'],

    // Emerald alerts/sections
    ['border-emerald-200 bg-emerald-50', 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20'],
    ['text-emerald-900"', 'text-emerald-900 dark:text-emerald-300"'],

    // Rose/red alerts
    ['border-rose-200 bg-rose-50', 'border-rose-200 bg-rose-50 dark:border-rose-800 dark:bg-rose-900/20'],
    ['text-rose-900"', 'text-rose-900 dark:text-rose-300"'],
];

let totalChanges = 0;

for (const relFile of files) {
    const filePath = path.join(viewsDir, relFile);

    if (!fs.existsSync(filePath)) {
        console.log(`SKIP (not found): ${relFile}`);
        continue;
    }

    let content = fs.readFileSync(filePath, 'utf8');
    let fileChanges = 0;

    for (const [search, replace] of rules) {
        // Only apply if the replacement string's dark: classes are not already present
        if (content.includes(search) && !content.includes(replace)) {
            const count = content.split(search).length - 1;
            content = content.replaceAll(search, replace);
            fileChanges += count;
        }
    }

    if (fileChanges > 0) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`OK: ${relFile} (${fileChanges} changes)`);
        totalChanges += fileChanges;
    } else {
        console.log(`SKIP (no changes): ${relFile}`);
    }
}

console.log(`\nDone! Total changes: ${totalChanges}`);
