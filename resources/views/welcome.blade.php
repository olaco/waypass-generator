<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Evans Baroque Ltd | Waypass Generator</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Corporate Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Evans Baroque Brand Colors */
        :root {
            --eb-magenta: #E91E63;
            --eb-dark: #1A1A2E;
            --eb-bg: #F8F9FA;
        }

        .font-display { font-family: 'Playfair Display', serif; }

        .bg-eb-magenta { background-color: var(--eb-magenta); }
        .bg-eb-dark { background-color: var(--eb-dark); }
        .text-eb-magenta { color: var(--eb-magenta); }
        .border-eb-magenta { border-color: var(--eb-magenta); }

        .hover-eb-magenta:hover { background-color: #D81B60; }

        .bg-corporate {
            background-color: var(--eb-bg);
            background-image:
                radial-gradient(at 0% 0%, rgba(233, 30, 99, 0.05) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(26, 26, 46, 0.05) 0, transparent 50%);
        }
    </style>
</head>
<body class="h-full bg-corporate text-gray-800 antialiased">

    <div class="relative flex min-h-screen flex-col">

        <!-- Header -->
        <header class="relative z-10 w-full bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

                <!-- Official Evans Baroque Logo -->
                <a href="{{ route('home') }}" class="block">
                    <img src="{{ asset('images/evans-baroque-logo.png') }}"
                         alt="Evans Baroque Ltd"
                         class="h-14 w-auto object-contain">
                </a>

                <!-- Login Button -->
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                           class="rounded-md bg-eb-dark px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800">
                            Staff Log In
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex flex-1 flex-col items-center justify-center px-6 py-20 text-center">

            <!-- Corporate Badge -->
            <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-eb-magenta/20 bg-eb-magenta/10 px-4 py-1.5 text-xs font-medium text-eb-magenta">
                <span class="h-2 w-2 rounded-full bg-eb-magenta animate-pulse"></span>
                Secure Internal Logistics Portal
            </div>

            <h1 class="font-display max-w-4xl text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                Building a Healthier Africa,<br>
                <span class="text-eb-magenta">One Waybill at a Time.</span>
            </h1>

            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-600">
                Welcome to the internal system for Evans Baroque Ltd. Manage your
                distribution, waybill approvals, and quality assurance processes
                seamlessly. <br>
                <span class="font-semibold text-gray-900">Access is restricted to authorized personnel only.</span>
            </p>

            <div class="mt-10 flex items-center justify-center gap-x-6">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="rounded-md bg-eb-magenta px-8 py-4 text-base font-semibold text-white shadow-lg shadow-eb-magenta/30 transition hover-eb-magenta">
                        Access Waypass Portal
                        <span aria-hidden="true" class="ml-2">→</span>
                    </a>
                @endif
            </div>

            <!-- Corporate Stats -->
            <div class="mt-20 grid w-full max-w-5xl grid-cols-1 gap-6 sm:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-3xl font-extrabold text-eb-dark">3-Step</div>
                    <div class="mt-1 text-sm text-gray-500">Strict Approval Chain</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-3xl font-extrabold text-eb-dark">100%</div>
                    <div class="mt-1 text-sm text-gray-500">Paperless Process</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-3xl font-extrabold text-eb-dark">Real-time</div>
                    <div class="mt-1 text-sm text-gray-500">Email Notifications</div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="text-3xl font-extrabold text-eb-dark">0</div>
                    <div class="mt-1 text-sm text-gray-500">Compromised Deliveries</div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full bg-eb-dark text-gray-300">
            <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-white font-bold mb-4">CONTACT US</h4>
                    <p class="text-sm">If you have any question, please contact us at any of our support lines:</p>
                    <p class="text-eb-magenta font-bold mt-2">+234-901-020-0000</p>
                    <p class="text-sm mt-1">info@evansbaroque.com</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">QUICK LINKS</h4>
                    <ul class="space-y-2 text-sm">
                        <li>Quality Assurance</li>
                        <li>Manufacturing</li>
                        <li>Distribution</li>
                        <li>Registrations</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">CERTIFIED</h4>
                    <p class="text-sm">NAFDAC: 04-3872-0240</p>
                    <p class="text-sm mt-1">Pharmacy Council of Nigeria (PCN)</p>
                </div>
            </div>
            <div class="border-t border-white/10 py-4 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Evans Baroque Ltd. All Rights Reserved. | Internal Portal
            </div>
        </footer>
    </div>

</body>
</html>
