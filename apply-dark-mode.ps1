$viewsDir = "d:\Kulyeah\Semester 4\RPL\Projek new\jahitly\resources\views"

$files = @(
    "livewire\admin\orders\show.blade.php",
    "livewire\admin\queue\index.blade.php",
    "livewire\admin\appointments\index.blade.php",
    "livewire\admin\fabrics\index.blade.php",
    "livewire\admin\vermaks\index.blade.php",
    "livewire\customer\orders\index.blade.php",
    "livewire\customer\orders\show.blade.php",
    "livewire\customer\orders\create.blade.php",
    "livewire\customer\orders\cancel-order.blade.php",
    "livewire\customer\payments\create.blade.php",
    "livewire\customer\payments\history.blade.php",
    "livewire\customer\payments\rejected-payment-handler.blade.php",
    "livewire\customer\appointments\create.blade.php",
    "profile\edit.blade.php",
    "profile\partials\update-profile-information-form.blade.php",
    "dashboard.blade.php"
)

$rules = @(
    # Backgrounds - white cards/surfaces
    @('border-stone-200 bg-white p-6 shadow-sm"', 'border-stone-200 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800"'),
    @('border-stone-200 bg-white p-5 shadow-sm"', 'border-stone-200 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800"'),
    @('border-stone-200 bg-white p-5"', 'border-stone-200 bg-white p-5 dark:border-stone-700 dark:bg-stone-800"'),
    @('border-stone-200 bg-white p-6"', 'border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800"'),
    @('border-stone-200 bg-white p-4"', 'border-stone-200 bg-white p-4 dark:border-stone-700 dark:bg-stone-800"'),
    @('border-slate-200 bg-white shadow-sm"', 'border-slate-200 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800"'),
    @('border-slate-200 bg-white p-5 shadow-sm"', 'border-slate-200 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800"'),
    @('border-slate-200 bg-white p-6 shadow-sm"', 'border-slate-200 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800"'),
    @('bg-white shadow-sm sm:rounded-lg"', 'bg-white shadow-sm sm:rounded-lg dark:bg-stone-800"'),
    @('bg-white overflow-hidden shadow-sm', 'bg-white dark:bg-stone-800 overflow-hidden shadow-sm'),

    # Table headers
    @('bg-slate-50 text-slate-600"', 'bg-slate-50 text-slate-600 dark:bg-stone-700/50 dark:text-stone-400"'),
    @('bg-stone-50 text-stone-600"', 'bg-stone-50 text-stone-600 dark:bg-stone-700/50 dark:text-stone-400"'),

    # Table body dividers
    @('divide-y divide-slate-200"', 'divide-y divide-slate-200 dark:divide-stone-700"'),
    @('divide-y divide-stone-200"', 'divide-y divide-stone-200 dark:divide-stone-700"'),

    # Table row hover
    @('hover:bg-slate-50"', 'hover:bg-slate-50 dark:hover:bg-stone-700/50"'),
    @('hover:bg-stone-50"', 'hover:bg-stone-50 dark:hover:bg-stone-700/50"'),

    # Borders
    @('border-t border-slate-200 px', 'border-t border-slate-200 dark:border-stone-700 px'),
    @('border-t border-stone-200 px', 'border-t border-stone-200 dark:border-stone-700 px'),
    @('border-t border-stone-200 pt', 'border-t border-stone-200 dark:border-stone-700 pt'),
    @('border-b border-stone-200 p', 'border-b border-stone-200 dark:border-stone-700 p'),
    @('border border-dashed border-stone-300 bg-stone-50', 'border border-dashed border-stone-300 bg-stone-50 dark:border-stone-600 dark:bg-stone-700/30'),
    @('border-stone-100 bg-stone-50 p-', 'border-stone-100 bg-stone-50 dark:border-stone-700 dark:bg-stone-700/30 p-'),

    # Primary text  
    @('text-stone-900"', 'text-stone-900 dark:text-stone-100"'),
    @('text-slate-900"', 'text-slate-900 dark:text-stone-100"'),
    @('text-gray-900"', 'text-gray-900 dark:text-stone-100"'),
    @('text-gray-800"', 'text-gray-800 dark:text-stone-200"'),

    # Secondary text
    @('text-stone-700"', 'text-stone-700 dark:text-stone-300"'),
    @('text-slate-700"', 'text-slate-700 dark:text-stone-300"'),
    @('text-stone-800"', 'text-stone-800 dark:text-stone-200"'),

    # Muted text
    @('text-stone-600"', 'text-stone-600 dark:text-stone-400"'),
    @('text-stone-500"', 'text-stone-500 dark:text-stone-400"'),
    @('text-slate-500"', 'text-slate-500 dark:text-stone-400"'),
    @('text-stone-400"', 'text-stone-400 dark:text-stone-500"'),
    @('text-slate-400"', 'text-slate-400 dark:text-stone-500"'),

    # Surfaces
    @('bg-stone-50 p-3"', 'bg-stone-50 dark:bg-stone-700/30 p-3"'),
    @('bg-stone-50 p-4"', 'bg-stone-50 dark:bg-stone-700/30 p-4"'),
    @('bg-stone-50 p-5"', 'bg-stone-50 dark:bg-stone-700/30 p-5"'),
    @('bg-stone-50 p-6"', 'bg-stone-50 dark:bg-stone-700/30 p-6"'),
    @('bg-stone-100 px-', 'bg-stone-100 dark:bg-stone-700 px-'),

    # Buttons
    @('border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50"', 'border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50 dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700"'),
    @('border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300"', 'border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300 dark:border-stone-600 dark:text-stone-300 dark:hover:border-stone-500"'),
    @('bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800"', 'bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-stone-600 dark:hover:bg-stone-500"'),

    # Labels
    @('text-sm font-semibold text-slate-700"', 'text-sm font-semibold text-slate-700 dark:text-stone-300"'),
    @('text-sm font-semibold text-stone-500"', 'text-sm font-semibold text-stone-500 dark:text-stone-400"'),

    # Amber card sections  
    @('border-amber-200 bg-amber-50', 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20'),
    @('border-amber-100 bg-white/70', 'border-amber-100 bg-white/70 dark:border-amber-800 dark:bg-stone-700/50'),
    @('text-amber-700"', 'text-amber-700 dark:text-amber-400"'),
    @('text-amber-900"', 'text-amber-900 dark:text-amber-300"'),

    # Emerald sections
    @('border-emerald-200 bg-emerald-50', 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20'),

    # Table overflow borders
    @('overflow-hidden rounded-2xl border border-slate-200 bg-white', 'overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-stone-700 dark:bg-stone-800'),
    @('overflow-hidden rounded-2xl border border-stone-200 bg-white', 'overflow-hidden rounded-2xl border border-stone-200 bg-white dark:border-stone-700 dark:bg-stone-800'),
    @('rounded-2xl border border-stone-200 bg-white', 'rounded-2xl border border-stone-200 bg-white dark:border-stone-700 dark:bg-stone-800'),

    # Form surface
    @('border-stone-200 bg-stone-50 p-', 'border-stone-200 bg-stone-50 dark:border-stone-700 dark:bg-stone-700/30 p-')
)

$totalChanges = 0

foreach ($relFile in $files) {
    $filePath = Join-Path $viewsDir $relFile
    if (-not (Test-Path $filePath)) {
        Write-Host "SKIP (not found): $relFile"
        continue
    }

    $content = [System.IO.File]::ReadAllText($filePath)
    $fileChanges = 0

    foreach ($rule in $rules) {
        $search = $rule[0]
        $replace = $rule[1]

        if ($content.Contains($search) -and -not $content.Contains($replace)) {
            $before = $content.Length
            $content = $content.Replace($search, $replace)
            $after = $content.Length
            if ($before -ne $after) {
                $fileChanges++
            }
        }
    }

    if ($fileChanges -gt 0) {
        [System.IO.File]::WriteAllText($filePath, $content)
        Write-Host "OK: $relFile ($fileChanges rules applied)"
        $totalChanges += $fileChanges
    } else {
        Write-Host "SKIP (no changes needed): $relFile"
    }
}

Write-Host "`nDone! Total rules applied: $totalChanges"
