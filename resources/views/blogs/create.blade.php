<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ajouter un article</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h1>Ajouter un article</h1>

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

    <div class="mb-3">
      <label class="form-label">Titre</label>
      <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Catégorie</label>
      <select name="category" class="form-select" required>
        <option value="">-- Sélectionnez une catégorie --</option>
        @foreach($categories as $category)
          <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
            {{ $category }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Contenu</label>
      <textarea name="content" rows="6" class="form-control" required>{{ old('content') }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Image (optionnelle)</label>
      <input type="file" name="image" accept="image/*" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Ajouter</button>
    <a href="{{ route('blogs.index') }}" class="btn btn-secondary">Retour à la liste</a>
  </form>
</div>
</body>
</html>