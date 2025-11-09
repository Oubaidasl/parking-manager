<?php base_path("views/partials/header.php") ?>


<?php base_path("views/partials/nav.php") ?>

<?php base_path("views/partials/banner.php", ['title' => $title]) ?>

    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto p-4 bg-blue-100 rounded-lg shadow-lg">
            <ul class="divide-y divide-blue-300">
                <?= '<li class="py-2 px-4 hover:bg-gray-700 rounded">' . htmlspecialchars($note['note']) . '</li>'; ?> 
            </ul>
        </div>
        <div class="mt-8 flex justify-center space-x-4">
            <form action="/note-edit" method="GET" class="flex items-center">
                <input type="hidden" name="id" value="<?= $note['id'] ?>">
                <button type="submit" class="rounded-md bg-indigo-600 px-6 py-2 text-base font-semibold text-white shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                    Edit Note
                </button>
            </form>

            <form action="/note-delete" method="POST" class="flex items-center">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="id" value="<?= $note['id'] ?>">
                <button type="submit" class="rounded-md bg-red-600 px-6 py-2 text-base font-semibold text-white shadow-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                    Delete Note
                </button>
            </form>
            <a href="/notes" class="rounded-md bg-gray-600 px-6 py-2 text-base font-semibold text-white shadow-lg hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 mr-4 focus:ring-offset-2">
                Back
            </a>
        </div>

        

    </main>
    </div>
</body>
</html>

