@extends('partials.layouts.master')

@section('title', 'Créer un Article | SmartHealth')
@section('title-sub', 'Blog')
@section('pagetitle', 'Créer un Article')

@section('css')
    <!-- Editor css -->
    <link rel="stylesheet" href="assets/libs/quill/quill.snow.css">
    <!-- Select css -->
    <link rel="stylesheet" href="assets/libs/choices.js/public/assets/styles/choices.min.css">
    <!-- Uploaded css -->
    <link rel="stylesheet" href="assets/libs/dropzone/dropzone.css">
@endsection

@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Nouvel Article de Blog</h6>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-5">
                                <div class="col-12 col-xl-6">
                                    <label for="blogTitle" class="form-label">Titre de l'Article<span class="text-danger ms-1">*</span></label>
                                    <input type="text" name="title" placeholder="Titre de l'article" class="form-control" id="blogTitle" value="{{ old('title') }}" required>
                                </div>
                                
                                <div class="col-12 col-xl-6">
                                    <label for="blog-category" class="form-label">Catégorie de l'Article<span class="text-danger ms-1">*</span></label>
                                    <select name="category" id="blog-category" class="form-select" required>
                                        @if(old('category'))
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected style="display: none;">Sélectionnez une catégorie</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-lg-12">
                                    <label for="snowEditor" class="form-label">Contenu de l'Article<span class="text-danger ms-1">*</span></label>
                                    <textarea name="content" id="blogContent" rows="10" class="form-control" placeholder="Écrivez le contenu de votre article ici..." required>{{ old('content') }}</textarea>
                                </div>

                                <div class="col-lg-12">
                                    <label for="blogImage" class="form-label">Image de l'Article</label>
                                    <input type="file" name="image" accept="image/*" class="form-control" id="blogImage">
                                    <small class="text-muted">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</small>
                                </div>

                                <div class="col-lg-12 justify-content-end d-flex gap-3 mt-6">
                                    <a href="{{ route('blogs.index') }}" class="btn btn-light-light text-body">Annuler</a>
                                    <button type="submit" class="btn btn-primary">Publier l'Article</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Editor js -->
    <script src="assets/libs/quill/quill.js"></script>
    <!-- Select js -->
    <script src="assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    
    <script>
        // Initialiser Choices.js pour le select de catégorie
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = new Choices('#blog-category', {
                searchEnabled: false,
                itemSelectText: '',
                removeItemButton: false,
                shouldSort: false,
                callbackOnChange: function(value) {
                    // Supprimer l'option placeholder après sélection
                    const placeholderOption = this.passedElement.element.querySelector('option[value=""]');
                    if (placeholderOption && value) {
                        placeholderOption.remove();
                    }
                }
            });
        });

        // Alternative avec JavaScript pur si Choices.js ne fonctionne pas comme attendu
        document.getElementById('blog-category').addEventListener('change', function() {
            if (this.value !== '') {
                const placeholderOption = this.querySelector('option[value=""]');
                if (placeholderOption) {
                    placeholderOption.remove();
                }
            }
        });
    </script>

    <!-- App js -->
    <script type="module" src="assets/js/app.js"></script>
@endsection