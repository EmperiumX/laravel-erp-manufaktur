<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'New Citra ERP') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Responsive Tables Script & Styles -->
        <style>
            @media (max-width: 767px) {
                main table { display: block !important; width: 100% !important; border: none !important; }
                main table thead { display: none !important; }
                main table tbody { display: block !important; width: 100% !important; }
                main table tr {
                    display: flex !important; flex-direction: column !important; width: 100% !important;
                    margin-bottom: 1rem !important; background-color: #ffffff !important;
                    border: 1px solid #e5e7eb !important; border-radius: 0.5rem !important;
                    padding: 0.5rem 0 !important; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
                }
                main table td {
                    display: flex !important; justify-content: space-between !important; align-items: flex-start !important;
                    padding: 0.75rem 1rem !important; border-bottom: 1px solid #f3f4f6 !important;
                    text-align: right !important; border-left: none !important; border-right: none !important;
                    border-top: none !important; font-size: 0.875rem !important; word-break: break-word !important; min-width: 0 !important;
                }
                main table td:last-child { border-bottom: none !important; }
                main table td::before {
                    content: attr(data-label) !important; font-weight: 600 !important; color: #4b5563 !important;
                    text-align: left !important; margin-right: 1rem !important; flex-shrink: 0 !important; max-width: 40% !important;
                }
                main .overflow-x-auto { overflow-x: visible !important; }
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const applyDataLabels = () => {
                    document.querySelectorAll("main table").forEach(table => {
                        const headers = Array.from(table.querySelectorAll("thead th")).map(th => th.innerText.trim());
                        if(headers.length === 0) return;
                        table.querySelectorAll("tbody tr").forEach(tr => {
                            const tds = Array.from(tr.querySelectorAll("td"));
                            if (tds.length === 1 && tds[0].hasAttribute("colspan")) return;
                            tds.forEach((td, index) => {
                                if (headers[index] && !td.hasAttribute("data-label")) {
                                    td.setAttribute("data-label", headers[index]);
                                }
                            });
                        });
                    });
                };
                applyDataLabels();
                
                const observer = new MutationObserver((mutations) => {
                    let shouldApply = false;
                    for (let m of mutations) {
                        if (m.addedNodes.length) {
                            shouldApply = true;
                            break;
                        }
                    }
                    if (shouldApply) applyDataLabels();
                });
                observer.observe(document.body, { childList: true, subtree: true });

                const initTomSelect = (el) => {
                    if (el.tagName === 'SELECT') {
                        // Skip if already tomselected, marked to skip, or is a DataTables system select
                        if (
                            el.classList.contains('tomselected') || 
                            el.closest('.no-select2') || 
                            el.closest('.no-tomselect') ||
                            el.closest('.dataTables_length') ||
                            el.closest('.dataTables_wrapper') ||
                            (el.name && el.name.endsWith('_length')) ||
                            el.id === 'fontSelector'
                        ) {
                            return;
                        }
                        
                        new TomSelect(el, {
                            create: false
                        });
                    }
                };

                // Reposition DataTable Length Select to the bottom (above info)
                const repositionDataTableLength = (wrapper) => {
                    if (wrapper.classList.contains('dt-repositioned')) return;
                    const lengthSel = wrapper.querySelector('.dataTables_length');
                    const infoSel = wrapper.querySelector('.dataTables_info');
                    if (lengthSel && infoSel) {
                        wrapper.classList.add('dt-repositioned');
                        infoSel.parentNode.insertBefore(lengthSel, infoSel);
                        
                        // Custom inline styles for clean spacing and layout
                        lengthSel.style.display = 'block';
                        lengthSel.style.marginBottom = '0.5rem';
                        lengthSel.style.float = 'left';
                        lengthSel.style.clear = 'both';
                        infoSel.style.clear = 'both';
                    }
                };

                // Align DataTable filter with the create button or header actions on the same line
                const alignDataTableFilter = (wrapper) => {
                    if (wrapper.classList.contains('dt-filter-aligned')) return;
                    
                    const filterSel = wrapper.querySelector('.dataTables_filter');
                    if (!filterSel) return;
                    
                    // Find the outermost container (handles overflow-x-auto wrappers)
                    let outerContainer = wrapper;
                    if (wrapper.parentElement && (wrapper.parentElement.classList.contains('overflow-x-auto') || wrapper.parentElement.classList.contains('overflow-auto') || (wrapper.parentElement.tagName === 'DIV' && wrapper.parentElement.className.includes('overflow-')))) {
                        outerContainer = wrapper.parentElement;
                    }
                    
                    // Traverse backwards to find target button/actions sibling
                    let targetSibling = null;
                    let curr = outerContainer.previousElementSibling;
                    for (let i = 0; i < 4 && curr; i++) {
                        if (
                            (curr.tagName === 'A' || curr.tagName === 'BUTTON') ||
                            (curr.tagName === 'DIV' && (curr.querySelector('a, button') || curr.classList.contains('flex') || curr.className.includes('gap-')))
                        ) {
                            if (!curr.className.includes('bg-green-') && !curr.className.includes('bg-red-')) {
                                targetSibling = curr;
                                break;
                            }
                        }
                        curr = curr.previousElementSibling;
                    }
                    
                    if (targetSibling) {
                        wrapper.classList.add('dt-filter-aligned');
                        
                        // Case A: Sibling is already a flex wrapper div
                        if (targetSibling.tagName === 'DIV' && (targetSibling.querySelector('a, button') || targetSibling.classList.contains('flex'))) {
                            targetSibling.classList.add('flex', 'justify-between', 'items-center', 'flex-wrap', 'gap-4');
                            targetSibling.appendChild(filterSel);
                            filterSel.style.margin = '0';
                            filterSel.style.float = 'none';
                        }
                        // Case B: Sibling is a single button element
                        else if (targetSibling.tagName === 'A' || targetSibling.tagName === 'BUTTON') {
                            const flexRow = document.createElement('div');
                            flexRow.className = 'flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 w-full';
                            targetSibling.parentNode.insertBefore(flexRow, targetSibling);
                            
                            targetSibling.classList.remove('mb-4');
                            targetSibling.style.marginBottom = '0';
                            
                            flexRow.appendChild(targetSibling);
                            flexRow.appendChild(filterSel);
                            filterSel.style.margin = '0';
                            filterSel.style.float = 'none';
                        }
                    }
                };

                // Run on initial load
                document.querySelectorAll('main select').forEach(initTomSelect);
                document.querySelectorAll('.dataTables_wrapper').forEach(repositionDataTableLength);
                document.querySelectorAll('.dataTables_wrapper').forEach(alignDataTableFilter);

                // Observe DOM changes to auto-initialize selects and reposition DataTables
                const selectObserver = new MutationObserver((mutations) => {
                    document.querySelectorAll('.dataTables_wrapper').forEach(repositionDataTableLength);
                    document.querySelectorAll('.dataTables_wrapper').forEach(alignDataTableFilter);
                    
                    mutations.forEach((mutation) => {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                if (node.tagName === 'SELECT') {
                                    initTomSelect(node);
                                } else {
                                    node.querySelectorAll('select').forEach(initTomSelect);
                                }
                            }
                        });
                    });
                });
                const mainContent = document.querySelector('main');
                if (mainContent) {
                    selectObserver.observe(mainContent, { childList: true, subtree: true });
                }
            });
        </script>

        <!-- Tom Select Searchable Dropdowns -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <style>
            /* Custom styles to match Tom Select with our Tailwind theme */
            .ts-wrapper .ts-control {
                border-radius: 0.75rem !important; /* rounded-xl */
                border-color: #d1d5db !important; /* border-gray-300 */
                padding: 0.625rem 0.875rem !important;
                font-size: 0.875rem !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            }
            .ts-wrapper.focus .ts-control {
                border-color: #ef4444 !important; /* focus:border-red-500 */
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important; /* focus:ring-red-500 */
            }
            .ts-dropdown {
                border-radius: 0.75rem !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                border-color: #f3f4f6 !important;
                z-index: 9999 !important;
                padding: 4px !important;
            }
            .ts-dropdown .option {
                padding: 8px 12px !important;
                border-radius: 0.5rem !important;
                cursor: pointer;
            }
            .ts-dropdown .active {
                background-color: #fee2e2 !important; /* red-100 */
                color: #b91c1c !important; /* red-700 */
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-800 bg-gray-50">
        <div class="flex h-screen overflow-hidden bg-gray-50" x-data="{ sidebarOpen: false }">
            
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
                
                <!-- Top Header -->
                @include('layouts.topbar')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8 w-full max-w-9xl mx-auto">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <strong class="font-bold">Validasi Gagal!</strong>
                            <ul class="list-disc pl-5 mt-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
            
        </div>
    </body>
</html>
