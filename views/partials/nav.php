<nav class="bg-gray-800/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
        <div class="shrink-0">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="size-8" />
        </div>
        <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
            <!-- Current: "bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->
            <a href="/" <?= uriIs('/') ? 'aria-current="page" class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white"' : 'class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white"' ?>  >Home</a>
            <a href="/dashboard" <?= uriIs('/dashboard') ? 'aria-current="page" class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white"' : 'class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white"' ?>>Dashboard</a>
            <a href="/notes" <?= uriIs('/notes') ? 'aria-current="page" class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white"' : 'class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white"' ?>>Notes</a>
            <a href="/projects" <?= uriIs('/projects') ? 'aria-current="page" class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white"' : 'class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white"' ?>>Projects</a>
            <a href="/calendar" <?= uriIs('/calendar') ? 'aria-current="page" class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white"' : 'class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white"' ?>>Calendar</a>
            </div>
        </div>
        </div>
            <div class="ml-4 flex items-center md:ml-6">
                <?php if (isset($_SESSION['user'])): ?>
                    <form action="/sign-out" method="POST">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="rounded-md bg-indigo-600 px-6 py-2 text-base font-semibold text-white shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                            Sign Out
                        </button>
                    </form>
                <?php else: ?>
                    <form action="/sign-in" method="GET">
                        <button type="submit" class="rounded-md bg-indigo-600 px-6 py-2 text-base font-semibold text-white shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                            Sign In
                        </button>
                    </form>
                <?php endif; ?>
            </div>

</nav>