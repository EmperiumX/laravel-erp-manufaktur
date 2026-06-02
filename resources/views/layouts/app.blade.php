<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
            });
        </script>
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
                    {{ $slot }}
                </main>
            </div>
            
        </div>
    </body>
</html>
