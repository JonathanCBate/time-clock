<nav class="bg-gray-800 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('stopwatch.index') }}" class="text-xl font-bold">MyApp</a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-4 items-center">
                <a href="{{ route('stopwatch.index') }}" class="hover:text-gray-400">stopwatch</a>
                <a href="{{ route('stopwatch.generatePdf') }}" class="hover:text-gray-400">pdf</a>
                <a href="{{ route('email_form') }}" class="hover:text-gray-400">email</a>
                <a href="{{ route('saved-times') }}" class="hover:text-gray-400">saved times</a>
            </div>

            <!-- Authentication Links -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-400">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-gray-400">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gray-400">Login</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-400">Register</a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="focus:outline-none">
                    ☰
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="hidden md:hidden" id="mobile-menu">
        <a href="{{ route('stopwatch.index') }}"  class="block px-4 py-2 text-white">stopwatch</a>
        <a href="{{ route('stopwatch.generatePdf') }}" class="block px-4 py-2 text-white">Make pdf</a>
        <a href="{{ route('email_form') }}" class="block px-4 py-2 text-white">email pdf</a>
        <a href="{{ route('saved-times') }}" class="hover:text-gray-400">saved times</a>

        @auth
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-white">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" class="px-4 py-2">
                @csrf
                <button type="submit" class="text-white">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block px-4 py-2 text-white">Login</a>
            <a href="{{ route('register') }}" class="block px-4 py-2 text-white">Register</a>
        @endauth
    </div>

    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</nav>
