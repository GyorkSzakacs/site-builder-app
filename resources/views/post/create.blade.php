<x-manager-layout>
    <x-auth-card>

        <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ __('Új bejegyzés hozzáadása') }}</h1>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="/post" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div>
                <x-label for="title" :value="__('Cím')" />

                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
            </div>

            <!-- Title visibility-->
            <div class="mt-4">
                <x-label for="title_visibility" :value="__('Cím láthatósága')" />

                <div class="flex ml-2">
                    <x-input id="visibility_true" class="block mt-1" type="radio" name="title_visibility" :value=1 checked/>
                    <x-label for="visibility_true" class="block p-1 text-xs" :value="__('Igen')" />
                </div>

                <div class="flex ml-2">
                    <x-input id="visibility_false" class="block mt-1" type="radio" name="title_visibility" :value=0 />
                    <x-label for="visibility_false" class="block p-1 text-xs" :value="__('Nem')" />
                </div>
            </div>

            <!-- Description -->
            <div class="mt-4">
                <x-label for="description" :value="__('Rövid leírás')" />

                <textarea name="description" rows="2" cols="40" maxlength="250" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    {{ old('description') }}
                </textarea>
            </div>

            <!-- Post image -->
            <div class="mt-4">
                <x-label for="post_image" :value="__('Bejegyzés kép (Megosztáshoz)')" />

                <input id="post_image" class="block mt-1" type="file" name="post_image" />
            </div>

            <!-- Position -->
            <div class="mt-4">
                <x-label for="position" :value="__('Pozíció')" />

                <x-input id="position" class="block mt-1" type="number" name="position" :value="old('position', $next)" min="1" max="{{ $next }}" required />
            </div>

            <!-- Content -->
            <div class="mt-4">
                <x-label for="content" :value="__('Bejegyzés tartalma')" />

                <textarea id="content" name="content" rows="4" cols="40" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    {{ old('content')}}
                </textarea>
            </div>

            <input type="hidden" name="section_id" value="{{ $sectionId }}"/>

            <div class="flex items-center justify-end mt-4">

                <x-cancel :link="url()->previous()">
                    {{ __('Mégse') }}
                </x-cancel>
                
                <x-button class="ml-4">
                    {{ __('Hozzáadás') }}
                </x-button>

            </div>
        </form>
    </x-auth-card>

    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        const image_upload = (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '{{ route("upload-image") }}');

            var token = '{{ csrf_token() }}';
            xhr.setRequestHeader('X-CSRF-Token', token);

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = () => {
                if (xhr.status === 403)
                {
                    reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                    return;
                }

                if (xhr.status < 200 || xhr.status >= 300)
                {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                const json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string')
                {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                if (typeof json.error != 'undefined')
                {
                    reject(json.error);
                    return;
                }

                resolve(json.location);
            };

            xhr.onerror = () => {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };

            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        });

        tinymce.init({
            selector: 'textarea#content',
            plugins: 
                'table save image advlist autolink link lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code insertdatetime media nonbreaking emoticons template directionality fullscreen help',
            toolbar: 'undo redo | styleselect | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | link image',
            images_upload_handler: image_upload
        });
    </script>

</x-guest-layout>
