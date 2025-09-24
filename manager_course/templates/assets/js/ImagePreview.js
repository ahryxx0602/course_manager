    function previewImage(event) {
        const reader = new FileReader();
        const file = event.target.files[0];
        const preview = document.getElementById('preview');

        if (file) {
            reader.onload = function () {
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }
