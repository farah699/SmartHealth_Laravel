@extends('partials.layouts.master')

@section('title', 'Éditer l\'Article | SmartHealth')
@section('title-sub', 'Blog')
@section('pagetitle', 'Éditer l\'Article')

@section('css')
    <!-- Editor css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/quill/quill.snow.css') }}">
    <!-- Select css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <!-- Uploaded css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
@endsection

@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Éditer l'Article de Blog</h6>
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

                        <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-5">
                                <div class="col-12 col-xl-6">
                                    <label for="blogTitle" class="form-label">Titre de l'Article<span class="text-danger ms-1">*</span></label>
                                    <input type="text" name="title" placeholder="Titre de l'article" class="form-control" id="blogTitle" value="{{ old('title', $blog->title) }}" required>
                                </div>
                                
                                <div class="col-12 col-xl-6">
                                    <label for="blog-category" class="form-label">Catégorie de l'Article<span class="text-danger ms-1">*</span></label>
                                    <select name="category" id="blog-category" class="form-select" required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ (old('category', $blog->category) == $category) ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-12">
                                    <label for="snowEditor" class="form-label">Contenu de l'Article<span class="text-danger ms-1">*</span></label>
                                    <textarea name="content" id="blogContent" rows="10" class="form-control" placeholder="Écrivez le contenu de votre article ici..." required>{{ old('content', $blog->content) }}</textarea>
                                </div>

                                <div class="col-lg-12">
                                    <label for="blogImage" class="form-label">Image de l'Article</label>
                                    
                                    @if($blog->image_url)
                                        <div class="mb-3">
                                            <p class="text-muted mb-2">Image actuelle :</p>
                                            <img src="{{ asset('storage/' . $blog->image_url) }}" alt="Image actuelle" 
                                                 class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                        </div>
                                    @endif
                                    
                                    <input type="file" name="image" accept="image/*" class="form-control" id="blogImage">
                                    <small class="text-muted">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB. Laissez vide pour conserver l'image actuelle.</small>
                                </div>

                                <div class="col-lg-12 justify-content-end d-flex gap-3 mt-6">
                                    <a href="{{ route('blogs.show', $blog->id) }}" class="btn btn-light-light text-body">Annuler</a>
                                    <button type="submit" class="btn btn-success">Mettre à jour l'Article</button>
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
    <script src="{{ asset('assets/libs/quill/quill.js') }}"></script>
    <!-- Select js -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    
    <script>
        // Initialiser Choices.js pour le select de catégorie
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = new Choices('#blog-category', {
                searchEnabled: false,
                itemSelectText: '',
                removeItemButton: false,
                shouldSort: false
            });
        });
    </script>

    <!-- App js -->
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection