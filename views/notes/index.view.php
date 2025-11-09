<?php base_path("views/partials/header.php") ?>


<?php base_path("views/partials/nav.php") ?>

<?php base_path("views/partials/banner.php", ['title' => 'My Notes']) ?>

    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto p-4 bg-blue-100 rounded-lg shadow-lg">
            <ul class="divide-y divide-blue-300">
                

                <?php foreach ($posts as $post) : ?>
                    
                    <a href="/note?id=<?= $post['id'] ?>">
                        <?= '<li class="py-2 px-4 hover:bg-gray-700 rounded">' . htmlspecialchars($post['note']) . '</li>'; ?>
                    </a> 
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Button to create a new note -->
        <div class="max-w-2xl mx-auto mt-4 text-center">
            <a href="/note-create" class="rounded-md bg-indigo-600 px-6 py-2 text-base font-semibold text-white shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                Create a Note
            </a>
        </div>

    </main>
    </div>
</body>
</html>

