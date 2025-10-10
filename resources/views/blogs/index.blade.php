<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Liste des articles</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Articles</h1>
    <a href="{{ route('blogs.create') }}" class="btn btn-success">Ajouter un article</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @forelse($blogs as $blog)
    <div class="card mb-3">
      <div class="row g-0">
        @if($blog->image_url)
          <div class="col-md-3">
            <img src="{{ asset('storage/' . $blog->image_url) }}" class="img-fluid rounded-start" alt="image" style="height: 200px; object-fit: cover;">
          </div>
          <div class="col-md-9">
        @else
          <div class="col-12">
        @endif

          <div class="card-body">
            <h5 class="card-title">{{ $blog->title }}</h5>
            <p class="card-text"><small class="text-muted">Catégorie: {{ $blog->category }} — Auteur: {{ $blog->user->name }}</small></p>
            <p class="card-text">{{ \Illuminate\Support\Str::limit($blog->content, 200) }}</p>
            <p class="card-text"><small class="text-muted">Publié le {{ $blog->created_at->format('d/m/Y H:i') }}</small></p>
          </div>
        </div>
      </div>
    </div>
  @empty
    <p>Aucun article pour le moment.</p>
  @endforelse
</div>
</body>
</html>