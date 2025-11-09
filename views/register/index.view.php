<?php base_path("views/partials/header.php") ?>


<?php base_path("views/partials/nav.php") ?>




<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white">Register new account</h2>
    </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form action="register-store" method="POST" class="space-y-6">
        <div>
        <label for="name" class="block text-sm/6 font-medium text-gray-100">Name</label>
        <div class="mt-2">
            <input 
                id="name" 
                type="name" 
                name="name" 
                required 
                autocomplete="name" 
                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" 
                value="<?= $params['name'] ?? '' ?>"
                />
            <p class="mt-3 text-sm/6 text-red-400"><?= $errors['name'] ?? '' ?></p>
        </div>
        </div>

        <div>
        <label for="email" class="block text-sm/6 font-medium text-gray-100">Email address</label>
        <div class="mt-2">
            <input 
                id="email" 
                type="email" 
                name="email" 
                required 
                autocomplete="email" 
                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" 
                value="<?= $params['email'] ?? '' ?>"
                />
            <p class="mt-3 text-sm/6 text-red-400"><?= $errors['email'] ?? '' ?></p>
        </div>
        </div>

        <div>
        <label for="password" class="block text-sm/6 font-medium text-gray-100">Password</label>
        <div class="mt-2">
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password" 
                class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" 
                value="<?= $params['password'] ?? '' ?>"
                />
            <p class="mt-3 text-sm/6 text-red-400"><?= $errors['password'] ?? '' ?></p>
        </div>
        </div>

        <p class="mt-3 text-sm/6 text-red-400"><?= $errors['existance'] ?? '' ?></p>

        <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-500 px-3 py-1.5 text-sm/6 font-semibold text-white hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Register</button>
        </div>
    </form>

    
  </div>
</div>
