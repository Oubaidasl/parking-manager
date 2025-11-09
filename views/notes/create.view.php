<?php base_path("views/partials/header.php") ?>


<?php base_path("views/partials/nav.php") ?>

<?php base_path("views/partials/banner.php", ['title' => "Create new Note"]) ?>

    <main>
        <div class="flex justify-center">
        <form class="w-full max-w-6xl" method="POST" action="/note-store">
            <div class="space-y-12">
                

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    

                    <div class="col-span-full">
                    <label for="note" class="block text-sm/6 font-medium text-white">Description</label>
                    <div class="mt-2">
                        <textarea 
                            id="note" 
                            name="note" 
                            rows="3" 
                            class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" 
                            placeholder="Here is an idea of a note ..." 
                            required
                            ><?= $_POST['note'] ?? '' ?></textarea>
                    </div>
                    <p class="mt-3 text-sm/6 text-red-400"><?= $errors['body'] ?? '' ?></p>
                    <p class="mt-3 text-sm/6 text-gray-400">Write a description of the note.</p>
                    </div>

                    
                    
                    
                    
                </div>
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="rounded-md bg-indigo-600 px-6 py-2 text-base font-semibold text-white shadow-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                        Save Note
                    </button>
                </div>
                
        </form>
        </div>


    </main>
    </div>
</body>
</html>


