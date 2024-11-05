<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadPdfController extends Controller
{
    public function download(Request $request) {
        // Obtém os IDs dos pedidos a partir da solicitação
        $orderIds = $request->input('ids');
    
        // Busca todos os pedidos com os IDs fornecidos e ordena pela data mais antiga
        $orders = Order::whereIn('id', $orderIds)
            ->orderBy('created_at', 'desc') // Ordena pela data de criação de forma ascendente
            ->get();
    
        // Verifica se há pedidos para processar
        if ($orders->isEmpty()) {
            return redirect()->route('order.index')->with('error', 'Nenhum pedido encontrado.');
        }
    
        // Carrega a view para o PDF com os dados dos pedidos
        $pdf = Pdf::loadView('pdf/pdf', ['data' => $orders]);
    
        return $pdf->setPaper('a4')->stream('pedidos.pdf');
    }
    
}
