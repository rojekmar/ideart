<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Nowe zapytanie ze strony</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1d1d1f; line-height: 1.6;">
    <h2 style="color: #b8860b;">Nowe zapytanie ze strony IDEART</h2>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td><strong>Imię</strong></td>
            <td>{{ $data['name'] }}</td>
        </tr>
        <tr>
            <td><strong>E-mail</strong></td>
            <td><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></td>
        </tr>
        @if(!empty($data['phone']))
            <tr>
                <td><strong>Telefon</strong></td>
                <td><a href="tel:{{ $data['phone'] }}">{{ $data['phone'] }}</a></td>
            </tr>
        @endif
        <tr>
            <td><strong>Rodzaj projektu</strong></td>
            <td>{{ $data['type'] }}</td>
        </tr>
    </table>

    <p><strong>Wiadomość:</strong></p>
    <p style="white-space: pre-wrap; background: #f5f5f7; padding: 12px; border-radius: 8px;">{{ $data['message'] }}</p>

    <p style="color: #86868b; font-size: 12px; margin-top: 24px;">
        Wiadomość wysłana z formularza kontaktowego na ideart.com.pl.
    </p>
</body>
</html>
