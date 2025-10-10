<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rappel - Évaluation psychologique</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; }
        .header { background: linear-gradient(135deg, #6f42c1, #007bff); padding: 30px 20px; text-align: center; }
        .header h1, .header p { color: #000000 !important; } /* 👈 Texte en noir */
        .content { padding: 30px 20px; }
        .button { display: inline-block; background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #6c757d; }
        .icon { font-size: 48px; margin-bottom: 20px; }
        a.button { color: #ffffff !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🧠</div>
            <h1>SmartHealth - Évaluation Psychologique</h1>
            <p>{{ $period === 'morning' ? 'Bonjour' : 'Bonsoir' }} {{ $user->name ?? 'Utilisateur' }} !</p>
        </div>
        
        <div class="content">
            <h2>Rappel d'évaluation - {{ ucfirst($dayName) }}</h2>
            
            <p>
                {{ $period === 'morning' 
                   ? "C'est le moment de commencer votre évaluation psychologique hebdomadaire." 
                   : "N'oubliez pas de compléter votre évaluation psychologique avant la fin de la journée." }}
            </p>
            
            <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #1976d2; margin-top: 0;">Cette évaluation comprend :</h3>
                <ul style="color: #424242; line-height: 1.6;">
                    <li><strong>PHQ-9</strong> - Évaluation de la dépression (9 questions)</li>
                    <li><strong>GAD-7</strong> - Évaluation de l'anxiété (7 questions)</li>
                </ul>
                <p style="color: #666; font-size: 14px; margin-bottom: 0;">
                    ⏱️ Durée estimée : 5-10 minutes
                </p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="http://127.0.0.1:8000/questionnaires" class="button">Commencer l'évaluation</a>
            </div>
            
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;">
                <p style="margin: 0; color: #856404; font-size: 14px;">
                    <strong>Rappel :</strong> Les questionnaires sont disponibles uniquement le lundi et vendredi.
                    Votre bien-être mental est important !
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} SmartHealth. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement dans le cadre de votre suivi psychologique.</p>
        </div>
    </div>
</body>
</html>
