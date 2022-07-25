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

                if (!json)
                {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                if (typeof json.error != 'undefined')
                {
                    reject(json.error);
                    return;
                }

                if (typeof json.location != 'string')
                {
                    reject('Invalid JSON: ' + xhr.responseText);
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
            language:'hu_HU',
            plugins: 
                'table save image advlist autolink link lists charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code insertdatetime media nonbreaking emoticons template directionality fullscreen help',
            toolbar: 'undo redo | styleselect | bold italic underline | fontselect fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | link image',
            relative_urls: false,
            remove_script_host: false,
            document_base_url: 'http://localhost/',
            images_upload_handler: image_upload
        });
    </script>