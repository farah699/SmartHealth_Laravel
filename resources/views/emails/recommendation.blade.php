<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommandation SmartHealth</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        .recommendation-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin: 20px 0; }
        .badge { background: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .btn { background: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 SmartHealth</h1>
            <p>Recommandation personnalisée pour vous</p>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $authorName }} ! 👋</h2>
            
            <p>Félicitations pour la publication de votre article <strong>"{{ $blogTitle }}"</strong> !</p>
            
            <p>Notre système d'IA a analysé votre contenu et vous propose une ressource qui pourrait enrichir vos connaissances :</p>
            
            <div class="recommendation-card">
                <h3>📖 {{ $recommendation['title'] }}</h3>
                
                <div style="margin: 15px 0;">
                    <span class="badge">{{ $recommendation['category'] }}</span>
                    @if($recommendation['content_type'])
                        <span class="badge" style="background: #17a2b8;">{{ $recommendation['content_type'] }}</span>
                    @endif
                </div>
                
                @if($recommendation['description'])
                    <p><strong>Description :</strong><br>{{ $recommendation['description'] }}</p>
                @endif
                
                @if($recommendation['target_audience'] && $recommendation['target_audience'] !== 'Tous')
                    <p><strong>Public cible :</strong> {{ $recommendation['target_audience'] }}</p>
                @endif
                
                @if($recommendation['difficulty_level'] && $recommendation['difficulty_level'] !== 'N/A')
                    <p><strong>Niveau :</strong> {{ $recommendation['difficulty_level'] }}</p>
                @endif
                
                @if($recommendation['estimated_time'] && $recommendation['estimated_time'] !== 'N/A')
                    <p><strong>Temps estimé :</strong> {{ $recommendation['estimated_time'] }}</p>
                @endif
                
                @if($recommendation['url'])
                    <a href="{{ $recommendation['url'] }}" class="btn" target="_blank">
                        🔗 Consulter la ressource
                    </a>
                @endif
            </div>
            
            <p>Cette recommandation a été générée automatiquement en fonction du contenu et de la catégorie de votre article.</p>
            
            <p>Continuez à partager vos connaissances sur SmartHealth ! 🚀</p>
        </div>
        
        <div class="footer">
            <p>SmartHealth - Votre plateforme de santé intelligente</p>
            <p><small>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</small></p>
        </div>
    </div>
</body>
</html>