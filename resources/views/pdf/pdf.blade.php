<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>

    <link rel="stylesheet" href="css/pdf.css" type="text/css"> 
</head>
<body>
    <table class="w-full">
        <tr>
            <td class="w-half">
                <img src="img/Logo-full.png" alt="laravel daily" width="200" />
            </td>
            <td class="w-half">
                <h2>Invoice ID: 834847473</h2>
            </td>
        </tr>
    </table>
 
    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div><h4>To:</h4></div>
                    <div>John Doe</div>
                    <div>123 Acme Str.</div>
                </td>
                <td class="w-half">
                    <div><h4>From:</h4></div>
                    <div>Laravel Daily</div>
                    <div>London</div>
                </td>
            </tr>
        </table>
    </div>
 
    <div class="margin-top">
        <table class="products">
            <tr>
                <th>ID</th>
                <th>Preço</th>
                <th>Data</th>
            </tr>
            <tr class="items">
                @foreach($data as $item)
                <tr> <!-- Adicione a tag <tr> aqui para cada item -->
                    <td>
                        {{ $item->id }} <!-- Use o atributo id do modelo Order -->
                    </td>
                    <td>
                        R$ {{ number_format($item->grand_total, 2, ',', '.') }} <!-- Formate o preço corretamente -->
                    </td>
                    <td>
                        {{ $item->created_at->format('d/m/Y H:i') }} <!-- Formate a data -->
                    </td>
                </tr>
                @endforeach
            </tr>
        </table>
    </div>
 
    <div class="total">
        Valor Total: R$129.00
    </div>
 
    <div class="footer margin-top">
        <div>Thank you</div>
        <div>&copy; Laravel Daily</div>
    </div>
</body>
</html>